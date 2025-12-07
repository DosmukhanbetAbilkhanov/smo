<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserType>
 */
class UserTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_ru' => $this->faker->randomElement(['Админ', 'Покупатель', 'Компания', 'Продавец']),
            'name_kz' => $this->faker->randomElement(['Админ', 'Сатып алушы', 'Компания', 'Сатушы']),
        ];
    }
}
