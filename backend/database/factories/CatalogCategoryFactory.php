<?php

namespace Database\Factories;

use App\Models\CatalogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CatalogCategoryFactory
 * 
 * @extends Factory<CatalogCategory>
 */
class CatalogCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatalogCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'url_key' => fake()->unique()->slug(3),
            'is_active' => true,
            'position' => fake()->numberBetween(0, 100),
            'parent_id' => null,
        ];
    }

    /**
     * Indicates that the category is a child category.
     *
     * @param int $parentId
     * @return Factory
     */
    public function child(int $parentId): Factory
    {
        return $this->state(function (array $attributes) use ($parentId) {
            return [
                'parent_id' => $parentId,
            ];
        });
    }
} 