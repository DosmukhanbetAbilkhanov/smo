<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 10000);

        return [
            'order_number' => 'ORD-'.date('Y').'-'.fake()->unique()->numberBetween(10000, 99999),
            'user_id' => \App\Models\User::factory(),
            'shop_id' => \App\Models\Shop::factory(),
            'status' => fake()->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered']),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'delivery_address' => fake()->streetAddress(),
            'delivery_entry' => fake()->optional()->numberBetween(1, 10),
            'delivery_floor' => fake()->optional()->numberBetween(1, 20),
            'delivery_apartment' => fake()->optional()->numberBetween(1, 200),
            'delivery_intercom' => fake()->optional()->numerify('###'),
            'delivery_city_id' => \App\Models\City::factory(),
            'contact_phone' => fake()->phoneNumber(),
            'delivery_notes' => fake()->optional()->sentence(),
        ];
    }
}
