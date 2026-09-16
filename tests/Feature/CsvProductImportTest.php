<?php

namespace Tests\Feature;

use App\Livewire\Products\CsvImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Livewire\Livewire;
use Tests\TestCase;

class CsvProductImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_products_and_categories_from_csv(): void
    {
        $csv = "title,price,category,sku\nWhole Milk,3.49,Dairy,MILK-001\nBananas,1.99,Produce,PROD-002\n";

        $file = File::createWithContent('products.csv', $csv);

        Livewire::test(CsvImport::class)
            ->set('file', $file)
            ->call('import');

        $this->assertDatabaseHas('products', ['title' => 'Whole Milk', 'sku' => 'MILK-001']);
        $this->assertDatabaseHas('products', ['title' => 'Bananas', 'sku' => 'PROD-002']);
        $this->assertDatabaseHas('categories', ['name' => 'Dairy']);
        $this->assertDatabaseHas('categories', ['name' => 'Produce']);
    }

    public function test_reimporting_the_same_sku_updates_instead_of_duplicating(): void
    {
        $original = "title,price,sku\nWhole Milk,3.49,MILK-001\n";
        Livewire::test(CsvImport::class)
            ->set('file', File::createWithContent('products.csv', $original))
            ->call('import');

        $updated = "title,price,sku\nWhole Milk,4.99,MILK-001\n";
        Livewire::test(CsvImport::class)
            ->set('file', File::createWithContent('products.csv', $updated))
            ->call('import');

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['sku' => 'MILK-001', 'price' => 4.99]);
    }

    public function test_rows_missing_required_fields_are_reported_as_errors(): void
    {
        $csv = "title,price\n,9.99\nValid Product,\n";

        $component = Livewire::test(CsvImport::class)
            ->set('file', File::createWithContent('products.csv', $csv))
            ->call('import');

        $this->assertSame(2, $component->get('result')['skipped']);
        $this->assertDatabaseCount('products', 0);
    }
}
