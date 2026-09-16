<?php

namespace App\Services\Shopify;

use App\Models\Product;
use Illuminate\Support\Arr;

class ShopifyProductSyncService
{
    public function __construct(
        protected ShopifyClient $client,
        protected ProductPayloadMapper $mapper,
    ) {}

    /**
     * Push a local product to Shopify, creating or updating it as needed.
     *
     * @throws ShopifyApiException
     */
    public function push(Product $product): Product
    {
        $product->loadMissing(['category', 'images']);

        $payload = $this->mapper->map($product);

        try {
            $response = $product->isSyncedToShopify()
                ? $this->client->updateProduct($product->shopify_product_id, $payload)
                : $this->client->createProduct($payload);
        } catch (ShopifyApiException $exception) {
            $product->forceFill(['shopify_sync_error' => $exception->getMessage()])->save();

            throw $exception;
        }

        $product->forceFill([
            'shopify_product_id' => (string) Arr::get($response, 'id', $product->shopify_product_id),
            'shopify_synced_at' => now(),
            'shopify_sync_error' => null,
        ])->save();

        $this->syncImageIds($product, Arr::get($response, 'images', []));

        return $product->fresh(['category', 'images']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $shopifyImages
     */
    protected function syncImageIds(Product $product, array $shopifyImages): void
    {
        $localImages = $product->images;

        foreach ($shopifyImages as $index => $shopifyImage) {
            $localImage = $localImages->get($index);

            if ($localImage && $id = Arr::get($shopifyImage, 'id')) {
                $localImage->forceFill(['shopify_image_id' => (string) $id])->save();
            }
        }
    }
}
