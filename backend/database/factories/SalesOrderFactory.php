<?php

namespace Database\Factories;

use App\Models\SalesOrder;
use App\Models\CustomerEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SalesOrderFactory
 * 
 * @extends Factory<SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderNumber = 'ORDER-' . fake()->unique()->numerify('#####');
        
        return [
            'customer_id' => CustomerEntity::factory(),
            'increment_id' => $orderNumber,
            'status' => 'pending',
            'subtotal' => fake()->randomFloat(2, 10, 500),
            'grand_total' => fake()->randomFloat(2, 10, 500),
            'currency' => 'USD',
            'payment_method' => 'credit_card',
            'shipping_address' => json_encode([
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'postcode' => fake()->postcode(),
                'country' => fake()->countryCode(),
            ]),
        ];
    }

    /**
     * Set order status to completed.
     *
     * @return $this
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    /**
     * Create an order for a specific customer
     *
     * @param int $customerId
     * @return $this
     */
    public function forCustomer($customerId)
    {
        return $this->state(function (array $attributes) use ($customerId) {
            return [
                'customer_id' => $customerId,
            ];
        });
    }
} 