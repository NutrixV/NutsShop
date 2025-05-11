<?php

namespace Tests\Unit\Models;

use App\Models\Quote;
use App\Models\CustomerEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class QuoteTest
 * 
 * Тести для моделі Quote (кошик)
 */
class QuoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест створення порожнього кошика
     */
    public function test_can_create_empty_quote()
    {
        $quoteData = [
            'subtotal' => 0.00,
            'grand_total' => 0.00,
            'items_count' => 0,
            'items_qty' => 0,
            'currency' => 'USD',
        ];

        $quote = Quote::create($quoteData);

        $this->assertInstanceOf(Quote::class, $quote);
        $this->assertEquals(0.00, $quote->subtotal);
        $this->assertEquals(0.00, $quote->grand_total);
        $this->assertEquals(0, $quote->items_count);
        $this->assertEquals(0, $quote->items_qty);
        $this->assertEquals('USD', $quote->currency);
        $this->assertNotNull($quote->entity_id);
    }

    /**
     * Тест створення кошика з прив'язкою до клієнта
     */
    public function test_can_create_quote_with_customer()
    {
        $customer = CustomerEntity::factory()->create();
        
        $quote = Quote::create([
            'customer_id' => $customer->entity_id,
            'subtotal' => 0.00,
            'grand_total' => 0.00,
            'items_count' => 0,
            'items_qty' => 0,
            'currency' => 'USD',
        ]);

        $this->assertEquals($customer->entity_id, $quote->customer_id);
    }
} 