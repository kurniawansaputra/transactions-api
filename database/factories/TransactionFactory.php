<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 3),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence,
            'amount' => $this->faker->numberBetween(100000, 1000000),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
