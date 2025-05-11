<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\CustomerEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class QuoteFactory
 * 
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => null,
            'items_count' => 0,
            'subtotal' => 0.00,
            'grand_total' => 0.00,
            'currency' => 'USD',
        ];
    }

    /**
     * Indicate that the quote belongs to a customer
     *
     * @return $this
     */
    public function forCustomer()
    {
        return $this->state(function (array $attributes) {
            return [
                'customer_id' => CustomerEntity::factory(),
            ];
        });
    }

    /**
     * Create a quote with a specific customer
     *
     * @param int $customerId
     * @return $this
     */
    public function withCustomer($customerId)
    {
        return $this->state(function (array $attributes) use ($customerId) {
            return [
                'customer_id' => $customerId,
            ];
        });
    }
} 