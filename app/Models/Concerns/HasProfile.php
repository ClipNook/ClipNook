<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Cache;

trait HasProfile
{
    /**
     * Calculate profile completion percentage.
     */
    public function profileCompletion(): int
    {
        return Cache::remember(
            "user.{$this->id}.profile_completion",
            now()->addHours(1), // Cache for 1 hour
            function () {
                $steps = [
                    'profile'     => $this->isProfileComplete(),
                    'avatar'      => ! $this->isAvatarDisabled(),
                    'roles'       => $this->isStreamer() || $this->isCutter(),
                    'preferences' => $this->hasPreferencesSet(),
                ];

                $completed = count(array_filter($steps));

                return (int) round(($completed / count($steps)) * 100);
            }
        );
    }

    /**
     * Determine if base profile is complete.
     */
    public function isProfileComplete(): bool
    {
        return ! empty($this->display_name)
            && ! empty($this->twitch_email)
            && filter_var($this->twitch_email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Whether user has set timezone & locale.
     */
    public function hasPreferencesSet(): bool
    {
        return ! empty($this->timezone) && ! empty($this->locale);
    }

    /**
     * List completed profile step keys.
     *
     * @return array<int, string>
     */
    public function completedProfileSteps(): array
    {
        $steps = [];

        if ($this->isProfileComplete()) {
            $steps[] = 'profile';
        }

        if (! $this->isAvatarDisabled()) {
            $steps[] = 'avatar';
        }

        if ($this->isStreamer() || $this->isCutter()) {
            $steps[] = 'roles';
        }

        if ($this->hasPreferencesSet()) {
            $steps[] = 'preferences';
        }

        return $steps;
    }

    /**
     * Get missing profile steps.
     *
     * @return array<int, string>
     */
    public function missingProfileSteps(): array
    {
        $allSteps  = ['profile', 'avatar', 'roles', 'preferences'];
        $completed = $this->completedProfileSteps();

        return array_diff($allSteps, $completed);
    }

    /**
     * Human readable profile updated at or 'never'.
     */
    public function profileUpdatedAt(): string
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : __('ui.never');
    }

    /**
     * Check if profile is fully complete.
     */
    public function isProfileFullyComplete(): bool
    {
        return $this->profileCompletion() === 100;
    }

    /**
     * Clear profile completion cache.
     */
    public function clearProfileCompletionCache(): void
    {
        Cache::forget("user.{$this->id}.profile_completion");
    }
}
