<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // A string of 1 to 3 words in Title Case.
            'name' => Str::title(
                implode(" ", fake()->randomElements(
                    fake()->words(3),
                    null
                ))
            )
        ];
    }
}
