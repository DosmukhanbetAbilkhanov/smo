<?php

use App\Models\Company;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->product = Product::factory()->create();
});

it('belongs to a shop', function () {
    $shop = Shop::factory()->create();
    $product = Product::factory()->create(['shop_id' => $shop->id]);

    expect($product->shop)
        ->toBeInstanceOf(Shop::class)
        ->id->toBe($shop->id);
});

it('can access company through shop', function () {
    $company = Company::factory()->create();
    $shop = Shop::factory()->create(['company_id' => $company->id]);
    $product = Product::factory()->create(['shop_id' => $shop->id]);

    expect($product->shop->company)
        ->toBeInstanceOf(Company::class)
        ->id->toBe($company->id);
});

it('belongs to a nomenclature', function () {
    $nomenclature = Nomenclature::factory()->create();
    $product = Product::factory()->create(['nomenclature_id' => $nomenclature->id]);

    expect($product->nomenclature)
        ->toBeInstanceOf(Nomenclature::class)
        ->id->toBe($nomenclature->id);
});

it('can have multiple product specs', function () {
    $specs = ProductSpec::factory()->count(3)->create(['product_id' => $this->product->id]);

    expect($this->product->specs)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(ProductSpec::class);
});

it('has proper fillable attributes', function () {
    $fillable = [
        'name_ru', 'name_kz', 'nomenclature_id', 'shop_id', 'price', 'quantity', 'images', 'is_active',
    ];

    expect($this->product->getFillable())->toBe($fillable);
});

it('supports bilingual names', function () {
    $product = Product::factory()->create([
        'name_ru' => 'Товар',
        'name_kz' => 'Тауар',
    ]);

    expect($product->name_ru)->toBe('Товар')
        ->and($product->name_kz)->toBe('Тауар');
});

it('has price and quantity attributes', function () {
    $product = Product::factory()->create([
        'price' => 1500.50,
        'quantity' => 100,
    ]);

    expect($product->price)->toBe(1500.50)
        ->and($product->quantity)->toBe(100);
});
