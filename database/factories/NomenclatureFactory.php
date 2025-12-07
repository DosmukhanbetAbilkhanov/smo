<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nomenclature>
 */
class NomenclatureFactory extends Factory
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
            'unit_id' => Unit::inRandomOrder()->first()->id ?? Unit::factory(),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'description_ru' => $this->faker->sentence(),
            'description_kz' => $this->faker->sentence(),
            'GTIN' => $this->faker->ean13(),
            'NTIN' => null,
            'brandname' => $this->faker->company(),
        ];
    }
}
