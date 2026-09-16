<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search by title or SKU..."
                class="w-full max-w-xs rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            >

            <select wire:model.live="categoryId" class="rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="status" class="rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <a href="{{ route('products.create') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            + Add Product
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-neutral-100 bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">Price</th>
                    <th class="px-5 py-3">Stock</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Shopify</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->id }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-neutral-100">
                                    @if ($image = $product->primaryImage())
                                        <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('products.edit', $product) }}" wire:navigate class="font-medium text-neutral-900 hover:text-emerald-700">{{ $product->title }}</a>
                                    @if ($product->sku)
                                        <p class="text-xs text-neutral-500">SKU: {{ $product->sku }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-neutral-600">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">${{ number_format((float) $product->price, 2) }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $product->quantity }}</td>
                        <td class="px-5 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-1 text-xs font-medium capitalize',
                                'bg-emerald-100 text-emerald-700' => $product->status === 'active',
                                'bg-neutral-100 text-neutral-600' => $product->status !== 'active',
                            ])>{{ $product->status }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($product->isSyncedToShopify())
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Synced
                                </span>
                            @else
                                <span class="text-xs text-neutral-400">Not synced</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-3 text-sm">
                                <button
                                    type="button"
                                    wire:click="sync({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="sync({{ $product->id }})"
                                    class="font-medium text-emerald-700 hover:text-emerald-800 disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="sync({{ $product->id }})">{{ $product->isSyncedToShopify() ? 'Re-sync' : 'Sync' }}</span>
                                    <span wire:loading wire:target="sync({{ $product->id }})">Syncing…</span>
                                </button>
                                <a href="{{ route('products.edit', $product) }}" wire:navigate class="font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                                <button type="button" wire:click="delete({{ $product->id }})" wire:confirm="Delete this product?" class="font-medium text-red-600 hover:text-red-700">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-neutral-500">
                            No products found. <a href="{{ route('products.create') }}" wire:navigate class="text-emerald-700 hover:underline">Add your first product</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
