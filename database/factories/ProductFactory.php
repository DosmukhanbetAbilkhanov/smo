<?php

namespace Database\Factories;

use App\Models\Nomenclature;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_ru' => $this->faker->words(2, true),
            'name_kz' => $this->faker->words(2, true),
            'nomenclature_id' => Nomenclature::inRandomOrder()->first()->id ?? Nomenclature::factory(),
            'shop_id' => Shop::inRandomOrder()->first()->id ?? Shop::factory(),
            'SKU' => $this->faker->ean8(),
            'price' => $this->faker->randomFloat(2, 100, 50000),
            'quantity' => $this->faker->numberBetween(1, 500),
        ];
    }
}
