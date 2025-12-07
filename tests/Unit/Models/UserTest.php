<?php

use App\Models\City;
use App\Models\Company;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('has a company relationship', function () {
    $company = Company::factory()->create(['user_id' => $this->user->id]);

    expect($this->user->company)
        ->toBeInstanceOf(Company::class)
        ->id->toBe($company->id);
});

it('can have no company', function () {
    expect($this->user->company)->toBeNull();
});

it('belongs to a city', function () {
    $city = City::factory()->create();
    $user = User::factory()->create(['city_id' => $city->id]);

    expect($user->city)
        ->toBeInstanceOf(City::class)
        ->id->toBe($city->id);
});

it('belongs to a user type', function () {
    $userType = UserType::factory()->create();
    $user = User::factory()->create(['type_id' => $userType->id]);

    expect($user->type)
        ->toBeInstanceOf(UserType::class)
        ->id->toBe($userType->id);
});

it('can be active or inactive', function () {
    $activeUser = User::factory()->create(['active' => true]);
    $inactiveUser = User::factory()->create(['active' => false]);

    expect($activeUser->active)->toBeTrue()
        ->and($inactiveUser->active)->toBeFalse();
});

it('has proper fillable attributes', function () {
    $fillable = [
        'type_id', 'city_id', 'name', 'email', 'email_verified_at',
        'phone', 'phone_verified_at', 'password', 'active',
    ];

    expect($this->user->getFillable())->toBe($fillable);
});

it('has proper hidden attributes', function () {
    $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    expect($this->user->getHidden())->toBe($hidden);
});
