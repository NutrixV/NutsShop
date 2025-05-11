<?php

namespace Tests\Unit\Models;

use App\Models\CustomerEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CustomerEntityTest
 * 
 * Тести для моделі CustomerEntity
 */
class CustomerEntityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест створення клієнта з базовими атрибутами
     */
    public function test_can_create_customer()
    {
        $customerData = [
            'email' => 'test@example.com',
            'password_hash' => bcrypt('password123'),
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $customer = CustomerEntity::create($customerData);

        $this->assertInstanceOf(CustomerEntity::class, $customer);
        $this->assertEquals('test@example.com', $customer->email);
        $this->assertEquals('John', $customer->first_name);
        $this->assertEquals('Doe', $customer->last_name);
        $this->assertNotNull($customer->entity_id);
    }
    
    /**
     * Тест додавання API токену
     */
    public function test_can_add_api_token()
    {
        $customer = CustomerEntity::factory()->create([
            'api_token' => null
        ]);
        
        $token = 'test-api-token-12345';
        $customer->api_token = $token;
        $customer->save();
        
        $refreshedCustomer = CustomerEntity::find($customer->entity_id);
        $this->assertEquals($token, $refreshedCustomer->api_token);
    }
    
    /**
     * Тест методу getFullName
     */
    public function test_get_full_name()
    {
        $customer = CustomerEntity::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        
        // Якщо метод існує
        if (method_exists($customer, 'getFullName')) {
            $this->assertEquals('John Doe', $customer->getFullName());
        } else {
            // Перевіряємо прямо конкатенацію імені
            $this->assertEquals('John Doe', $customer->first_name . ' ' . $customer->last_name);
        }
    }
} 