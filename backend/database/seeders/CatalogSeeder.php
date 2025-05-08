<?php

namespace Database\Seeders;

use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Перевіряємо, чи вже існують категорії
        if (CatalogCategory::count() > 0) {
            $this->command->info('Категорії вже існують, пропускаємо створення.');
            return;
        }
        
        // Створення категорій
        $nutsCategory = CatalogCategory::create([
            'name' => 'Горіхи',
            'url_key' => 'nuts',
            'is_active' => true,
            'position' => 10
        ]);
        
        $sweetsCategory = CatalogCategory::create([
            'name' => 'Солодощі',
            'url_key' => 'sweets',
            'is_active' => true,
            'position' => 20
        ]);
        
        // Підкатегорії для горіхів
        $almondCategory = CatalogCategory::create([
            'parent_id' => $nutsCategory->category_id,
            'name' => 'Мигдаль',
            'url_key' => 'almonds',
            'is_active' => true,
            'position' => 10
        ]);
        
        $walnutCategory = CatalogCategory::create([
            'parent_id' => $nutsCategory->category_id,
            'name' => 'Волоські горіхи',
            'url_key' => 'walnuts',
            'is_active' => true,
            'position' => 20
        ]);
        
        $pistachioCategory = CatalogCategory::create([
            'parent_id' => $nutsCategory->category_id,
            'name' => 'Фісташки',
            'url_key' => 'pistachios',
            'is_active' => true,
            'position' => 30
        ]);
        
        // Підкатегорії для солодощів
        $chocolateCategory = CatalogCategory::create([
            'parent_id' => $sweetsCategory->category_id,
            'name' => 'Шоколад',
            'url_key' => 'chocolate',
            'is_active' => true,
            'position' => 10
        ]);
        
        $candyCategory = CatalogCategory::create([
            'parent_id' => $sweetsCategory->category_id,
            'name' => 'Цукерки',
            'url_key' => 'candy',
            'is_active' => true,
            'position' => 20
        ]);
        
        // Створення продуктів-горіхів
        $almondProduct = $this->createProduct(
            'Мигдаль обсмажений',
            'almond-roasted',
            'Ароматний обсмажений мигдаль, багатий на вітамін Е та корисні жири.',
            250.00,
            'Мигдаль',
            true,
            false,
            true,
            'US',
            250,
            date('Y-m-d', strtotime('+6 months'))
        );
        
        $almondProduct->categories()->attach($almondCategory->category_id);
        $almondProduct->categories()->attach($nutsCategory->category_id);
        
        $walnutProduct = $this->createProduct(
            'Волоські горіхи очищені',
            'walnut-shelled',
            'Відбірні волоські горіхи з багатим смаком та високим вмістом Омега-3.',
            220.00,
            'Волоський горіх',
            false,
            false,
            true,
            'UA',
            200,
            date('Y-m-d', strtotime('+4 months'))
        );
        
        $walnutProduct->categories()->attach($walnutCategory->category_id);
        $walnutProduct->categories()->attach($nutsCategory->category_id);
        
        $pistachioProduct = $this->createProduct(
            'Фісташки підсолені',
            'pistachio-salted',
            'Фісташки преміум якості з додаванням солі.',
            350.00,
            'Фісташка',
            false,
            true,
            true,
            'TR',
            150,
            date('Y-m-d', strtotime('+8 months'))
        );
        
        $pistachioProduct->categories()->attach($pistachioCategory->category_id);
        $pistachioProduct->categories()->attach($nutsCategory->category_id);
        
        // Створення солодощів
        $darkChocolateProduct = $this->createProduct(
            'Шоколад чорний 70%',
            'dark-chocolate-70',
            'Насичений чорний шоколад з вмістом какао 70%.',
            120.00,
            null,
            false,
            false,
            true,
            'CH',
            100,
            date('Y-m-d', strtotime('+12 months')),
            8,
            70.0
        );
        
        $darkChocolateProduct->categories()->attach($chocolateCategory->category_id);
        $darkChocolateProduct->categories()->attach($sweetsCategory->category_id);
        
        $milkChocolateProduct = $this->createProduct(
            'Шоколад молочний з горіхами',
            'milk-chocolate-nuts',
            'Ніжний молочний шоколад з цілими лісовими горіхами.',
            140.00,
            null,
            false,
            false,
            false,
            'CH',
            100,
            date('Y-m-d', strtotime('+10 months')),
            7,
            36.0
        );
        
        $milkChocolateProduct->categories()->attach($chocolateCategory->category_id);
        $milkChocolateProduct->categories()->attach($sweetsCategory->category_id);
    }
    
    /**
     * Допоміжний метод для створення продукту
     * 
     * @param string $name Назва продукту
     * @param string $urlKey URL-ключ продукту
     * @param string $description Опис продукту
     * @param float $price Ціна продукту
     * @param string|null $nutType Тип горіха (якщо застосовно)
     * @param bool|null $roasted Чи обсмажений
     * @param bool|null $salted Чи солений
     * @param bool|null $glutenFree Без глютену
     * @param string|null $originCountry Країна походження
     * @param int|null $weightG Вага в грамах
     * @param string|null $expiryDate Термін придатності
     * @param int|null $sweetness_level Рівень солодкості (0-10)
     * @param float|null $cocoaPct Відсоток какао (для шоколаду)
     * @return CatalogProduct
     */
    private function createProduct(
        string $name, 
        string $urlKey, 
        string $description, 
        float $price, 
        ?string $nutType = null,
        ?bool $roasted = false,
        ?bool $salted = false,
        ?bool $glutenFree = true,
        ?string $originCountry = null,
        ?int $weightG = null,
        ?string $expiryDate = null,
        ?int $sweetness_level = null,
        ?float $cocoaPct = null
    ): CatalogProduct {
        return CatalogProduct::create([
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'name' => $name,
            'description' => $description,
            'short_description' => Str::limit($description, 100),
            'price' => $price,
            'base_currency' => 'UAH',
            'qty' => 100,
            'is_in_stock' => true,
            'visibility' => 4,
            'status' => 1,
            'nut_type' => $nutType,
            'sweetness_level' => $sweetness_level,
            'cocoa_pct' => $cocoaPct,
            'salted' => $salted,
            'roasted' => $roasted,
            'gluten_free' => $glutenFree,
            'organic' => false,
            'origin_country' => $originCountry,
            'weight_g' => $weightG,
            'expiry_date' => $expiryDate
        ]);
    }
}
