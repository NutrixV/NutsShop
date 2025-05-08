<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\CatalogProductAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CatalogProduct::query();
        
        // Фільтрувати за категорією, якщо задано
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('catalog_category.category_id', $request->input('category_id'));
            });
        }
        
        // Фільтрувати за пошуковим запитом, якщо задано
        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm)
                  ->orWhere('sku', 'LIKE', $searchTerm);
            });
        }
        
        // Фільтрувати за featured (рекомендовані) продукти
        if ($request->has('featured')) {
            $featured = $request->boolean('featured');
            if ($featured) {
                // Логіка для відображення рекомендованих продуктів
                // Це може бути логіка за замовчуванням для головної сторінки
                $query->where('visibility', 4) // Visible in catalog and search
                      ->where('status', 1) // Enabled
                      ->where('is_in_stock', true);
            }
        }
        
        // Фільтрація за ціновим діапазоном
        if ($request->has('price_from')) {
            $query->where('price', '>=', $request->input('price_from'));
        }
        
        if ($request->has('price_to')) {
            $query->where('price', '<=', $request->input('price_to'));
        }
        
        // Фільтрація за типом горіхів (nut_type)
        if ($request->has('attr_nut_type')) {
            $nutTypes = explode(',', $request->input('attr_nut_type'));
            $query->whereIn('nut_type', $nutTypes);
        }
        
        // Фільтрація за країною походження (origin_country)
        if ($request->has('attr_origin_country')) {
            $countries = explode(',', $request->input('attr_origin_country'));
            $query->whereIn('origin_country', $countries);
        }
        
        // Фільтрація за відсотком какао (cocoa_pct)
        if ($request->has('cocoa_pct_from')) {
            $query->where('cocoa_pct', '>=', $request->input('cocoa_pct_from'));
        }
        
        if ($request->has('cocoa_pct_to')) {
            $query->where('cocoa_pct', '<=', $request->input('cocoa_pct_to'));
        }
        
        // Фільтрація за вагою (weight_g)
        if ($request->has('weight_g_from')) {
            $query->where('weight_g', '>=', $request->input('weight_g_from'));
        }
        
        if ($request->has('weight_g_to')) {
            $query->where('weight_g', '<=', $request->input('weight_g_to'));
        }
        
        // Фільтрація за рівнем солодкості (sweetness_level)
        if ($request->has('attr_sweetness_level')) {
            $levels = explode(',', $request->input('attr_sweetness_level'));
            $query->whereIn('sweetness_level', $levels);
        }
        
        // Фільтрація за булевими атрибутами (salted, roasted, gluten_free, organic)
        $booleanFields = ['salted', 'roasted', 'gluten_free', 'organic'];
        foreach ($booleanFields as $field) {
            if ($request->has('attr_' . $field) && $request->input('attr_' . $field) == 1) {
                $query->where($field, true);
            }
        }
        
        // Фільтрація за знижками
        if ($request->has('discount') && $request->boolean('discount')) {
            $query->whereNotNull('special_price')
                  ->whereRaw('special_price < price');
        }
        
        // Сортування
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        
        $allowedSortFields = ['name', 'price', 'created_at'];
        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        // Пагінація
        $limit = $request->input('limit', 20);
        $products = $query->paginate($limit);
        
        return response()->json($products);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id): JsonResponse
    {
        $product = CatalogProduct::with('categories')->findOrFail($id);
        
        return response()->json($product);
    }

    /**
     * Get available filters for the current product set
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getFilters(Request $request): JsonResponse
    {
        // Створюємо базовий запит для продуктів, який буде використовуватись для визначення фільтрів
        $query = CatalogProduct::query();
        
        // Застосовуємо ті ж фільтри, що і при отриманні продуктів
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('catalog_category.category_id', $request->input('category_id'));
            });
        }
        
        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm)
                  ->orWhere('sku', 'LIKE', $searchTerm);
            });
        }
        
        // Клонуємо запит для подальшого використання
        $productsQuery = clone $query;
        
        // Отримуємо ціновий діапазон для всіх продуктів, що відповідають поточним фільтрам
        $priceRange = $query->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        
        // Отримуємо продукти для аналізу
        $products = $productsQuery->get();
        
        $filters = [];
        
        // Додаємо ціновий діапазон як фільтр
        $filters[] = [
            'code' => 'price',
            'name' => 'Ціна',
            'type' => 'range',
            'min' => $priceRange->min_price ?? 0,
            'max' => $priceRange->max_price ?? 1000,
        ];
        
        // Перевіряємо і додаємо фільтр за типом горіхів (nut_type)
        $nutTypes = $products->pluck('nut_type')->filter()->unique()->values();
        if ($nutTypes->count() > 0) {
            $options = [];
            foreach ($nutTypes as $type) {
                $count = $products->where('nut_type', $type)->count();
                $options[] = [
                    'id' => $type,
                    'value' => $type,
                    'count' => $count
                ];
            }
            
            $filters[] = [
                'code' => 'nut_type',
                'name' => 'Тип горіхів',
                'type' => 'checkbox',
                'group' => 'general',
                'options' => $options
            ];
        }
        
        // Перевіряємо і додаємо фільтр за країною походження (origin_country)
        $countries = $products->pluck('origin_country')->filter()->unique()->values();
        if ($countries->count() > 0) {
            $options = [];
            foreach ($countries as $country) {
                $count = $products->where('origin_country', $country)->count();
                $options[] = [
                    'id' => $country,
                    'value' => $this->getCountryName($country),
                    'count' => $count
                ];
            }
            
            $filters[] = [
                'code' => 'origin_country',
                'name' => 'Країна походження',
                'type' => 'checkbox',
                'group' => 'general',
                'options' => $options
            ];
        }
        
        // Додаємо булеві фільтри (так/ні)
        $booleanFilters = [
            'salted' => 'Солоні',
            'roasted' => 'Обсмажені',
            'gluten_free' => 'Без глютену',
            'organic' => 'Органічні'
        ];
        
        foreach ($booleanFilters as $field => $name) {
            $trueCount = $products->where($field, true)->count();
            
            if ($trueCount > 0) {
                $filters[] = [
                    'code' => $field,
                    'name' => $name,
                    'type' => 'checkbox',
                    'group' => 'general',
                    'options' => [
                        [
                            'id' => 1,
                            'value' => 'Так',
                            'count' => $trueCount
                        ]
                    ]
                ];
            }
        }
        
        // Додаємо фільтр за рівнем солодкості (sweetness_level), якщо він використовується
        $sweetnessLevels = $products->pluck('sweetness_level')->filter()->unique()->values();
        if ($sweetnessLevels->count() > 0) {
            $options = [];
            foreach ($sweetnessLevels as $level) {
                $count = $products->where('sweetness_level', $level)->count();
                $levelName = $this->getSweetnessLevelName($level);
                $options[] = [
                    'id' => $level,
                    'value' => $levelName,
                    'count' => $count
                ];
            }
            
            $filters[] = [
                'code' => 'sweetness_level',
                'name' => 'Рівень солодкості',
                'type' => 'checkbox',
                'group' => 'general',
                'options' => $options
            ];
        }
        
        // Додаємо фільтр за відсотком какао (cocoa_pct), якщо він використовується
        $hasCocoa = $products->whereNotNull('cocoa_pct')->count() > 0;
        if ($hasCocoa) {
            $cocoaRange = [
                'min' => $products->whereNotNull('cocoa_pct')->min('cocoa_pct'),
                'max' => $products->whereNotNull('cocoa_pct')->max('cocoa_pct')
            ];
            
            $filters[] = [
                'code' => 'cocoa_pct',
                'name' => 'Відсоток какао',
                'type' => 'range',
                'group' => 'nutrition',
                'min' => $cocoaRange['min'],
                'max' => $cocoaRange['max'],
                'unit' => '%'
            ];
        }
        
        // Додаємо фільтр за вагою (weight_g), якщо він використовується
        $hasWeight = $products->whereNotNull('weight_g')->count() > 0;
        if ($hasWeight) {
            $weightRange = [
                'min' => $products->whereNotNull('weight_g')->min('weight_g'),
                'max' => $products->whereNotNull('weight_g')->max('weight_g')
            ];
            
            $filters[] = [
                'code' => 'weight_g',
                'name' => 'Вага',
                'type' => 'range',
                'group' => 'general',
                'min' => $weightRange['min'],
                'max' => $weightRange['max'],
                'unit' => 'г'
            ];
        }
        
        return response()->json($filters);
    }
    
    /**
     * Отримати назву країни за її кодом
     *
     * @param string $code
     * @return string
     */
    private function getCountryName(string $code): string
    {
        $countries = [
            'UA' => 'Україна',
            'US' => 'США',
            'TR' => 'Туреччина',
            'IT' => 'Італія',
            'ES' => 'Іспанія',
            'FR' => 'Франція',
            'GR' => 'Греція',
            'CN' => 'Китай',
            'IN' => 'Індія',
            'ID' => 'Індонезія',
            'BR' => 'Бразилія',
            'CL' => 'Чилі',
            'AU' => 'Австралія',
            'NZ' => 'Нова Зеландія'
        ];
        
        return $countries[$code] ?? $code;
    }
    
    /**
     * Отримати опис рівня солодкості за його значенням
     *
     * @param int $level
     * @return string
     */
    private function getSweetnessLevelName(int $level): string
    {
        $levels = [
            1 => 'Не солодкий',
            2 => 'Трохи солодкий',
            3 => 'Помірно солодкий',
            4 => 'Солодкий',
            5 => 'Дуже солодкий'
        ];
        
        return $levels[$level] ?? "Рівень $level";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
