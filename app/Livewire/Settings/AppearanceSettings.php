<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;

use function app;
use function array_keys;
use function auth;
use function implode;
use function session;
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
        $this->user = auth()->user();
        $this->fill($this->user->appearance_settings ?? [
            'theme'           => 'dark',
            'language'        => 'en',
            'compact_mode'    => false,
            'show_thumbnails' => true,
            'clips_per_page'  => 12,
        ]);
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

        session()->flash('message', 'Appearance settings updated successfully.');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.appearance-settings');
    }
}
