<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()->id ?? Company::factory(),
            'city_id' => City::inRandomOrder()->first()->id ?? City::factory(),
            'name' => $this->faker->company().' Shop',
            'address' => $this->faker->address(),
            'min_order_amount' => $this->faker->randomFloat(2, 2000, 5000),
        ];
    }
}
