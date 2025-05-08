<?php

namespace Database\Seeders;

use App\Models\CatalogProductAttribute;
use App\Models\CatalogProductAttributeOption;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Основні атрибути для фільтрації
        $attributes = [
            [
                'attribute_code' => 'nut_type',
                'frontend_label' => 'Тип горіхів',
                'frontend_input' => 'select',
                'attribute_group' => 'general',
                'position' => 10,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'options' => [
                    ['value' => 'Мигдаль', 'position' => 10],
                    ['value' => 'Фісташки', 'position' => 20],
                    ['value' => 'Кеш\'ю', 'position' => 30],
                    ['value' => 'Волоський горіх', 'position' => 40],
                    ['value' => 'Фундук', 'position' => 50],
                    ['value' => 'Арахіс', 'position' => 60],
                    ['value' => 'Бразильський горіх', 'position' => 70],
                    ['value' => 'Пекан', 'position' => 80],
                ]
            ],
            [
                'attribute_code' => 'origin_country',
                'frontend_label' => 'Країна походження',
                'frontend_input' => 'select',
                'attribute_group' => 'general',
                'position' => 20,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'options' => [
                    ['value' => 'Україна', 'position' => 10],
                    ['value' => 'США', 'position' => 20],
                    ['value' => 'Іспанія', 'position' => 30],
                    ['value' => 'Італія', 'position' => 40],
                    ['value' => 'Туреччина', 'position' => 50],
                    ['value' => 'Іран', 'position' => 60],
                    ['value' => 'Китай', 'position' => 70],
                ]
            ],
            [
                'attribute_code' => 'processing_type',
                'frontend_label' => 'Тип обробки',
                'frontend_input' => 'select',
                'attribute_group' => 'general',
                'position' => 30,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'options' => [
                    ['value' => 'Сирі', 'position' => 10],
                    ['value' => 'Обсмажені', 'position' => 20],
                    ['value' => 'Солоні', 'position' => 30],
                    ['value' => 'Карамелізовані', 'position' => 40],
                    ['value' => 'В шоколаді', 'position' => 50],
                ]
            ],
            [
                'attribute_code' => 'weight',
                'frontend_label' => 'Вага',
                'frontend_input' => 'range',
                'attribute_group' => 'general',
                'position' => 40,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
            ],
            [
                'attribute_code' => 'organic',
                'frontend_label' => 'Органічні',
                'frontend_input' => 'select',
                'attribute_group' => 'general',
                'position' => 50,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'options' => [
                    ['value' => 'Так', 'position' => 10],
                    ['value' => 'Ні', 'position' => 20],
                ]
            ],
            [
                'attribute_code' => 'calories',
                'frontend_label' => 'Калорійність',
                'frontend_input' => 'range',
                'attribute_group' => 'nutrition',
                'position' => 60,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
            ],
            [
                'attribute_code' => 'protein',
                'frontend_label' => 'Білок',
                'frontend_input' => 'range',
                'attribute_group' => 'nutrition',
                'position' => 70,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
            ],
            [
                'attribute_code' => 'fat',
                'frontend_label' => 'Жири',
                'frontend_input' => 'range',
                'attribute_group' => 'nutrition',
                'position' => 80,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
            ],
            [
                'attribute_code' => 'carbs',
                'frontend_label' => 'Вуглеводи',
                'frontend_input' => 'range',
                'attribute_group' => 'nutrition',
                'position' => 90,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
            ],
            [
                'attribute_code' => 'storage_conditions',
                'frontend_label' => 'Умови зберігання',
                'frontend_input' => 'select',
                'attribute_group' => 'storage',
                'position' => 100,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'options' => [
                    ['value' => 'Прохолодне сухе місце', 'position' => 10],
                    ['value' => 'Холодильник', 'position' => 20],
                    ['value' => 'Морозильна камера', 'position' => 30],
                ]
            ],
            [
                'attribute_code' => 'shelf_life',
                'frontend_label' => 'Термін придатності',
                'frontend_input' => 'range',
                'attribute_group' => 'storage',
                'position' => 110,
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
            ],
        ];

        foreach ($attributes as $attributeData) {
            $options = $attributeData['options'] ?? [];
            unset($attributeData['options']);
            
            $attribute = CatalogProductAttribute::create($attributeData);
            
            if (!empty($options)) {
                foreach ($options as $optionData) {
                    $optionData['attribute_id'] = $attribute->attribute_id;
                    CatalogProductAttributeOption::create($optionData);
                }
            }
        }
    }
} 