<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin panel routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group with admin prefix. This file
| handles custom admin routes not managed by Filament.
|
*/

// Custom admin routes (outside of Filament)
Route::middleware(['auth:web', 'can:admin'])->prefix('admin')->group(function () {
    
    // Custom reports or API endpoints for admin functions
    Route::get('/reports/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales']);
    Route::get('/reports/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory']);
    
    // Export functions
    Route::get('/export/orders', [\App\Http\Controllers\Admin\ExportController::class, 'orders']);
    Route::get('/export/customers', [\App\Http\Controllers\Admin\ExportController::class, 'customers']);
}); 