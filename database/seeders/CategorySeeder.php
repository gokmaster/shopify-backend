<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Dairy & Eggs',
            'Meat & Seafood',
            'Fruits & Vegetables',
            'Bakery',
            'Beverages',
            'Pantry & Dry Goods',
            'Frozen Foods',
            'Snacks',
        ];

        foreach ($categories as $name) {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
