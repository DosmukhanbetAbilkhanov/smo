<?php

use App\Models\Category;
use App\Models\Nomenclature;
use App\Models\Spec;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class)->group('api');

beforeEach(function () {
    // Clear cache before each test
    Cache::flush();
});

test('can retrieve all parent categories', function () {
    $parentCategory = Category::factory()->create(['parent_id' => null]);
    $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name_ru',
                    'name_kz',
                    'slug',
                    'icon',
                    'parent_id',
                    'specs',
                    'children',
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonCount(1, 'data');
});

test('categories endpoint includes specs and children', function () {
    $category = Category::factory()->create(['parent_id' => null]);
    $spec = Spec::factory()->create();
    $category->specs()->attach($spec->id, ['is_required' => true]);
    Category::factory()->create(['parent_id' => $category->id]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertOk()
        ->assertJsonPath('data.0.specs.0.id', $spec->id)
        ->assertJsonPath('data.0.specs.0.is_required', true)
        ->assertJsonCount(1, 'data.0.children');
});

test('can retrieve a category by slug', function () {
    $category = Category::factory()->create(['slug' => 'test-category']);

    $response = $this->getJson('/api/v1/categories/test-category');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name_ru',
                'name_kz',
                'slug',
                'icon',
                'parent_id',
                'specs',
                'children',
                'nomenclatures_count',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'slug' => 'test-category',
            ],
        ]);
});

test('returns 404 for non-existent category slug', function () {
    $response = $this->getJson('/api/v1/categories/non-existent-slug');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Category not found',
        ]);
});

test('can retrieve nomenclatures for a category', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create(['slug' => 'test-category']);
    $nomenclatures = Nomenclature::factory()->count(3)->create([
        'category_id' => $category->id,
        'unit_id' => $unit->id,
    ]);

    $response = $this->getJson('/api/v1/categories/test-category/nomenclatures');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name_ru',
                    'name_kz',
                    'description_ru',
                    'description_kz',
                    'SKU',
                    'GTIN',
                    'NTIN',
                    'brandname',
                    'status',
                    'unit',
                    'category',
                    'approved_by',
                    'approved_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonCount(3, 'data');
});

test('returns 404 when retrieving nomenclatures for non-existent category', function () {
    $response = $this->getJson('/api/v1/categories/non-existent-slug/nomenclatures');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Category not found',
        ]);
});

test('categories are cached', function () {
    Category::factory()->create(['parent_id' => null]);

    // First request
    $this->getJson('/api/v1/categories')->assertOk();

    // Delete all categories
    Category::query()->delete();

    // Second request should still return cached data
    $response = $this->getJson('/api/v1/categories');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

test('category show is cached', function () {
    $category = Category::factory()->create(['slug' => 'test-category']);

    // First request
    $this->getJson('/api/v1/categories/test-category')->assertOk();

    // Delete the category
    $category->delete();

    // Second request should still return cached data
    $response = $this->getJson('/api/v1/categories/test-category');

    $response->assertOk()
        ->assertJsonPath('data.slug', 'test-category');
});
