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

        <a href="{{ asset('docs/shopify-sample-products.csv') }}" download class="mt-3 inline-flex items-center gap-2 rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3v12" />
                <path d="m7 10 5 5 5-5" />
                <path d="M5 21h14" />
            </svg>
            Download sample CSV
        </a>

        <form wire:submit="import" class="mt-4 space-y-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">CSV file</label>
                <div x-data="{ fileName: null }" class="flex flex-wrap items-center gap-3">
                    <label for="csv-file" class="cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                        Choose file
                    </label>
                    <span class="text-sm text-neutral-500" x-text="fileName ?? 'No file chosen'"></span>
                    <input id="csv-file" type="file" wire:model="file" accept=".csv,.txt" class="sr-only" x-on:change="fileName = $event.target.files[0]?.name ?? null">
                </div>
                <p class="mt-1 text-xs text-neutral-500">CSV or TXT, up to 10MB.</p>
                <div wire:loading wire:target="file" class="mt-1 text-xs text-neutral-500">Uploading…</div>
                @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

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
