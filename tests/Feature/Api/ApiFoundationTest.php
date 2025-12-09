<?php

use App\Enums\UserTypeEnum;
use App\Models\City;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('api');

beforeEach(function () {
    // Create user types
    UserType::factory()->create(['id' => UserTypeEnum::Admin->value]);
    UserType::factory()->create(['id' => UserTypeEnum::RetailCustomer->value]);
    UserType::factory()->create(['id' => UserTypeEnum::CompanyCustomer->value]);
    UserType::factory()->create(['id' => UserTypeEnum::Seller->value]);

    // Create a default city for tests
    City::factory()->create();
});

test('api health check endpoint returns ok', function () {
    $response = $this->getJson('/api/v1/health');

    $response->assertOk()
        ->assertJson(['status' => 'ok']);
});

test('authenticated user can access protected endpoint', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/user');

    $response->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'email',
        ]);
});

test('unauthenticated user cannot access protected endpoint', function () {
    $response = $this->getJson('/api/v1/user');

    $response->assertUnauthorized();
});

test('api returns consistent error response for not found routes', function () {
    $response = $this->getJson('/api/v1/nonexistent');

    $response->assertNotFound()
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([
            'success' => false,
        ]);
});
