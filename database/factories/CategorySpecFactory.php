<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Spec;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategorySpec>
 */
class CategorySpecFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'spec_id' => Spec::inRandomOrder()->first()->id ?? Spec::factory(),
            'is_required' => $this->faker->boolean(40),
        ];
    }
}
