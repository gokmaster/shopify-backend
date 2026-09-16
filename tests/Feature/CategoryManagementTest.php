<?php

namespace Tests\Feature;

use App\Livewire\Categories\Index;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_category(): void
    {
        Livewire::test(Index::class)
            ->call('create')
            ->set('name', 'Dairy')
            ->call('save');

        $this->assertDatabaseHas('categories', [
            'name' => 'Dairy',
            'slug' => 'dairy',
        ]);
    }

    public function test_can_delete_an_empty_category(): void
    {
        $category = Category::factory()->create();

        Livewire::test(Index::class)->call('delete', $category);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_a_category_that_has_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        Livewire::test(Index::class)->call('delete', $category);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
