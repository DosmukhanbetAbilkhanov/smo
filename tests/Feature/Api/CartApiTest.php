<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class)->group('api');

beforeEach(function () {
    // Create required user types
    UserType::factory()->create(['id' => 1, 'name_ru' => 'Админ', 'name_kz' => 'Админ']);
    UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    UserType::factory()->create(['id' => 3, 'name_ru' => 'Компания', 'name_kz' => 'Компания']);
    UserType::factory()->create(['id' => 4, 'name_ru' => 'Продавец', 'name_kz' => 'Сатушы']);

    $this->city = City::factory()->create();
});

test('unauthenticated user cannot access carts', function () {
    $response = $this->getJson('/api/v1/carts');

    $response->assertUnauthorized();
});

test('authenticated user can get empty carts list', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/carts');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ])
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonCount(0, 'data');
});

test('can add item to cart and creates shop-specific cart', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'price' => 1000,
        'quantity' => 10,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Item added to cart successfully',
        ])
        ->assertJsonPath('data.shop_id', $shop->id)
        ->assertJsonPath('data.items_count', 2)
        ->assertJsonCount(1, 'data.items');

    expect(Cart::count())->toBe(1);
    expect(Cart::first()->shop_id)->toBe($shop->id);
    expect(CartItem::count())->toBe(1);
});

test('can get cart for specific shop', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/carts/{$shop->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $cart->id)
        ->assertJsonPath('data.shop_id', $shop->id);
});

test('adding items from different shops creates separate carts', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop1 = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $shop2 = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $product1 = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop1->id,
        'quantity' => 10,
    ]);

    $product2 = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop2->id,
        'quantity' => 10,
    ]);

    Sanctum::actingAs($user);

    // Add item from shop 1
    $this->postJson('/api/v1/cart/add', [
        'product_id' => $product1->id,
        'quantity' => 2,
    ]);

    // Add item from shop 2
    $this->postJson('/api/v1/cart/add', [
        'product_id' => $product2->id,
        'quantity' => 3,
    ]);

    $response = $this->getJson('/api/v1/carts');

    $response->assertOk()
        ->assertJsonCount(2, 'data');

    expect(Cart::count())->toBe(2);
});

test('adding same product twice updates quantity in same cart', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'price' => 1000,
        'quantity' => 10,
    ]);

    Sanctum::actingAs($user);

    // Add product first time
    $this->postJson('/api/v1/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    // Add same product again
    $response = $this->postJson('/api/v1/cart/add', [
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.items_count', 5);

    expect(Cart::count())->toBe(1);
    expect(CartItem::count())->toBe(1);
    expect(CartItem::first()->quantity)->toBe(5);
});

test('cannot add item with insufficient stock', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'quantity' => 5,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/cart/add', [
        'product_id' => $product->id,
        'quantity' => 10,
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Insufficient stock available',
        ]);
});

test('can update cart item quantity', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'price' => 1000,
        'quantity' => 10,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => $product->price,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/cart/items/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Cart updated successfully',
        ])
        ->assertJsonPath('data.items_count', 5);

    expect($cartItem->fresh()->quantity)->toBe(5);
});

test('can remove item from cart', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => $product->price,
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/v1/cart/items/{$cartItem->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Item removed from cart successfully',
        ])
        ->assertJsonPath('data.items_count', 0)
        ->assertJsonCount(0, 'data.items');

    expect(CartItem::count())->toBe(0);
});

test('can clear cart for specific shop', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->count(3)->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ])->each(function ($product) use ($cart) {
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'price' => $product->price,
        ]);
    });

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/v1/carts/{$shop->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ])
        ->assertJsonPath('data.items_count', 0)
        ->assertJsonCount(0, 'data.items');

    expect(CartItem::count())->toBe(0);
});

test('cart includes shop and product details', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create(['name_ru' => 'Метр']);
    $category = Category::factory()->create(['name_ru' => 'Кабели']);
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
        'name_ru' => 'Кабель ВВГ',
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
        'name' => 'Test Shop',
    ]);

    $product = Product::factory()->create([
        'name_ru' => 'Test Product',
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'price' => 1000,
    ]);

    Sanctum::actingAs($user);

    $this->postJson('/api/v1/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->getJson("/api/v1/carts/{$shop->id}");

    $response->assertOk()
        ->assertJsonPath('data.shop.name', 'Test Shop')
        ->assertJsonPath('data.items.0.product.name_ru', 'Test Product')
        ->assertJsonPath('data.items.0.product.nomenclature.name_ru', 'Кабель ВВГ')
        ->assertJsonPath('data.items.0.quantity', 2)
        ->assertJsonPath('data.items.0.price', 1000);
});

test('cart calculates total correctly', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
    ]);

    $product1 = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'price' => 1000,
    ]);

    $product2 = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
        'price' => 500,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product1->id,
        'quantity' => 2,
        'price' => $product1->price,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product2->id,
        'quantity' => 3,
        'price' => $product2->price,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/carts/{$shop->id}");

    $response->assertOk()
        ->assertJsonPath('data.items_count', 5);

    // Total should be (1000 * 2) + (500 * 3) = 3500
    expect($response->json('data.total'))->toBe(3500);
});

test('cannot update another users cart item', function () {
    $user1 = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $user2 = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
    ]);

    $company = Company::factory()->create(['city_id' => $this->city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $this->city->id,
    ]);

    $cart1 = Cart::factory()->create([
        'user_id' => $user1->id,
        'shop_id' => $shop->id,
    ]);

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart1->id,
        'product_id' => $product->id,
        'price' => $product->price,
    ]);

    Sanctum::actingAs($user2);

    $response = $this->putJson("/api/v1/cart/items/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response->assertNotFound();
});
