<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Cache;

trait HasPreferences
{
    /**
     * Get the user's preferred theme.
     */
    public function getThemeAttribute(): string
    {
        return $this->theme_preference ?? 'system';
    }

    /**
     * Get the user's preferred locale.
     */
    public function getLocaleAttribute(): string
    {
        return $this->locale ?? config('app.locale', 'en');
    }

    /**
     * Get the user's preferred timezone.
     */
    public function getTimezoneAttribute(): string
    {
        return $this->timezone ?? config('app.timezone', 'UTC');
    }

    /**
     * Determine if user prefers dark mode.
     */
    public function prefersDarkMode(): bool
    {
        $theme = $this->theme_preference;

        if ($theme === 'dark') {
            return true;
        }

        if ($theme === 'light') {
            return false;
        }

        // System preference - check via JavaScript or default to false
        return false;
    }

    /**
     * Determine if user prefers light mode.
     */
    public function prefersLightMode(): bool
    {
        return ! $this->prefersDarkMode();
    }

    /**
     * Get theme class for Tailwind CSS.
     */
    public function themeClass(): string
    {
        return $this->prefersDarkMode() ? 'dark' : '';
    }

    /**
     * Update user preferences.
     *
     * @param  array<string, mixed>  $preferences
     */
    public function updatePreferences(array $preferences): bool
    {
        $validKeys = ['theme_preference', 'locale', 'timezone'];

        $updates = array_intersect_key($preferences, array_flip($validKeys));

        if (empty($updates)) {
            return false;
        }

        $this->fill($updates);

        return $this->save();
    }

    /**
     * Clear user preferences cache.
     */
    public function clearPreferencesCache(): void
    {
        Cache::forget("user.{$this->id}.preferences");
    }

    /**
     * Get cached preferences.
     *
     * @return array<string, mixed>
     */
    public function getCachedPreferences(): array
    {
        return Cache::remember(
            "user.{$this->id}.preferences",
            now()->addHours(24),
            fn () => [
                'theme'    => $this->theme_preference,
                'locale'   => $this->locale,
                'timezone' => $this->timezone,
            ]
        );
    }
}
