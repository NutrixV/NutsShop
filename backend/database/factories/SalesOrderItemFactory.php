<?php

namespace Database\Factories;

use App\Models\SalesOrderItem;
use App\Models\SalesOrder;
use App\Models\CatalogProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SalesOrderItemFactory
 * 
 * @extends Factory<SalesOrderItem>
 */
class SalesOrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = CatalogProduct::factory()->create();
        $qty = fake()->numberBetween(1, 5);
        
        return [
            'order_id' => SalesOrder::factory(),
            'product_id' => $product->entity_id,
            'sku' => $product->sku,
            'name' => $product->name,
            'qty_ordered' => $qty,
            'price' => $product->price,
            'row_total' => $product->price * $qty,
        ];
    }

    /**
     * Create an order item for a specific order
     *
     * @param int $orderId
     * @return $this
     */
    public function forOrder($orderId)
    {
        return $this->state(function (array $attributes) use ($orderId) {
            return [
                'order_id' => $orderId,
            ];
        });
    }

    /**
     * Create an order item for a specific product
     *
     * @param int $productId
     * @param float $price
     * @return $this
     */
    public function forProduct($productId, $price)
    {
        return $this->state(function (array $attributes) use ($productId, $price) {
            $qty = $attributes['qty_ordered'] ?? 1;
            
            return [
                'product_id' => $productId,
                'price' => $price,
                'row_total' => $price * $qty,
            ];
        });
    }
} 