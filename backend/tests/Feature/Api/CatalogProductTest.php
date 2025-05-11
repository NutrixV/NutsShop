<?php

/**
 * Tests for CatalogProduct API endpoints
 *
 * @category Tests
 * @package  Feature\Api
 * @author   Claude Developer <claude@anthropic.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://nuts-shop.com
 */

namespace Tests\Feature\Api;

use App\Models\CatalogProduct;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CatalogProductTest - тести для API продуктів каталогу
 * 
 * @category Tests
 * @package  Feature\Api
 * @author   Claude Developer <claude@anthropic.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://nuts-shop.com
 */
class CatalogProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест отримання списку продуктів.
     *
     * @return void
     */
    public function testCanFetchProducts()
    {
        // Створюємо кілька продуктів для тесту
        CatalogProduct::factory()->count(3)->create();

        $response = $this->getJson('/api/products');
        
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'entity_id', 'sku', 'name', 'price'
                        ]
                    ],
                    'current_page',
                    'total'
                ]
            );
    }

    /**
     * Тест отримання одного продукту.
     *
     * @return void
     */
    public function testCanFetchSingleProduct()
    {
        $product = CatalogProduct::factory()->create(
            [
                'name' => 'Test Product',
                'sku' => 'TEST-SKU-123',
                'price' => 19.99,
            ]
        );

        $response = $this->getJson('/api/products/' . $product->entity_id);
        
        $response->assertStatus(200)
            ->assertJson(
                [
                    'entity_id' => $product->entity_id,
                    'name' => 'Test Product',
                    'sku' => 'TEST-SKU-123',
                    'price' => '19.99', // API повертає як string
                ]
            );
    }

    /**
     * Тест отримання помилки при запиті неіснуючого продукту.
     *
     * @return void
     */
    public function testReturns404ForNonExistentProduct()
    {
        $response = $this->getJson('/api/products/999999');

        $response->assertStatus(404);
    }

    /**
     * Тест фільтрації продуктів за ціною.
     *
     * @return void
     */
    public function testCanFilterProductsByPrice()
    {
        // Створюємо продукти з різними цінами
        CatalogProduct::factory()->create(['price' => 15.99]);
        CatalogProduct::factory()->create(['price' => 25.99]);
        CatalogProduct::factory()->create(['price' => 35.99]);
        CatalogProduct::factory()->create(['price' => 45.99]);

        // Фільтруємо продукти з ціною від 20 до 40
        $response = $this->getJson('/api/products?price_from=20&price_to=40');
        
        $response->assertStatus(200);
        
        $responseData = $response->json();
        $this->assertArrayHasKey('data', $responseData);
        
        // Перевіряємо, що всі продукти мають ціну в діапазоні
        foreach ($responseData['data'] as $product) {
            $price = (float)$product['price']; // Конвертуємо в float для порівняння
            $this->assertGreaterThanOrEqual(20, $price);
            $this->assertLessThanOrEqual(40, $price);
        }
    }

    /**
     * Простий тест для перевірки, що тестова інфраструктура працює.
     *
     * @return void
     */
    public function testSimple()
    {
        $this->assertTrue(true);
    }
}