<?php

namespace Database\Factories;

use App\Models\CustomerEntity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerEntity>
 */
class CustomerEntityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerEntity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => Hash::make('password'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'api_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Set the user's API token.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withApiToken(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'api_token' => Str::random(80),
            ];
        });
    }
} 