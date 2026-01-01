<?php

declare(strict_types=1);

namespace App\Models\Concerns\Clip;

/**
 * Handles clip voting functionality.
 */
trait HasVoting
{
    /**
     * Get the upvotes count.
     */
    public function getUpvotesCountAttribute(): int
    {
        return $this->votes()->where('vote_type', 'upvote')->count();
    }

    /**
     * Get the downvotes count.
     */
    public function getDownvotesCountAttribute(): int
    {
        return $this->votes()->where('vote_type', 'downvote')->count();
    }

    /**
     * Get the total score (upvotes - downvotes).
     */
    public function getScoreAttribute(): int
    {
        return $this->upvotes - $this->downvotes;
    }

    /**
     * Check if clip is popular based on score and views.
     */
    public function isPopular(): bool
    {
        return $this->score > config('constants.limits.clip_score_threshold', 10) &&
               $this->view_count > config('constants.limits.clip_view_threshold', 100);
    }

    /**
     * Check if clip is trending (recent with good score).
     */
    public function isTrending(): bool
    {
        return $this->created_at->diffInHours(now()) <= 24 && $this->score > 5;
    }

    /**
     * Upvote the clip.
     */
    public function upvote(\App\Models\User $user): bool
    {
        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'upvote') {
                // Already upvoted, remove vote
                $existingVote->delete();
                $this->decrement('upvotes');

                return false;
            } else {
                // Change from downvote to upvote
                $existingVote->update(['vote_type' => 'upvote']);
                $this->increment('upvotes');
                $this->decrement('downvotes');

                return true;
            }
        }

        // New upvote
        $this->votes()->create([
            'user_id'   => $user->id,
            'vote_type' => 'upvote',
        ]);
        $this->increment('upvotes');

        return true;
    }

    /**
     * Downvote the clip.
     */
    public function downvote(\App\Models\User $user): bool
    {
        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'downvote') {
                // Already downvoted, remove vote
                $existingVote->delete();
                $this->decrement('downvotes');

                return false;
            } else {
                // Change from upvote to downvote
                $existingVote->update(['vote_type' => 'downvote']);
                $this->decrement('upvotes');
                $this->increment('downvotes');

                return true;
            }
        }

        // New downvote
        $this->votes()->create([
            'user_id'   => $user->id,
            'vote_type' => 'downvote',
        ]);
        $this->increment('downvotes');

        return true;
    }

    /**
     * Get vote type for a specific user.
     */
    public function getVoteTypeForUser(\App\Models\User $user): ?string
    {
        $vote = $this->votes()->where('user_id', $user->id)->first();

        return $vote?->vote_type;
    }

    /**
     * Remove vote from user.
     */
    public function removeVote(\App\Models\User $user): void
    {
        $this->votes()->where('user_id', $user->id)->delete();
    }

    /**
     * Toggle vote for user.
     */
    public function toggleVote(\App\Models\User $user, string $type): void
    {
        if ($type === 'upvote') {
            $this->upvote($user);
        } elseif ($type === 'downvote') {
            $this->downvote($user);
        }
    }
}
