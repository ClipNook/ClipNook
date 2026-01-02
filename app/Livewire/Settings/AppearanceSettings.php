<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Livewire\Settings\Concerns\ManagesUserSettings;
use Livewire\Component;

/**
 * User appearance settings component.
 */
final class AppearanceSettings extends Component
{
    use ManagesUserSettings;

    public string $theme = 'dark';

    public string $language = 'en';

    public bool $compact_mode = false;

    public bool $show_thumbnails = true;

    public int $clips_per_page = 12;

    public array $availableThemes;

    public array $availableLanguages;

    protected $except = ['availableThemes', 'availableLanguages'];

    protected string $settingsKey = 'appearance_settings';

    protected array $defaultSettings = [
        'theme'           => 'dark',
        'language'        => 'en',
        'compact_mode'    => false,
        'show_thumbnails' => true,
        'clips_per_page'  => 12,
    ];

    protected array $validationRules = [
        'theme'          => 'required|in:light,dark,auto',
        'language'       => 'required|in:en',
        'clips_per_page' => 'required|integer|min:6|max:96',
    ];

    public function mount(): void
    {
        $this->initializeUser();

        // Ensure UI arrays are properly initialized
        $this->availableThemes = [
            'light' => 'Light',
            'dark'  => 'Dark',
            'auto'  => 'Auto (System)',
        ];

        $this->availableLanguages = [
            'en' => 'English',
        ];

        $settings = $this->loadSettings($this->defaultSettings, $this->settingsKey, ['availableThemes', 'availableLanguages']);

        // Only fill the settings properties, not the UI arrays
        $this->theme = $settings['theme'] ?? $this->defaultSettings['theme'];
        $this->language = $settings['language'] ?? $this->defaultSettings['language'];
        $this->compact_mode = $settings['compact_mode'] ?? $this->defaultSettings['compact_mode'];
        $this->show_thumbnails = $settings['show_thumbnails'] ?? $this->defaultSettings['show_thumbnails'];
        $this->clips_per_page = $settings['clips_per_page'] ?? $this->defaultSettings['clips_per_page'];

        // Ensure UI arrays are properly initialized after loading settings
        $this->availableThemes = [
            'light' => 'Light',
            'dark'  => 'Dark',
            'auto'  => 'Auto (System)',
        ];

        $this->availableLanguages = [
            'en' => 'English',
        ];
    }

    public function updateAppearance(): void
    {
        $this->validate();

        $settings = [
            'theme'           => $this->theme,
            'language'        => $this->language,
            'compact_mode'    => $this->compact_mode,
            'show_thumbnails' => $this->show_thumbnails,
            'clips_per_page'  => $this->clips_per_page,
        ];

        $this->saveSettings($settings, $this->settingsKey);

        // Update user's locale
        app()->setLocale($this->language);

        $this->notifySuccess(__('settings.appearance_settings_updated'));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.settings.appearance-settings');
    }
}
