<?php

use App\Models\Category;
use App\Models\Nomenclature;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class)->group('api');

beforeEach(function () {
    // Clear cache before each test
    Cache::flush();
});

test('can retrieve paginated nomenclatures', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    Nomenclature::factory()->count(20)->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson('/api/v1/nomenclatures');

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

test('can retrieve specific page of nomenclatures', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    Nomenclature::factory()->count(20)->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson('/api/v1/nomenclatures?page=2');

    $response->assertOk()
        ->assertJsonPath('data.pagination.current_page', 2)
        ->assertJsonCount(5, 'data.data');
});

test('can retrieve nomenclature by id', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson("/api/v1/nomenclatures/{$nomenclature->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
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
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $nomenclature->id,
                'name_ru' => $nomenclature->name_ru,
            ],
        ]);
});

test('returns 404 for non-existent nomenclature', function () {
    $response = $this->getJson('/api/v1/nomenclatures/99999');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Nomenclature not found',
        ]);
});

test('nomenclature includes unit and category relationships', function () {
    $unit = Unit::factory()->create(['name_ru' => 'Метр']);
    $category = Category::factory()->create(['name_ru' => 'Кабели']);
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson("/api/v1/nomenclatures/{$nomenclature->id}");

    $response->assertOk()
        ->assertJsonPath('data.unit.name_ru', 'Метр')
        ->assertJsonPath('data.category.name_ru', 'Кабели');
});

test('nomenclatures are cached', function () {
    $unit = Unit::factory()->create();
    $category = Category::factory()->create();
    $nomenclature = Nomenclature::factory()->create([
        'unit_id' => $unit->id,
        'category_id' => $category->id,
    ]);

    // First request
    $this->getJson("/api/v1/nomenclatures/{$nomenclature->id}")->assertOk();

    // Delete the nomenclature
    $originalName = $nomenclature->name_ru;
    $nomenclature->delete();

    // Second request should still return cached data
    $response = $this->getJson("/api/v1/nomenclatures/{$nomenclature->id}");

    $response->assertOk()
        ->assertJsonPath('data.name_ru', $originalName);
});
