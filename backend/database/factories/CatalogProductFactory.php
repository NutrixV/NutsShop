<?php

namespace Database\Factories;

use App\Models\CatalogProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CatalogProductFactory
 * 
 * @extends Factory<CatalogProduct>
 */
class CatalogProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatalogProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => 'PRD-' . fake()->unique()->numerify('######'),
            'price' => fake()->randomFloat(2, 10, 1000),
            'description' => fake()->paragraph(),
            'is_in_stock' => true,
            'qty' => fake()->numberBetween(1, 1000),
            'base_currency' => 'USD',
            'status' => 1, // Enabled
            'visibility' => 4, // Catalog & Search
            'nut_type' => fake()->randomElement(['almond', 'cashew', 'pecan', 'walnut', 'hazelnut']),
            'organic' => fake()->boolean(),
            'roasted' => fake()->boolean(),
        ];
    }
} 