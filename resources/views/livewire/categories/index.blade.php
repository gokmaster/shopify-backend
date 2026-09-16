<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-neutral-500">Organize products into departments like Dairy, Meat, or Fruits &amp; Vegetables.</p>
        @unless ($showForm)
            <button type="button" wire:click="create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                + Add Category
            </button>
        @endunless
    </div>

    @if ($showForm)
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <h2 class="mb-4 font-medium text-neutral-900">{{ $editingId ? 'Edit category' : 'New category' }}</h2>

            <form wire:submit="save" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Name</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Dairy">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-1">
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Image (optional)</label>
                    <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-neutral-600">
                    @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-16 w-16 rounded-lg object-cover">
                    @elseif ($existingImagePath)
                        <img src="{{ asset('storage/'.$existingImagePath) }}" class="mt-2 h-16 w-16 rounded-lg object-cover">
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Description (optional)</label>
                    <textarea wire:model="description" rows="2" class="w-full rounded-lg border border-neutral-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex items-center gap-3 sm:col-span-2">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700" wire:loading.attr="disabled" wire:target="save">
                        Save category
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-lg px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($categories as $category)
            <div class="rounded-xl border border-neutral-200 bg-white p-4">
                <div class="mb-3 flex h-28 items-center justify-center overflow-hidden rounded-lg bg-neutral-100">
                    @if ($category->imageUrl())
                        <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-3xl">🛒</span>
                    @endif
                </div>

                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium text-neutral-900">{{ $category->name }}</p>
                        <p class="text-xs text-neutral-500">{{ $category->products_count }} product{{ $category->products_count === 1 ? '' : 's' }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center gap-3 text-sm">
                    <button type="button" wire:click="edit({{ $category->id }})" class="font-medium text-emerald-700 hover:text-emerald-800">Edit</button>
                    <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Delete this category?" class="font-medium text-red-600 hover:text-red-700">Delete</button>
                </div>
            </div>
        @empty
            <p class="col-span-full py-8 text-center text-sm text-neutral-500">No categories yet.</p>
        @endforelse
    </div>
</div>
