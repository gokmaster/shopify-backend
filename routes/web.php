<?php

use App\Livewire\Categories;
use App\Livewire\Dashboard;
use App\Livewire\Products;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');

Route::get('/products', Products\Index::class)->name('products.index');
Route::get('/products/import', Products\CsvImport::class)->name('products.import');
Route::get('/products/create', Products\Form::class)->name('products.create');
Route::get('/products/{product}/edit', Products\Form::class)->name('products.edit');

Route::get('/categories', Categories\Index::class)->name('categories.index');

Route::get('/settings/shopify', Settings\ShopifySettings::class)->name('settings.shopify');
