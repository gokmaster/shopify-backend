<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['header' => 'Categories'])]
class Index extends Component
{
    use WithFileUploads;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public ?string $description = null;

    public $image = null;

    public ?string $existingImagePath = null;

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Category $category): void
    {
        $this->resetForm();
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->existingImagePath = $category->image_path;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:4096',
        ]);

        $category = $this->editingId ? Category::findOrFail($this->editingId) : new Category;
        $category->name = $data['name'];

        if (! $this->editingId) {
            $category->slug = Str::slug($data['name']);
        }

        $category->description = $data['description'];

        if ($this->image) {
            $category->image_path = $this->image->store('categories', 'public');
        }

        $category->save();

        session()->flash('success', $this->editingId ? 'Category updated.' : 'Category created.');

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            session()->flash('error', 'Cannot delete a category that still has products assigned to it.');

            return;
        }

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        session()->flash('success', 'Category deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'image', 'existingImagePath']);
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }
}
