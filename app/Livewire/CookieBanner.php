<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class CookieBanner extends Component
{
    public bool $showBanner = false;

    public bool $necessaryConsent = true; // Always true, cannot be disabled

    protected $listeners = [
        'showCookieBanner' => 'show',
    ];

    public function mount()
    {
        $this->checkConsentStatus();
    }

    public function show()
    {
        $this->showBanner = true;
    }

    public function accept()
    {
        $this->saveConsents();
        $this->showBanner = false;

        $this->dispatch('cookies-accepted', [
            'necessary' => true,
        ]);
    }

    public function openSettings()
    {
        $this->showBanner = true;
    }

    private function checkConsentStatus()
    {
        $consent = Cookie::get('cookie_consent');

        if (! $consent) {
            $this->showBanner = true;

            return;
        }

        $consentData = json_decode($consent, true);

        if (! $consentData) {
            $this->showBanner = true;

            return;
        }

        // Only check if consent was given, no need to track analytics/marketing since we don't use them
    }

    private function saveConsents()
    {
        $consentData = [
            'necessary' => true,
            'timestamp' => now()->toISOString(),
            'version'   => '1.0',
        ];

        Cookie::queue('cookie_consent', json_encode($consentData), 365 * 24 * 60); // 1 year
    }

    public function render()
    {
        return view('livewire.cookie-banner');
    }
}
