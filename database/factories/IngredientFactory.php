<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'stock' => $stock = fake()->numberBetween(1000, 100000), // 1kg to 100kg
            'threshold' => (int) ($stock / fake()->numberBetween(2, 10)), // 50% to 10% of stock
        ];
    }
}
