<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Services\Shopify\ShopifyApiException;
use App\Services\Shopify\ShopifyCredentials;
use App\Services\Shopify\ShopifyProductSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['header' => 'Products'])]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $categoryId = '';

    #[Url(history: true)]
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function delete(Product $product): void
    {
        foreach ($product->images as $image) {
            if ($image->disk_path) {
                Storage::disk('public')->delete($image->disk_path);
            }
        }

        $product->delete();

        session()->flash('success', "\"{$product->title}\" was deleted.");
    }

    public function sync(Product $product, ShopifyProductSyncService $syncService): void
    {
        if (! ShopifyCredentials::configured()) {
            session()->flash('error', 'Connect your Shopify store in Settings before syncing products.');

            return;
        }

        try {
            $syncService->push($product);
            session()->flash('success', "\"{$product->title}\" was synced to Shopify.");
        } catch (ShopifyApiException $exception) {
            session()->flash('error', "Could not sync \"{$product->title}\": {$exception->getMessage()}");
        }
    }

    public function render(): View
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->when($this->search, fn ($query) => $query->where(function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->when($this->categoryId, fn ($query) => $query->where('category_id', $this->categoryId))
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
