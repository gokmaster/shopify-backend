<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-neutral-900">{{ $product ? 'Edit product' : 'Add product' }}</h2>
            <p class="text-sm text-neutral-500">{{ $product ? 'Update details, pricing and images.' : 'Fill in the details below or fetch them automatically from a URL.' }}</p>
        </div>
        <a href="{{ route('products.index') }}" wire:navigate class="text-sm font-medium text-neutral-600 hover:text-neutral-900">← Back to products</a>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <label class="mb-1 block text-sm font-medium text-neutral-700">Fetch details from a product URL (optional)</label>
                <div class="flex gap-2">
                    <input type="url" wire:model="sourceUrl" placeholder="https://supplier.example.com/product/123" class="flex-1 rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="button" wire:click="fetchDetails" wire:loading.attr="disabled" wire:target="fetchDetails" class="shrink-0 rounded-lg border border-emerald-600 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50 disabled:opacity-50">
                        <span wire:loading.remove wire:target="fetchDetails">Fetch details</span>
                        <span wire:loading wire:target="fetchDetails">Fetching…</span>
                    </button>
                </div>
                @error('sourceUrl') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-neutral-400">Pulls the title, description and main image from that page's public preview tags.</p>
            </div>

            <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Title</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Description</label>
                    <textarea wire:model="description" rows="5" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">Category</label>
                        <select wire:model="categoryId" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Uncategorized</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">Vendor / Brand</label>
                        <input type="text" wire:model="vendor" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">SKU</label>
                        <input type="text" wire:model="sku" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">Barcode (UPC/EAN)</label>
                        <input type="text" wire:model="barcode" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
                <h3 class="font-medium text-neutral-900">Pricing &amp; inventory</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">Price</label>
                        <input type="number" step="0.01" min="0" wire:model="price" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">Compare-at price</label>
                        <input type="number" step="0.01" min="0" wire:model="compareAtPrice" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700">Quantity</label>
                        <input type="number" min="0" wire:model="quantity" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Status</label>
                    <select wire:model="status" class="w-full max-w-xs rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <h3 class="mb-3 font-medium text-neutral-900">Images</h3>

                <div class="grid grid-cols-3 gap-2">
                    @if ($product)
                        @foreach ($product->images as $image)
                            <div class="group relative aspect-square overflow-hidden rounded-lg bg-neutral-100">
                                <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover">
                                <button type="button" wire:click="removeExistingImage({{ $image->id }})" wire:confirm="Remove this image?" class="absolute right-1 top-1 hidden h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white group-hover:flex">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    @endif

                    @foreach ($newImages as $index => $upload)
                        <div class="group relative aspect-square overflow-hidden rounded-lg bg-neutral-100">
                            <img src="{{ $upload->temporaryUrl() }}" alt="" class="h-full w-full object-cover">
                            <button type="button" wire:click="removeNewImage({{ $index }})" class="absolute right-1 top-1 hidden h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white group-hover:flex">
                                ×
                            </button>
                        </div>
                    @endforeach

                    @foreach ($pendingImageUrls as $index => $pending)
                        <div class="group relative aspect-square overflow-hidden rounded-lg bg-neutral-100">
                            <img src="{{ $pending['url'] }}" alt="" class="h-full w-full object-cover">
                            <span class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] text-white">fetched</span>
                            <button type="button" wire:click="removePendingImageUrl({{ $index }})" class="absolute right-1 top-1 hidden h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white group-hover:flex">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>

                <label class="mt-3 flex cursor-pointer items-center justify-center rounded-lg border border-dashed border-neutral-300 px-4 py-6 text-sm text-neutral-500 hover:border-emerald-400 hover:text-emerald-700">
                    <span>Click to upload images</span>
                    <input type="file" wire:model="newImages" multiple accept="image/*" class="hidden">
                </label>
                <div wire:loading wire:target="newImages" class="mt-2 text-xs text-neutral-400">Uploading…</div>
                @error('newImages.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @if ($product?->isSyncedToShopify())
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    Synced to Shopify
                    @if ($product->shopify_synced_at)
                        · last synced {{ $product->shopify_synced_at->diffForHumans() }}
                    @endif
                </div>
            @elseif ($product?->shopify_sync_error)
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    Last sync failed: {{ $product->shopify_sync_error }}
                </div>
            @endif

            <div class="space-y-2 rounded-xl border border-neutral-200 bg-white p-5">
                <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700" wire:loading.attr="disabled" wire:target="save">
                    Save product
                </button>
                <button type="button" wire:click="save(true)" class="w-full rounded-lg border border-emerald-600 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50" wire:loading.attr="disabled" wire:target="save(true)">
                    Save &amp; sync to Shopify
                </button>
                <a href="{{ route('products.index') }}" wire:navigate class="block rounded-lg px-4 py-2 text-center text-sm font-medium text-neutral-600 hover:bg-neutral-100">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
