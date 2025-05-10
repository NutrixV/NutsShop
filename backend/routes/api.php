<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::get('/', function () {
    return ['status' => 'ok', 'message' => 'NutsShop API is running'];
});

// Get CSRF token
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Public routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/product-filters', [ProductController::class, 'getFilters']);

// Customer authentication
Route::post('/customers/register', [CustomerController::class, 'register']);
Route::post('/customers/login', [CustomerController::class, 'login']);

// Cart (Quote) routes
Route::get('/cart', function (Request $request) {
    return app()->make(CartController::class)->show($request);
});
Route::post('/cart/items', [CartController::class, 'addItem']);
Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);
Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);

// Order routes (available for all users)
Route::post('/orders', [OrderController::class, 'store']);

// Health check endpoint for Render.com
Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Customer profile
    Route::get('/customers/me', [CustomerController::class, 'profile']);
    Route::put('/customers/me', [CustomerController::class, 'update']);
    
    // Customer addresses
    Route::get('/customers/addresses', [CustomerController::class, 'addresses']);
    Route::post('/customers/addresses', [CustomerController::class, 'addAddress']);
    Route::put('/customers/addresses/{id}', [CustomerController::class, 'updateAddress']);
    Route::delete('/customers/addresses/{id}', [CustomerController::class, 'deleteAddress']);
    
    // Order routes (only for authenticated users)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});
