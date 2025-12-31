<?php

namespace Database\Factories;

use App\Models\Clip;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user        = \App\Models\User::factory()->create();
        $broadcaster = \App\Models\User::factory()->create();

        return [
            'submitter_id'      => $user->id,
            'submitted_at'      => $this->faker->dateTimeBetween('-1 month', 'now'),
            'twitch_clip_id'    => $this->faker->unique()->regexify('[A-Za-z0-9]{10}'),
            'title'             => $this->faker->sentence(),
            'description'       => $this->faker->optional()->paragraph(),
            'url'               => 'https://clips.twitch.tv/'.$this->faker->slug(),
            'thumbnail_url'     => $this->faker->imageUrl(320, 180),
            'duration'          => $this->faker->numberBetween(10, 300),
            'view_count'        => $this->faker->numberBetween(0, 10000),
            'created_at_twitch' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status'            => $this->faker->randomElement(['pending', 'approved', 'rejected', 'flagged']),
            'tags'              => $this->faker->randomElements(['gaming', 'twitch', 'clips', 'streaming', 'esports'], $this->faker->numberBetween(0, 3)),
            'upvotes'           => $this->faker->numberBetween(0, 1000),
            'downvotes'         => $this->faker->numberBetween(0, 100),
            'broadcaster_id'    => $broadcaster->id, // Random broadcaster user
        ];
    }

    /**
     * Indicate that the clip is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the clip is pending moderation.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the clip is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
