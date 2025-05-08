<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
