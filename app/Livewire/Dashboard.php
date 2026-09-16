<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['header' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render(): View
    {
        return view('livewire.dashboard', [
            'totalProducts' => Product::query()->count(),
            'activeProducts' => Product::query()->where('status', Product::STATUS_ACTIVE)->count(),
            'syncedProducts' => Product::query()->whereNotNull('shopify_product_id')->count(),
            'totalCategories' => Category::query()->count(),
            'recentProducts' => Product::query()->with('category')->latest()->take(6)->get(),
        ]);
    }
}
