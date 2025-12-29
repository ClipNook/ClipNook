<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $definition = [
            'remember_token'    => Str::random(10),

            // Role flags defaults
            'is_viewer'         => false,
            'is_cutter'         => false,
            'is_streamer'       => false,
            'is_moderator'      => false,
            'is_admin'          => false,
        ];

        // Only add optional fields if the users table contains them
        if (Schema::hasColumn('users', 'name')) {
            $definition['name'] = fake()->name();
        }

        if (Schema::hasColumn('users', 'email')) {
            $definition['email']             = fake()->unique()->safeEmail();
            $definition['email_verified_at'] = now();
        }

        if (Schema::hasColumn('users', 'password')) {
            $definition['password'] = static::$password ??= Hash::make('password');
        }

        return $definition;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
