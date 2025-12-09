<?php

use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NomenclatureController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/health', function () {
        return response()->json(['status' => 'ok']);
    });

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/categories/{slug}/nomenclatures', [CategoryController::class, 'nomenclatures']);

    // Nomenclatures
    Route::get('/nomenclatures', [NomenclatureController::class, 'index']);
    Route::get('/nomenclatures/{id}', [NomenclatureController::class, 'show']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Cart routes
        Route::get('/carts', [CartController::class, 'index']);
        Route::get('/carts/{shopId}', [CartController::class, 'show']);
        Route::post('/cart/add', [CartController::class, 'add']);
        Route::put('/cart/items/{itemId}', [CartController::class, 'update']);
        Route::delete('/cart/items/{itemId}', [CartController::class, 'remove']);
        Route::delete('/carts/{shopId}', [CartController::class, 'clear']);
    });
});
