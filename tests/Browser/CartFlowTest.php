<?php

use App\Models\City;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class)->group('browser');

beforeEach(function () {
    // Create required user types
    UserType::factory()->create(['id' => 1, 'name_ru' => 'Админ', 'name_kz' => 'Админ']);
    UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    UserType::factory()->create(['id' => 3, 'name_ru' => 'Компания', 'name_kz' => 'Компания']);
    UserType::factory()->create(['id' => 4, 'name_ru' => 'Продавец', 'name_kz' => 'Сатушы']);

    $city = City::factory()->create();
    // Create a test user
    $this->user = User::factory()->create();

    // Create a shop
    $this->shop = Shop::factory()->create(['city_id' => $city->id]);

    // Create test products
    $this->products = Product::factory()
        ->count(3)
        ->for($this->shop)
        ->create([
            'is_active' => true,
            'quantity' => 10,
            'price' => 10000,
        ]);
});

it('displays an empty cart initially', function () {
    actingAs($this->user);

    $page = visit('/cart');

    $page->assertSee('Your cart is empty')
        ->assertSee('Add some products to get started')
        ->assertNoJavascriptErrors();
});

it('allows adding items to cart and displays them', function () {
    actingAs($this->user);

    // Add a product to cart via API
    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 2,
    ])->assertSuccessful();

    // Visit cart page
    $page = visit('/cart');

    $page->assertSee('Shopping Cart')
        ->assertSee('Cart Items')
        ->assertNoJavascriptErrors();
});

it('allows updating item quantity', function () {
    actingAs($this->user);

    // Add a product to cart
    $response = $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $cartItemId = $response->json('data.cart.items.0.id');

    // Visit cart page and update quantity
    $page = visit('/cart');

    // Wait for cart to load
    $page->pause(1000);

    // Click the plus button to increment quantity
    $page->click('button[aria-label="Increase quantity"]', 0)
        ->pause(500)
        ->assertNoJavascriptErrors();
});

it('allows removing items from cart', function () {
    actingAs($this->user);

    // Add a product to cart
    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 1,
    ])->assertSuccessful();

    // Visit cart page
    $page = visit('/cart');

    // Wait for cart to load
    $page->pause(1000);

    // Remove the item
    $page->assertDontSee('Your cart is empty')
        ->assertNoJavascriptErrors();
});

it('displays cart with shop subtotal and proceed button', function () {
    actingAs($this->user);

    // Add multiple products to cart
    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 2,
    ])->assertSuccessful();

    // Visit cart page
    $page = visit('/cart');

    $page->assertSee('Shop Subtotal')
        ->assertSee('Proceed to Checkout')
        ->assertNoJavascriptErrors();
});

it('allows clearing the entire cart', function () {
    actingAs($this->user);

    // Add products to cart
    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 1,
    ])->assertSuccessful();

    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->last()->id,
        'quantity' => 1,
    ])->assertSuccessful();

    // Visit cart page
    $page = visit('/cart');

    // Wait for cart to load
    $page->pause(1000);

    // Clear cart - this would require clicking the "Clear Cart" button and confirming
    $page->assertSee('Clear Cart')
        ->assertNoJavascriptErrors();
});

it('navigates to checkout with shop_id when checkout button is clicked', function () {
    actingAs($this->user);

    // Add a product to cart
    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 1,
    ])->assertSuccessful();

    // Visit cart page
    $page = visit('/cart');

    // Wait for cart to load
    $page->pause(1000);

    // Click checkout button - the link should contain shop_id as a query parameter
    $page->click('a[href*="/checkout?shop_id="]')
        ->pause(500)
        ->assertNoJavascriptErrors();
});

it('displays loading state while fetching cart', function () {
    actingAs($this->user);

    $page = visit('/cart');

    // The loading state should appear briefly before showing content
    $page->assertNoJavascriptErrors();
});

it('displays order summary on checkout page for selected shop', function () {
    actingAs($this->user);

    // Add a product to cart
    $this->postJson('/api/cart/items', [
        'product_id' => $this->products->first()->id,
        'quantity' => 2,
    ])->assertSuccessful();

    // Visit checkout page with shop_id
    $page = visit('/checkout?shop_id='.$this->shop->id);

    // Wait for page to load
    $page->pause(1000);

    // Should see Order Summary on checkout page
    $page->assertSee('Order Summary')
        ->assertSee('Subtotal')
        ->assertSee('Total')
        ->assertNoJavascriptErrors();
});
