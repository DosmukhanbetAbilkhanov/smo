<?php

use App\Models\City;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('auth');

beforeEach(function () {
    // Create required user types
    UserType::factory()->create(['id' => 1, 'name_ru' => 'Админ', 'name_kz' => 'Админ']);
    UserType::factory()->create(['id' => 2, 'name_ru' => 'Покупатель', 'name_kz' => 'Сатып алушы']);
    UserType::factory()->create(['id' => 3, 'name_ru' => 'Компания', 'name_kz' => 'Компания']);
    UserType::factory()->create(['id' => 4, 'name_ru' => 'Продавец', 'name_kz' => 'Сатушы']);

    $this->city = City::factory()->create();
});

it('can login with email and password', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('can login with phone and password', function () {
    $user = User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
        'phone' => '+1234567890',
        'password' => 'password',
    ]);

    $response = $this->post('/login', [
        'phone' => '+1234567890',
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('fails to login with incorrect email', function () {
    User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response = $this->post('/login', [
        'email' => 'wrong@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

it('fails to login with incorrect password', function () {
    User::factory()->create([
        'type_id' => 2,
        'city_id' => $this->city->id,
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

it('requires either email or phone', function () {
    $response = $this->post('/login', [
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('requires password', function () {
    $response = $this->post('/login', [
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('validates email format when provided', function () {
    $response = $this->post('/login', [
        'email' => 'not-an-email',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
});
