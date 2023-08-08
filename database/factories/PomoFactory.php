<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pomo>
 */
class PomoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'todo_id' => $this->faker->uuid(),
            'notes' => $this->faker->text(),
            'pomo_start' => $this->faker->unixTime(),
            'pomo_end' => $this->faker->unixTime(),
        ];
    }
}
