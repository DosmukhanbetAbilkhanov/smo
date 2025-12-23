<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// City selection routes
Route::post('/select-city', [CityController::class, 'store'])->name('city.store');
Route::post('/change-city', [CityController::class, 'change'])->name('city.change');

// Temporary route for testing - remove after testing
Route::get('/clear-city', function () {
    session()->forget('selected_city_id');
    if ($user = auth()->user()) {
        $user->update(['city_id' => null]);
    }

    return redirect('/')->with('success', 'City cleared - modal should appear');
});

// Categories
Route::get('/categories', [CatalogController::class, 'categories'])->name('categories.index');
Route::get('/categories/{slug}', [CatalogController::class, 'show'])->name('categories.show');

// Products
Route::get('/products', [CatalogController::class, 'products'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Shops
Route::get('/shops/{id}', [ShopController::class, 'show'])->name('shops.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Checkout & Orders
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
});

// Dashboard
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Design System
Route::get('/design-system', function () {
    return Inertia::render('DesignSystem');
})->name('design-system');

require __DIR__.'/settings.php';
