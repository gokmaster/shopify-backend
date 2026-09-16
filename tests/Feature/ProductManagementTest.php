<?php

namespace Tests\Feature;

use App\Livewire\Products\Form;
use App\Livewire\Products\Index;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_product(): void
    {
        $category = Category::factory()->create();

        Livewire::test(Form::class)
            ->set('title', 'Fresh Avocado')
            ->set('categoryId', $category->id)
            ->set('price', '2.50')
            ->set('quantity', 10)
            ->call('save')
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'title' => 'Fresh Avocado',
            'category_id' => $category->id,
            'price' => 2.50,
        ]);
    }

    public function test_title_and_price_are_required(): void
    {
        Livewire::test(Form::class)
            ->set('title', '')
            ->set('price', '')
            ->call('save')
            ->assertHasErrors(['title', 'price']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_can_update_an_existing_product(): void
    {
        $product = Product::factory()->create(['title' => 'Old Title']);

        Livewire::test(Form::class, ['product' => $product])
            ->set('title', 'New Title')
            ->call('save')
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'New Title',
        ]);
    }

    public function test_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        Livewire::test(Index::class)->call('delete', $product);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_index_can_filter_by_search_term(): void
    {
        Product::factory()->create(['title' => 'Cheddar Cheese']);
        Product::factory()->create(['title' => 'Orange Juice']);

        Livewire::test(Index::class)
            ->set('search', 'Cheddar')
            ->assertSee('Cheddar Cheese')
            ->assertDontSee('Orange Juice');
    }
}
