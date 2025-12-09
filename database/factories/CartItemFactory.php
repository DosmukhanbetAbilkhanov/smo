<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = \App\Models\Product::factory()->create();

        return [
            'cart_id' => \App\Models\Cart::factory(),
            'product_id' => $product->id,
            'quantity' => fake()->numberBetween(1, 10),
            'price' => $product->price,
        ];
    }
}
