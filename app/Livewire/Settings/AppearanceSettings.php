<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function __;
use function app;
use function array_filter;
use function array_keys;
use function array_merge;
use function implode;
use function is_array;
use function is_scalar;
use function view;

/**
 * User appearance settings component.
 */
final class AppearanceSettings extends Component
{
    public User $user;

    public string $theme = 'dark';

    public string $language = 'en';

    public bool $compact_mode = false;

    public bool $show_thumbnails = true;

    public int $clips_per_page = 12;

    public array $availableThemes = [
        'light' => 'Light',
        'dark'  => 'Dark',
        'auto'  => 'Auto (System)',
    ];

    public array $availableLanguages = [
        'en' => 'English',
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
        $settings   = $this->user->appearance_settings ?? [];

        // Ensure settings is an array
        if (! is_array($settings)) {
            $settings = [];
        }

        $settings = array_filter($settings, static fn ($value): bool => is_scalar($value));

        $this->fill(array_merge([
            'theme'           => 'dark',
            'language'        => 'en',
            'compact_mode'    => false,
            'show_thumbnails' => true,
            'clips_per_page'  => 12,
        ], $settings));
    }

    public function updateAppearance(): void
    {
        $this->validate([
            'theme'          => 'required|in:'.implode(',', array_keys($this->availableThemes)),
            'language'       => 'required|in:'.implode(',', array_keys($this->availableLanguages)),
            'clips_per_page' => 'required|integer|min:6|max:96',
        ]);

        $settings = [
            'theme'           => $this->theme,
            'language'        => $this->language,
            'compact_mode'    => $this->compact_mode,
            'show_thumbnails' => $this->show_thumbnails,
            'clips_per_page'  => $this->clips_per_page,
        ];

        $this->user->update([
            'appearance_settings' => $settings,
        ]);

        // Update user's locale
        app()->setLocale($this->language);

        $this->dispatch('notify', type: 'success', message: __('settings.appearance_settings_updated'));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.appearance-settings');
    }
}
