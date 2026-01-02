<?php

declare(strict_types=1);

namespace App\Livewire\Settings\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function array_filter;
use function array_merge;
use function is_array;
use function is_scalar;

/**
 * Trait for managing user settings in Livewire components.
 *
 * This trait provides common functionality for settings components
 * like appearance and notification settings.
 */
trait ManagesUserSettings
{
    public User $user;

    /**
     * Initialize the user from authentication.
     */
    protected function initializeUser(): void
    {
        $this->user = Auth::user();
    }

    /**
     * Load settings from user with defaults.
     *
     * @param array  $defaults    Default values for settings
     * @param string $settingsKey The key in user model to load from
     * @param array  $excludeKeys Keys to exclude from loading
     */
    protected function loadSettings(array $defaults, string $settingsKey, array $excludeKeys = []): array
    {
        $settings = $this->user->{$settingsKey} ?? [];

        // Ensure settings is an array
        if (! is_array($settings)) {
            $settings = [];
        }

        // Filter out non-scalar values for security and exclude specified keys
        $settings = array_filter($settings, static fn ($value, $key): bool => is_scalar($value) && ! in_array($key, $excludeKeys, true), ARRAY_FILTER_USE_BOTH);

        return array_merge($defaults, $settings);
    }

    /**
     * Save settings to user model.
     *
     * @param array  $settings    Settings to save
     * @param string $settingsKey The key in user model to save to
     */
    protected function saveSettings(array $settings, string $settingsKey): void
    {
        $this->user->update([
            $settingsKey => $settings,
        ]);
    }

    /**
     * Send success notification.
     */
    protected function notifySuccess(string $message): void
    {
        $this->dispatch('notify', type: 'success', message: $message);
    }

    /**
     * Send error notification.
     */
    protected function notifyError(string $message): void
    {
        $this->dispatch('notify', type: 'error', message: $message);
    }
}
