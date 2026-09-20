<div class="max-w-2xl space-y-6">
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h2 class="font-medium text-neutral-900">Connect your Shopify store</h2>
        <p class="mt-1 text-sm text-neutral-500">
            Create a custom app in your Shopify admin (Settings → Apps and sales channels → Develop apps) and grant it
            <code class="rounded bg-neutral-100 px-1 py-0.5">write_products</code> access, then paste the Admin API access token below.
        </p>

        <a href="{{ asset('docs/shopify-api-token-guide.pdf') }}" download class="mt-3 inline-flex items-center gap-2 rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3v12" />
                <path d="m7 10 5 5 5-5" />
                <path d="M5 21h14" />
            </svg>
            Download API token guide (PDF)
        </a>

        <form wire:submit="save" class="mt-5 space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">Store domain</label>
                <input type="text" wire:model="store" placeholder="your-store or your-store.myshopify.com" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('store') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">Admin API access token</label>
                <input type="password" wire:model="accessToken" placeholder="shpat_..." class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('accessToken') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">API version</label>
                <input type="text" wire:model="apiVersion" placeholder="2024-10" class="w-full max-w-xs rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('apiVersion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    Save settings
                </button>
                <button type="button" wire:click="testConnection" wire:loading.attr="disabled" wire:target="testConnection" class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 disabled:opacity-50">
                    <span wire:loading.remove wire:target="testConnection">Save &amp; test connection</span>
                    <span wire:loading wire:target="testConnection">Testing…</span>
                </button>
            </div>
        </form>

        @if ($connectionStatus)
            <div @class([
                'mt-4 rounded-lg px-4 py-3 text-sm',
                'bg-emerald-50 text-emerald-800' => $connectionOk,
                'bg-red-50 text-red-800' => ! $connectionOk,
            ])>
                {{ $connectionStatus }}
            </div>
        @endif
    </div>
</div>
