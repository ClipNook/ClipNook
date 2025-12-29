<?php

namespace App\Models\Concerns;

trait HasTimestamps
{
    /**
     * Get formatted created at date.
     */
    public function createdAtFormatted(): string
    {
        return $this->created_at->format('M j, Y \a\t g:i A');
    }

    /**
     * Get formatted updated at date.
     */
    public function updatedAtFormatted(): string
    {
        return $this->updated_at->format('M j, Y \a\t g:i A');
    }

    /**
     * Get human readable created at.
     */
    public function createdAtHuman(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get human readable updated at.
     */
    public function updatedAtHuman(): string
    {
        return $this->updated_at->diffForHumans();
    }

    /**
     * Check if model was created today.
     */
    public function wasCreatedToday(): bool
    {
        return $this->created_at->isToday();
    }

    /**
     * Check if model was updated today.
     */
    public function wasUpdatedToday(): bool
    {
        return $this->updated_at->isToday();
    }

    /**
     * Check if model was created this week.
     */
    public function wasCreatedThisWeek(): bool
    {
        return $this->created_at->isCurrentWeek();
    }

    /**
     * Check if model was updated this week.
     */
    public function wasUpdatedThisWeek(): bool
    {
        return $this->updated_at->isCurrentWeek();
    }

    /**
     * Get days since creation.
     */
    public function daysSinceCreation(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get days since last update.
     */
    public function daysSinceUpdate(): int
    {
        return $this->updated_at->diffInDays(now());
    }
}
