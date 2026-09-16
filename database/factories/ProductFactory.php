<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'title' => ucfirst($title),
            'description' => $this->faker->paragraph(),
            'vendor' => $this->faker->company(),
            'sku' => strtoupper($this->faker->bothify('SKU-####??')),
            'price' => $this->faker->randomFloat(2, 1, 200),
            'quantity' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement([Product::STATUS_DRAFT, Product::STATUS_ACTIVE]),
        ];
    }

    public function synced(): static
    {
        return $this->state(fn (array $attributes): array => [
            'shopify_product_id' => (string) $this->faker->unique()->numberBetween(1000000, 9999999),
            'shopify_synced_at' => now(),
        ]);
    }
}
