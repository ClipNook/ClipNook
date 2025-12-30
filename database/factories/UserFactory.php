<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'twitch_id'           => $this->faker->unique()->randomNumber(7),
            'twitch_login'        => $this->faker->userName(),
            'twitch_display_name' => $this->faker->name(),
            'twitch_email'        => $this->faker->email(),
        ];
    }
}
