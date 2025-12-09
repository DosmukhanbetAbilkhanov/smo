<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NomenclatureController;
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

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
