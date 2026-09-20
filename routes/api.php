<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Endpoints
Route::prefix('v1')->group(function () {

    // Products API
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Categories API
    Route::get('/categories', function () {
        return \App\Models\Category::where('is_active', true)->get();
    });

    // Brands API
    Route::get('/brands', function () {
        return \App\Models\Brand::where('is_active', true)->get();
    });
});
