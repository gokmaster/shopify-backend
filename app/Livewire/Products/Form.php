<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\AutoFetch\ProductAutoFetchException;
use App\Services\AutoFetch\ProductAutoFetchService;
use App\Services\Shopify\ShopifyApiException;
use App\Services\Shopify\ShopifyCredentials;
use App\Services\Shopify\ShopifyProductSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['header' => 'Product'])]
class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $title = '';

    public ?int $categoryId = null;

    public ?string $description = null;

    public ?string $vendor = null;

    public ?string $sku = null;

    public ?string $barcode = null;

    public string $price = '';

    public ?string $compareAtPrice = null;

    public int $quantity = 0;

    public string $status = Product::STATUS_DRAFT;

    public ?string $sourceUrl = null;

    /** @var array<int, mixed> */
    public array $newImages = [];

    /** @var array<int, array{url: string}> */
    public array $pendingImageUrls = [];

    public bool $isFetching = false;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product->load('images');
            $this->title = $product->title;
            $this->categoryId = $product->category_id;
            $this->description = $product->description;
            $this->vendor = $product->vendor;
            $this->sku = $product->sku;
            $this->barcode = $product->barcode;
            $this->price = (string) $product->price;
            $this->compareAtPrice = $product->compare_at_price !== null ? (string) $product->compare_at_price : null;
            $this->quantity = $product->quantity;
            $this->status = $product->status;
            $this->sourceUrl = $product->source_url;
        }
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'categoryId' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compareAtPrice' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|in:draft,active,archived',
            'sourceUrl' => 'nullable|url|max:2048',
            'newImages.*' => 'nullable|image|max:4096',
        ];
    }

    public function fetchDetails(ProductAutoFetchService $service): void
    {
        $this->validateOnly('sourceUrl', ['sourceUrl' => 'required|url|max:2048']);

        $this->isFetching = true;

        try {
            $details = $service->fetch($this->sourceUrl);

            if (! blank($details->title)) {
                $this->title = $details->title;
            }

            if (! blank($details->description)) {
                $this->description = $details->description;
            }

            if (! blank($details->imageUrl)) {
                $this->pendingImageUrls[] = ['url' => $details->imageUrl];
            }

            session()->flash('success', 'Fetched product details from the URL. Review and save.');
        } catch (ProductAutoFetchException $exception) {
            session()->flash('error', $exception->getMessage());
        } finally {
            $this->isFetching = false;
        }
    }

    public function removePendingImageUrl(int $index): void
    {
        unset($this->pendingImageUrls[$index]);
        $this->pendingImageUrls = array_values($this->pendingImageUrls);
    }

    public function removeNewImage(int $index): void
    {
        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    public function removeExistingImage(ProductImage $image): void
    {
        if ($image->disk_path) {
            Storage::disk('public')->delete($image->disk_path);
        }

        $image->delete();

        $this->product?->refresh();
        $this->product?->load('images');
    }

    public function save(bool $syncToShopify = false): void
    {
        $data = $this->validate();

        $product = $this->product ?? new Product;
        $product->title = $data['title'];
        $product->category_id = $data['categoryId'];
        $product->description = $data['description'];
        $product->vendor = $data['vendor'];
        $product->sku = $data['sku'];
        $product->barcode = $data['barcode'];
        $product->price = $data['price'];
        $product->compare_at_price = $data['compareAtPrice'];
        $product->quantity = $data['quantity'];
        $product->status = $data['status'];
        $product->source_url = $data['sourceUrl'];

        if (! $product->exists) {
            $product->slug = Product::uniqueSlug($data['title']);
        }

        $product->save();

        $this->persistNewImages($product);

        $this->product = $product;

        if ($syncToShopify) {
            $this->syncToShopify($product);

            return;
        }

        session()->flash('success', "\"{$product->title}\" was saved.");
        $this->redirectRoute('products.index', navigate: true);
    }

    protected function persistNewImages(Product $product): void
    {
        $position = $product->images()->count();

        foreach ($this->newImages as $upload) {
            ProductImage::create([
                'product_id' => $product->id,
                'disk_path' => $upload->store('products', 'public'),
                'alt_text' => $product->title,
                'position' => $position++,
            ]);
        }

        foreach ($this->pendingImageUrls as $pending) {
            ProductImage::create([
                'product_id' => $product->id,
                'url' => $pending['url'],
                'alt_text' => $product->title,
                'position' => $position++,
            ]);
        }

        $this->newImages = [];
        $this->pendingImageUrls = [];
    }

    protected function syncToShopify(Product $product): void
    {
        if (! ShopifyCredentials::configured()) {
            session()->flash('error', 'Product saved, but Shopify is not connected yet. Add your credentials in Settings.');
            $this->redirectRoute('products.edit', ['product' => $product], navigate: true);

            return;
        }

        try {
            app(ShopifyProductSyncService::class)->push($product);
            session()->flash('success', "\"{$product->title}\" was saved and synced to Shopify.");
        } catch (ShopifyApiException $exception) {
            session()->flash('error', "Product saved, but the Shopify sync failed: {$exception->getMessage()}");
        }

        $this->redirectRoute('products.edit', ['product' => $product], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.products.form', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
