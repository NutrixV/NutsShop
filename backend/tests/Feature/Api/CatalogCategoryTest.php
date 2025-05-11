<?php

namespace Tests\Feature\Api;

use App\Models\CatalogCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the catalog category API endpoints.
 *
 * @category Tests
 * @package  Tests\Feature\Api
 * @author   API Test Team <api.test@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://nuts-shop.com
 */
class CatalogCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Підготовка тестового середовища.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо тестові категорії
        CatalogCategory::factory()->count(3)->create();
    }

    /**
     * Тест отримання списку категорій.
     *
     * @return void
     */
    public function testCanFetchCategories()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'category_id',
                    'name',
                    'url_key',
                    'is_active',
                ]
            ]);
            
        // Перевіряємо, що ми отримали правильну кількість категорій
        $this->assertEquals(3, count($response->json()));
    }

    /**
     * Тест отримання однієї категорії за ідентифікатором.
     *
     * @return void
     */
    public function testCanFetchSingleCategory()
    {
        $category = CatalogCategory::factory()->create([
            'name' => 'Test Category',
            'url_key' => 'test-category',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/categories/{$category->category_id}");

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Test Category')
            ->assertJsonPath('url_key', 'test-category');
    }

    /**
     * Тест отримання помилки при запиті неіснуючої категорії.
     *
     * @return void
     */
    public function testReturns404ForNonExistentCategory()
    {
        $response = $this->getJson('/api/categories/9999');

        $response->assertStatus(404);
    }

    /**
     * Тест ієрархії категорій (батьківська-дочірня).
     *
     * @return void
     */
    public function testCategoryHierarchy()
    {
        // Створюємо батьківську категорію
        $parentCategory = CatalogCategory::factory()->create([
            'name' => 'Parent Category',
            'url_key' => 'parent-category',
        ]);

        // Створюємо дочірню категорію
        $childCategory = CatalogCategory::factory()->create([
            'name' => 'Child Category',
            'url_key' => 'child-category',
            'parent_id' => $parentCategory->category_id,
        ]);

        // Отримуємо батьківську категорію
        $response = $this->getJson("/api/categories/{$parentCategory->category_id}");
        $response->assertStatus(200)
            ->assertJsonPath('name', 'Parent Category');

        // Отримуємо дочірню категорію
        $response = $this->getJson("/api/categories/{$childCategory->category_id}");
        $response->assertStatus(200)
            ->assertJsonPath('name', 'Child Category')
            ->assertJsonPath('parent_id', $parentCategory->category_id);
    }

    /**
     * Проста перевірка, що тестове середовище працює.
     *
     * @return void
     */
    public function testSimple()
    {
        $this->assertTrue(true);
    }
} 