<div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Total products</p>
            <p class="mt-2 text-3xl font-semibold text-neutral-900">{{ $totalProducts }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Active</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $activeProducts }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Synced to Shopify</p>
            <p class="mt-2 text-3xl font-semibold text-neutral-900">{{ $syncedProducts }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <p class="text-sm text-neutral-500">Categories</p>
            <p class="mt-2 text-3xl font-semibold text-neutral-900">{{ $totalCategories }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <a href="{{ route('products.create') }}" wire:navigate class="group rounded-xl border border-neutral-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-sm">
            <p class="font-medium text-neutral-900 group-hover:text-emerald-700">Add a product</p>
            <p class="mt-1 text-sm text-neutral-500">Create a single product manually with images and pricing.</p>
        </a>
        <a href="{{ route('products.import') }}" wire:navigate class="group rounded-xl border border-neutral-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-sm">
            <p class="font-medium text-neutral-900 group-hover:text-emerald-700">Bulk import via CSV</p>
            <p class="mt-1 text-sm text-neutral-500">Upload a spreadsheet to create or update many products at once.</p>
        </a>
        <a href="{{ route('settings.shopify') }}" wire:navigate class="group rounded-xl border border-neutral-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-sm">
            <p class="font-medium text-neutral-900 group-hover:text-emerald-700">Connect Shopify</p>
            <p class="mt-1 text-sm text-neutral-500">Add your store credentials so products can be pushed to Shopify.</p>
        </a>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white">
        <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
            <h2 class="font-medium text-neutral-900">Recently added products</h2>
            <a href="{{ route('products.index') }}" wire:navigate class="text-sm font-medium text-emerald-700 hover:text-emerald-800">View all</a>
        </div>

        @if ($recentProducts->isEmpty())
            <p class="px-5 py-8 text-center text-sm text-neutral-500">No products yet. Add your first one to get started.</p>
        @else
            <ul class="divide-y divide-neutral-100">
                @foreach ($recentProducts as $product)
                    <li class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-neutral-100">
                                @if ($image = $product->primaryImage())
                                    <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('products.edit', $product) }}" wire:navigate class="font-medium text-neutral-900 hover:text-emerald-700">{{ $product->title }}</a>
                                <p class="text-xs text-neutral-500">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium capitalize text-neutral-600">{{ $product->status }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
