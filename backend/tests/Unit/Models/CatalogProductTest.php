<?php

namespace Tests\Unit\Models;

use App\Models\CatalogProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CatalogProductTest
 * 
 * Тести для моделі CatalogProduct
 */
class CatalogProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест створення продукту з базовими атрибутами
     */
    public function test_can_create_product()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TST-PRD-001',
            'price' => 10.99,
            'description' => 'Test product description',
            'is_in_stock' => true,
            'qty' => 100,
            'base_currency' => 'USD',
            'status' => 1,
        ];

        $product = CatalogProduct::create($productData);

        $this->assertInstanceOf(CatalogProduct::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('TST-PRD-001', $product->sku);
        $this->assertEquals(10.99, $product->price);
        $this->assertTrue($product->is_in_stock);
        $this->assertEquals(100, $product->qty);
        $this->assertNotNull($product->entity_id);
    }

    /**
     * Тест створення продукту через фабрику
     */
    public function test_can_create_product_using_factory()
    {
        $product = CatalogProduct::factory()->create();
        
        $this->assertInstanceOf(CatalogProduct::class, $product);
        $this->assertNotNull($product->name);
        $this->assertNotNull($product->sku);
        $this->assertNotNull($product->price);
        $this->assertNotNull($product->entity_id);
    }

    /**
     * Тест методу форматування ціни, якщо він існує
     */
    public function test_price_formatting()
    {
        $product = CatalogProduct::factory()->create([
            'price' => 25.50,
            'base_currency' => 'USD'
        ]);
        
        // Перевіряємо, що ціна форматується правильно, якщо є метод
        if (method_exists($product, 'getPriceFormatted')) {
            $formattedPrice = $product->getPriceFormatted();
            $this->assertStringContainsString('25.50', $formattedPrice);
            $this->assertStringContainsString('$', $formattedPrice);
        } else {
            // Якщо методу немає, просто перевіряємо наявність ціни
            $this->assertEquals(25.50, $product->price);
            $this->assertEquals('USD', $product->base_currency);
        }
    }

    /**
     * Тест специфічних полів для горіхів
     */
    public function test_nut_specific_fields()
    {
        $product = CatalogProduct::factory()->create([
            'nut_type' => 'almond',
            'organic' => true,
            'roasted' => true,
        ]);
        
        $this->assertEquals('almond', $product->nut_type);
        $this->assertTrue($product->organic);
        $this->assertTrue($product->roasted);
    }
} 