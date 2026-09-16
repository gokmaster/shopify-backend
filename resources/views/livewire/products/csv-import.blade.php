<div class="space-y-6">
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h2 class="font-medium text-neutral-900">Bulk import products from CSV</h2>
        <p class="mt-1 text-sm text-neutral-500">
            Your file needs a header row. At minimum include <code class="rounded bg-neutral-100 px-1 py-0.5">title</code> and <code class="rounded bg-neutral-100 px-1 py-0.5">price</code> columns.
            Optional columns: <code class="rounded bg-neutral-100 px-1 py-0.5">description</code>, <code class="rounded bg-neutral-100 px-1 py-0.5">category</code>,
            <code class="rounded bg-neutral-100 px-1 py-0.5">sku</code>, <code class="rounded bg-neutral-100 px-1 py-0.5">barcode</code>, <code class="rounded bg-neutral-100 px-1 py-0.5">quantity</code>,
            <code class="rounded bg-neutral-100 px-1 py-0.5">vendor</code>, <code class="rounded bg-neutral-100 px-1 py-0.5">compare_at_price</code>, <code class="rounded bg-neutral-100 px-1 py-0.5">image_url</code>.
        </p>
        <p class="mt-2 text-sm text-neutral-500">
            Rows are matched to existing products by SKU (or title if no SKU is given), so re-uploading the same file safely updates rather than duplicates.
        </p>

        <form wire:submit="import" class="mt-4 space-y-3">
            <input type="file" wire:model="file" accept=".csv,.txt" class="block w-full text-sm text-neutral-600">
            @error('file') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50" wire:loading.attr="disabled" wire:target="import">
                <span wire:loading.remove wire:target="import">Import CSV</span>
                <span wire:loading wire:target="import">Importing…</span>
            </button>
        </form>
    </div>

    @if ($result)
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="font-medium text-neutral-900">Import summary</h3>
            <div class="mt-3 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-lg bg-emerald-50 p-3">
                    <p class="text-2xl font-semibold text-emerald-700">{{ $result['created'] }}</p>
                    <p class="text-xs text-emerald-700">Created</p>
                </div>
                <div class="rounded-lg bg-sky-50 p-3">
                    <p class="text-2xl font-semibold text-sky-700">{{ $result['updated'] }}</p>
                    <p class="text-xs text-sky-700">Updated</p>
                </div>
                <div class="rounded-lg bg-red-50 p-3">
                    <p class="text-2xl font-semibold text-red-700">{{ $result['skipped'] }}</p>
                    <p class="text-xs text-red-700">Skipped</p>
                </div>
            </div>

            @if (! empty($result['errors']))
                <div class="mt-4">
                    <p class="mb-2 text-sm font-medium text-neutral-700">Rows with issues</p>
                    <ul class="space-y-1 text-sm text-red-700">
                        @foreach ($result['errors'] as $error)
                            <li>Row {{ $error['row'] }}: {{ $error['message'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('products.index') }}" wire:navigate class="mt-4 inline-block text-sm font-medium text-emerald-700 hover:underline">View products →</a>
        </div>
    @endif
</div>
