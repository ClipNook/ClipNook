<?php

namespace Database\Factories;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClipComment>
 */
class ClipCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'clip_id'    => Clip::factory(),
            'user_id'    => User::factory(),
            'content'    => fake()->realText(200),
            'parent_id'  => null,
            'is_deleted' => false,
            'deleted_at' => null,
        ];
    }

    /**
     * Indicate that the comment is a reply to another comment.
     */
    public function reply(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Indicate that the comment is deleted.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);
    }
}
