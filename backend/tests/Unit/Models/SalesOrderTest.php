<?php

namespace Tests\Unit\Models;

use App\Models\SalesOrder;
use App\Models\CustomerEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SalesOrderTest
 * 
 * Тести для моделі SalesOrder (замовлення)
 */
class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест створення замовлення
     */
    public function test_can_create_order()
    {
        $customer = CustomerEntity::factory()->create();
        
        $orderData = [
            'customer_id' => $customer->entity_id,
            'increment_id' => 'ORDER-10001',
            'status' => 'pending',
            'subtotal' => 100.00,
            'grand_total' => 100.00,
            'currency' => 'USD',
        ];

        $order = SalesOrder::create($orderData);

        $this->assertInstanceOf(SalesOrder::class, $order);
        $this->assertEquals($customer->entity_id, $order->customer_id);
        $this->assertEquals('ORDER-10001', $order->increment_id);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(100.00, $order->subtotal);
        $this->assertEquals(100.00, $order->grand_total);
        $this->assertEquals('USD', $order->currency);
        $this->assertNotNull($order->entity_id);
    }
} 