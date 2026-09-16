<?php

namespace App\Services\Shopify;

use App\Models\Setting;

class ShopifyCredentials
{
    public static function store(): ?string
    {
        return Setting::get('shopify_store') ?: config('services.shopify.store');
    }

    public static function accessToken(): ?string
    {
        return Setting::get('shopify_access_token') ?: config('services.shopify.access_token');
    }

    public static function apiVersion(): string
    {
        return Setting::get('shopify_api_version') ?: config('services.shopify.api_version', '2024-10');
    }

    public static function configured(): bool
    {
        return ! blank(self::store()) && ! blank(self::accessToken());
    }

    /**
     * Normalize the store setting (accepts "my-shop" or "my-shop.myshopify.com") into a full domain.
     */
    public static function domain(): ?string
    {
        $store = self::store();

        if (blank($store)) {
            return null;
        }

        $store = trim(str_replace(['https://', 'http://'], '', $store), '/');

        return str_contains($store, '.') ? $store : "{$store}.myshopify.com";
    }
}
