<?php

namespace Database\Factories;

use App\Models\QuoteItem;
use App\Models\Quote;
use App\Models\CatalogProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class QuoteItemFactory
 * 
 * @extends Factory<QuoteItem>
 */
class QuoteItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuoteItem::class;

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
            'quote_id' => Quote::factory(),
            'product_id' => $product->entity_id,
            'sku' => $product->sku,
            'name' => $product->name,
            'qty' => $qty,
            'price' => $product->price,
            'row_total' => $product->price * $qty,
        ];
    }

    /**
     * Create a quote item for a specific quote
     *
     * @param int $quoteId
     * @return $this
     */
    public function forQuote($quoteId)
    {
        return $this->state(function (array $attributes) use ($quoteId) {
            return [
                'quote_id' => $quoteId,
            ];
        });
    }

    /**
     * Create a quote item for a specific product
     *
     * @param int $productId
     * @param float $price
     * @return $this
     */
    public function forProduct($productId, $price)
    {
        return $this->state(function (array $attributes) use ($productId, $price) {
            $qty = $attributes['qty'] ?? 1;
            
            return [
                'product_id' => $productId,
                'price' => $price,
                'row_total' => $price * $qty,
            ];
        });
    }
} 