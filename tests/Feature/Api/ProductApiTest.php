<?php

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\Shop;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class)->group('api');

beforeEach(function () {
    Cache::flush();
});

test('can retrieve paginated products', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->count(20)->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'name_ru',
                        'name_kz',
                        'SKU',
                        'price',
                        'quantity',
                        'images',
                        'nomenclature',
                        'shop',
                        'specs',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonPath('data.pagination.total', 20)
        ->assertJsonPath('data.pagination.per_page', 15)
        ->assertJsonCount(15, 'data.data');
});

test('can retrieve specific page of products', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->count(20)->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?page=2');

    $response->assertOk()
        ->assertJsonPath('data.pagination.current_page', 2)
        ->assertJsonCount(5, 'data.data');
});

test('can search products by name_ru', function () {
    Cache::flush();

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product1 = Product::factory()->create([
        'name_ru' => 'Cable Copper Wire',
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $product2 = Product::factory()->create([
        'name_ru' => 'Socket Electric',
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?search=Cable');

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 1)
        ->assertJsonPath('data.data.0.name_ru', 'Cable Copper Wire');
});

test('can search products by SKU', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product = Product::factory()->create([
        'SKU' => 'ABC123',
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->count(5)->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?search=ABC123');

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 1)
        ->assertJsonPath('data.data.0.SKU', 'ABC123');
});

test('can filter products by price range', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->create([
        'price' => 500,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->create([
        'price' => 1500,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->create([
        'price' => 2500,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?min_price=1000&max_price=2000');

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 1);

    expect($response->json('data.data.0.price'))->toBe(1500);
});

test('can filter products by category', function () {
    $unit = Unit::factory()->create();
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();

    $nomenclature1 = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category1->id,
    ]);

    $nomenclature2 = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category2->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->count(3)->create([
        'nomenclature_id' => $nomenclature1->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->count(2)->create([
        'nomenclature_id' => $nomenclature2->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson("/api/v1/products?category_id={$category1->id}");

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 3);
});

test('can filter products by nomenclature', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature1 = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $nomenclature2 = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->count(3)->create([
        'nomenclature_id' => $nomenclature1->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->count(2)->create([
        'nomenclature_id' => $nomenclature2->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson("/api/v1/products?nomenclature_id={$nomenclature1->id}");

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 3);
});

test('can filter products by shop', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop1 = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $shop2 = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->count(3)->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop1->id,
    ]);

    Product::factory()->count(2)->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop2->id,
    ]);

    $response = $this->getJson("/api/v1/products?shop_id={$shop1->id}");

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 3);
});

test('can filter products by stock availability', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->count(3)->create([
        'quantity' => 10,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->count(2)->create([
        'quantity' => 0,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?in_stock=1');

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 3);
});

test('can filter products by specs', function () {
    Cache::flush();

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product1 = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    ProductSpec::factory()->create([
        'product_id' => $product1->id,
        'spec_name' => 'Color',
        'spec_value' => 'White',
    ]);

    $product2 = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    ProductSpec::factory()->create([
        'product_id' => $product2->id,
        'spec_name' => 'Color',
        'spec_value' => 'Black',
    ]);

    $response = $this->getJson('/api/v1/products?specs[Color]=White');

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 1);
});

test('can sort products by price', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    Product::factory()->create([
        'price' => 500,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->create([
        'price' => 1500,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->create([
        'price' => 1000,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?sort_by=price&sort_order=asc');

    $response->assertOk();

    expect($response->json('data.data.0.price'))->toBe(500);
    expect($response->json('data.data.1.price'))->toBe(1000);
    expect($response->json('data.data.2.price'))->toBe(1500);
});

test('can combine multiple filters', function () {
    Cache::flush();

    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product1 = Product::factory()->create([
        'name_ru' => 'Cable Copper',
        'price' => 1500,
        'quantity' => 10,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->create([
        'name_ru' => 'Cable Aluminum',
        'price' => 2500,
        'quantity' => 5,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    Product::factory()->create([
        'name_ru' => 'Socket',
        'price' => 1200,
        'quantity' => 0,
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson('/api/v1/products?search=Cable&min_price=1000&max_price=2000&in_stock=1');

    $response->assertOk()
        ->assertJsonPath('data.pagination.total', 1)
        ->assertJsonPath('data.data.0.name_ru', 'Cable Copper');
});

test('can retrieve product by id', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson("/api/v1/products/{$product->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name_ru',
                'name_kz',
                'SKU',
                'price',
                'quantity',
                'images',
                'nomenclature',
                'shop',
                'specs',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name_ru' => $product->name_ru,
            ],
        ]);
});

test('returns 404 for non-existent product', function () {
    $response = $this->getJson('/api/v1/products/99999');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Product not found',
        ]);
});

test('product includes nomenclature and shop relationships', function () {
    $unit = Unit::factory()->create(['name_ru' => 'Метр']);
    $category = Category::factory()->create(['name_ru' => 'Кабели']);
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
        'name_ru' => 'Кабель ВВГ',
    ]);

    $city = City::factory()->create(['name' => 'Алматы']);
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
        'name' => 'Магазин стройматериалов',
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    $response = $this->getJson("/api/v1/products/{$product->id}");

    $response->assertOk()
        ->assertJsonPath('data.nomenclature.name_ru', 'Кабель ВВГ')
        ->assertJsonPath('data.shop.name', 'Магазин стройматериалов')
        ->assertJsonPath('data.shop.city.name', 'Алматы');
});

test('product includes specs relationship', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    ProductSpec::factory()->create([
        'product_id' => $product->id,
        'spec_name' => 'Цвет',
        'spec_value' => 'Белый',
    ]);

    ProductSpec::factory()->create([
        'product_id' => $product->id,
        'spec_name' => 'Материал',
        'spec_value' => 'Пластик',
    ]);

    $response = $this->getJson("/api/v1/products/{$product->id}");

    $response->assertOk()
        ->assertJsonCount(2, 'data.specs')
        ->assertJsonPath('data.specs.0.spec_name', 'Цвет')
        ->assertJsonPath('data.specs.0.spec_value', 'Белый');
});

test('products are cached', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $city = City::factory()->create();
    $company = Company::factory()->create(['city_id' => $city->id]);
    $shop = Shop::factory()->create([
        'company_id' => $company->id,
        'city_id' => $city->id,
    ]);

    $product = Product::factory()->create([
        'nomenclature_id' => $nomenclature->id,
        'shop_id' => $shop->id,
    ]);

    // First request
    $this->getJson("/api/v1/products/{$product->id}")->assertOk();

    // Delete the product
    $originalName = $product->name_ru;
    $product->delete();

    // Second request should still return cached data
    $response = $this->getJson("/api/v1/products/{$product->id}");

    $response->assertOk()
        ->assertJsonPath('data.name_ru', $originalName);
});
