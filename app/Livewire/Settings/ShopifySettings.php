<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use App\Services\Shopify\ShopifyApiException;
use App\Services\Shopify\ShopifyClient;
use App\Services\Shopify\ShopifyCredentials;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['header' => 'Shopify Settings'])]
class ShopifySettings extends Component
{
    public string $store = '';

    public string $accessToken = '';

    public string $apiVersion = '2024-10';

    public ?string $connectionStatus = null;

    public bool $connectionOk = false;

    public function mount(): void
    {
        $this->store = ShopifyCredentials::store() ?? '';
        $this->accessToken = ShopifyCredentials::accessToken() ?? '';
        $this->apiVersion = ShopifyCredentials::apiVersion();
    }

    public function save(): void
    {
        $data = $this->validate([
            'store' => 'required|string|max:255',
            'accessToken' => 'required|string|max:255',
            'apiVersion' => 'required|string|max:20',
        ]);

        Setting::set('shopify_store', $data['store']);
        Setting::set('shopify_access_token', $data['accessToken']);
        Setting::set('shopify_api_version', $data['apiVersion']);

        session()->flash('success', 'Shopify settings saved.');
    }

    public function testConnection(ShopifyClient $client): void
    {
        $this->save();

        try {
            $shop = $client->testConnection();
            $this->connectionOk = true;
            $this->connectionStatus = 'Connected to '.($shop['name'] ?? $shop['myshopify_domain'] ?? 'your Shopify store').'.';
        } catch (ShopifyApiException $exception) {
            $this->connectionOk = false;
            $this->connectionStatus = $exception->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.settings.shopify-settings');
    }
}
