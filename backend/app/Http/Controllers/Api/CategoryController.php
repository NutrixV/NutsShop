<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request): JsonResponse
    {
        $active = $request->boolean('active', true);
        $parentId = $request->input('parent_id');
        
        $query = CatalogCategory::query();
        
        // Відображати лише активні категорії якщо активна опція
        if ($active) {
            $query->where('is_active', true);
        }
        
        // Фільтрувати за батьківською категорією, якщо задано
        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        }
        
        // Сортування за позицією
        $query->orderBy('position', 'asc');
        
        $categories = $query->get();
        
        return response()->json($categories);
    }

    /**
     * Display the specified category.
     */
    public function show(string $id): JsonResponse
    {
        $category = CatalogCategory::with('children')->findOrFail($id);
        
        return response()->json($category);
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
