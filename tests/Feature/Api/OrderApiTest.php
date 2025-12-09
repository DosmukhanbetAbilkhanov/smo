<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Company;
use App\Models\Nomenclature;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->city = City::factory()->create();
    $this->unit = Unit::factory()->create();
});

test('unauthenticated user cannot access orders', function () {
    $response = $this->getJson('/api/v1/orders');

    $response->assertUnauthorized();
});

test('authenticated user can get empty orders list', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/orders');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [],
        ]);
});

test('can create order from cart', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $nomenclature = Nomenclature::factory()->create(['unit_id' => $this->unit->id]);
    $product = Product::factory()->create([
        'shop_id' => $shop->id,
        'nomenclature_id' => $nomenclature->id,
        'quantity' => 100,
        'price' => 1500,
    ]);

    $cart = Cart::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 1500,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/orders', [
        'shop_id' => $shop->id,
        'delivery_address' => '123 Main St',
        'delivery_entry' => '2',
        'delivery_floor' => '5',
        'delivery_apartment' => '23',
        'delivery_intercom' => '23K',
        'delivery_city_id' => $this->city->id,
        'contact_phone' => '+77771234567',
        'delivery_notes' => 'Please call before delivery',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Order created successfully',
        ]);

    expect($response->json('data.order_number'))->toStartWith('ORD-');
    expect($response->json('data.status'))->toBe('pending');
    expect($response->json('data.total'))->toBe(3000);

    $product->refresh();
    expect($product->quantity)->toBe(98);

    expect(Cart::where('user_id', $user->id)->where('shop_id', $shop->id)->first()->items)->toHaveCount(0);
});

test('cannot create order from empty cart', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);
    $cart = Cart::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/orders', [
        'shop_id' => $shop->id,
        'delivery_address' => '123 Main St',
        'delivery_city_id' => $this->city->id,
        'contact_phone' => '+77771234567',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Cart is empty',
        ]);
});

test('cannot create order with insufficient stock', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $nomenclature = Nomenclature::factory()->create(['unit_id' => $this->unit->id]);
    $product = Product::factory()->create([
        'shop_id' => $shop->id,
        'nomenclature_id' => $nomenclature->id,
        'quantity' => 1,
        'price' => 1500,
    ]);

    $cart = Cart::factory()->create(['user_id' => $user->id, 'shop_id' => $shop->id]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'price' => 1500,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/orders', [
        'shop_id' => $shop->id,
        'delivery_address' => '123 Main St',
        'delivery_city_id' => $this->city->id,
        'contact_phone' => '+77771234567',
    ]);

    $response->assertStatus(400);
});

test('can get specific order', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'delivery_city_id' => $this->city->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/orders/{$order->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);
});

test('cannot get another users order', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user1 = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);
    $user2 = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user1->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $order = Order::factory()->create([
        'user_id' => $user1->id,
        'shop_id' => $shop->id,
        'delivery_city_id' => $this->city->id,
    ]);

    Sanctum::actingAs($user2);

    $response = $this->getJson("/api/v1/orders/{$order->id}");

    $response->assertNotFound();
});

test('can cancel pending order', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $nomenclature = Nomenclature::factory()->create(['unit_id' => $this->unit->id]);
    $product = Product::factory()->create([
        'shop_id' => $shop->id,
        'nomenclature_id' => $nomenclature->id,
        'quantity' => 90,
        'price' => 1500,
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'status' => 'pending',
        'delivery_city_id' => $this->city->id,
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 10,
        'price' => 1500,
        'subtotal' => 15000,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'status' => 'cancelled',
            ],
        ]);

    $product->refresh();
    expect($product->quantity)->toBe(100);
});

test('cannot cancel shipped order', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'status' => 'shipped',
        'delivery_city_id' => $this->city->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson("/api/v1/orders/{$order->id}/cancel");

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Order cannot be cancelled',
        ]);
});

test('order includes shop and items details', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $nomenclature = Nomenclature::factory()->create(['unit_id' => $this->unit->id]);
    $product = Product::factory()->create([
        'shop_id' => $shop->id,
        'nomenclature_id' => $nomenclature->id,
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'delivery_city_id' => $this->city->id,
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 1500,
        'subtotal' => 3000,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/orders/{$order->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'shop' => ['id', 'name'],
                'items' => [
                    '*' => [
                        'product' => ['id', 'name_ru'],
                        'quantity',
                        'price',
                        'subtotal',
                    ],
                ],
            ],
        ]);
});

test('orders are sorted by creation date descending', function () {
    $userType = UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    $user = User::factory()->create(['type_id' => 2, 'city_id' => $this->city->id]);

    $company = Company::factory()->create(['user_id' => $user->id, 'city_id' => $this->city->id]);
    $shop = Shop::factory()->create(['company_id' => $company->id]);

    $order1 = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'delivery_city_id' => $this->city->id,
        'created_at' => now()->subDays(2),
    ]);

    $order2 = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'delivery_city_id' => $this->city->id,
        'created_at' => now()->subDay(),
    ]);

    $order3 = Order::factory()->create([
        'user_id' => $user->id,
        'shop_id' => $shop->id,
        'delivery_city_id' => $this->city->id,
        'created_at' => now(),
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/orders');

    $response->assertOk();

    $orderIds = collect($response->json('data'))->pluck('id')->toArray();
    expect($orderIds)->toBe([$order3->id, $order2->id, $order1->id]);
});
