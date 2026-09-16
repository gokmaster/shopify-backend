<?php

namespace App\Services\Shopify;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductPayloadMapper
{
    /**
     * Build a Shopify Admin API product payload from a local Product model.
     *
     * @return array<string, mixed>
     */
    public function map(Product $product): array
    {
        $category = $product->category;

        $tags = collect([$category?->name])->filter()->implode(', ');

        $payload = [
            'title' => $product->title,
            'body_html' => $product->description ?? '',
            'vendor' => $product->vendor ?: config('app.name'),
            'product_type' => $category?->name ?? '',
            'tags' => $tags,
            'status' => $product->status === Product::STATUS_ACTIVE ? 'active' : 'draft',
            'variants' => [$this->mapVariant($product)],
        ];

        $images = $this->mapImages($product);

        if ($images !== []) {
            $payload['images'] = $images;
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapVariant(Product $product): array
    {
        $variant = [
            'price' => (string) $product->price,
            'sku' => $product->sku ?? '',
            'barcode' => $product->barcode ?? '',
            'inventory_management' => 'shopify',
            'inventory_policy' => 'deny',
            'inventory_quantity' => $product->quantity,
        ];

        if (! blank($product->compare_at_price)) {
            $variant['compare_at_price'] = (string) $product->compare_at_price;
        }

        return $variant;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function mapImages(Product $product): array
    {
        $images = [];

        foreach ($product->images as $image) {
            if ($image->disk_path && Storage::disk('public')->exists($image->disk_path)) {
                $images[] = [
                    'attachment' => base64_encode(Storage::disk('public')->get($image->disk_path)),
                    'filename' => basename($image->disk_path),
                    'alt' => $image->alt_text ?? $product->title,
                ];

                continue;
            }

            if ($image->url) {
                $images[] = [
                    'src' => $image->url,
                    'alt' => $image->alt_text ?? $product->title,
                ];
            }
        }

        return $images;
    }
}
