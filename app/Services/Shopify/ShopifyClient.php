<?php

namespace App\Services\Shopify;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ShopifyClient
{
    protected function request(): PendingRequest
    {
        if (! ShopifyCredentials::configured()) {
            throw new ShopifyApiException('Shopify is not configured yet. Add your store domain and access token in Settings.');
        }

        return Http::baseUrl($this->baseUrl())
            ->withHeaders([
                'X-Shopify-Access-Token' => ShopifyCredentials::accessToken(),
                'Content-Type' => 'application/json',
            ])
            ->acceptJson();
    }

    protected function baseUrl(): string
    {
        return sprintf(
            'https://%s/admin/api/%s',
            ShopifyCredentials::domain(),
            ShopifyCredentials::apiVersion(),
        );
    }

    public function testConnection(): array
    {
        $response = $this->request()->get('/shop.json');

        $this->throwIfFailed($response);

        return $response->json('shop', []);
    }

    public function createProduct(array $payload): array
    {
        $response = $this->request()->post('/products.json', ['product' => $payload]);

        $this->throwIfFailed($response);

        return $response->json('product', []);
    }

    public function updateProduct(string $shopifyProductId, array $payload): array
    {
        $response = $this->request()->put("/products/{$shopifyProductId}.json", ['product' => $payload]);

        $this->throwIfFailed($response);

        return $response->json('product', []);
    }

    public function deleteProduct(string $shopifyProductId): void
    {
        $response = $this->request()->delete("/products/{$shopifyProductId}.json");

        if ($response->status() === 404) {
            return;
        }

        $this->throwIfFailed($response);
    }

    protected function throwIfFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        $errors = $response->json('errors');
        $message = match (true) {
            is_string($errors) => $errors,
            is_array($errors) => collect($errors)->flatten()->implode(' '),
            default => "Shopify API request failed with status {$response->status()}.",
        };

        throw new ShopifyApiException($message ?: "Shopify API request failed with status {$response->status()}.");
    }
}
