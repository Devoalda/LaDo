<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'due_start' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'due_end' => fake()->dateTimeBetween('+1 week', '+2 week'),
            'user_id' => fake()->uuid(),
            'completed_at' => null,
            'created_at' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'updated_at' => fake()->dateTimeBetween('-1 week', '+1 week'),

        ];
    }
}
