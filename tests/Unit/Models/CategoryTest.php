<?php

use App\Models\Category;
use App\Models\Nomenclature;
use App\Models\Spec;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
});

it('can have a parent category', function () {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);

    expect($child->parent)
        ->toBeInstanceOf(Category::class)
        ->id->toBe($parent->id);
});

it('can be a root category with no parent', function () {
    expect($this->category->parent)->toBeNull();
});

it('can have multiple children categories', function () {
    $children = Category::factory()->count(3)->create(['parent_id' => $this->category->id]);

    expect($this->category->children)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Category::class);

    expect($this->category->children->pluck('id')->toArray())
        ->toBe($children->pluck('id')->toArray());
});

it('can have no children', function () {
    expect($this->category->children)->toBeEmpty();
});

it('can have multiple specs with pivot data', function () {
    $specs = Spec::factory()->count(3)->create();

    $this->category->specs()->attach($specs[0]->id, ['is_required' => true]);
    $this->category->specs()->attach($specs[1]->id, ['is_required' => false]);
    $this->category->specs()->attach($specs[2]->id, ['is_required' => true]);

    expect($this->category->specs)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Spec::class);

    expect($this->category->specs[0]->pivot->is_required)->toBe(1);
    expect($this->category->specs[1]->pivot->is_required)->toBe(0);
});

it('can have nomenclatures', function () {
    $nomenclatures = Nomenclature::factory()->count(2)->create(['category_id' => $this->category->id]);

    expect($this->category->nomenclatures)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Nomenclature::class);
});

it('has proper fillable attributes', function () {
    $fillable = ['name_ru', 'name_kz', 'parent_id', 'icon', 'slug'];

    expect($this->category->getFillable())->toBe($fillable);
});

it('supports bilingual names', function () {
    $category = Category::factory()->create([
        'name_ru' => 'Категория',
        'name_kz' => 'Санат',
    ]);

    expect($category->name_ru)->toBe('Категория')
        ->and($category->name_kz)->toBe('Санат');
});

it('has a unique slug', function () {
    $category = Category::factory()->create(['slug' => 'test-slug']);

    expect($category->slug)->toBe('test-slug');
});
