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
            'twitch_login'        => 'testuser'.$this->faker->unique()->randomNumber(3),
            'twitch_display_name' => 'Test User '.$this->faker->unique()->randomNumber(3),
            'twitch_email'        => 'test'.$this->faker->unique()->randomNumber(3).'@example.com',
            'email_verified_at'   => now(),
            'is_viewer'           => true,
            'is_cutter'           => false,
            'is_streamer'         => false,
            'is_moderator'        => false,
            'is_admin'            => false,
        ];
    }

    /**
     * Create a streamer user.
     */
    public function streamer(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_streamer' => true,
        ]);
    }

    /**
     * Create a moderator user.
     */
    public function moderator(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_moderator' => true,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
