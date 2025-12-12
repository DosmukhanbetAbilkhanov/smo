<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Categories
Route::get('/categories', [CatalogController::class, 'categories'])->name('categories.index');
Route::get('/categories/{slug}', [CatalogController::class, 'show'])->name('categories.show');

// Products & Search
Route::get('/products', [CatalogController::class, 'products'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [CatalogController::class, 'search'])->name('search');

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

require __DIR__.'/settings.php';
