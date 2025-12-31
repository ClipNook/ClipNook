<?php

use Laravel\Dusk\Browser;

test('cookie banner GDPR compliance', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Cookie Settings')
            ->assertSee('This website uses cookies')
            ->assertSee('Essential Cookies')
            ->assertSee('Analytics Cookies')
            ->assertSee('Marketing Cookies');

        $browser->press('Cookie Settings')
            ->waitFor('.cookie-settings-panel')
            ->assertSee('Cookie Preferences')
            ->assertPresent('input[name="essential_cookies"]')
            ->assertPresent('input[name="analytics_cookies"]')
            ->assertPresent('input[name="marketing_cookies"]');

        $browser->check('analytics_cookies')
            ->uncheck('marketing_cookies')
            ->press('Save Preferences')
            ->waitUntilMissing('.cookie-settings-panel');

        $cookies       = $browser->driver->manage()->getCookies();
        $cookieConsent = collect($cookies)->firstWhere('name', 'cookie_consent');
        expect($cookieConsent)->not->toBeNull();

        $consentData = json_decode($cookieConsent['value'], true);
        expect($consentData['analytics'])->toBeTrue();
        expect($consentData['marketing'])->toBeFalse();
        expect($consentData['essential'])->toBeTrue();
    });
});

test('cookie banner accessibility', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertPresent('[aria-label="Cookie Settings"]')
            ->assertPresent('[role="dialog"]')
            ->assertPresent('[aria-describedby]');

        $browser->keys('', '{tab}')
            ->assertFocused('[aria-label="Cookie Settings"]')
            ->keys('', '{enter}')
            ->waitFor('.cookie-settings-panel')
            ->assertFocused('.cookie-settings-panel');
    });
});

test('cookie banner remembers user preferences', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->press('Cookie Settings')
            ->waitFor('.cookie-settings-panel')
            ->check('analytics_cookies')
            ->uncheck('marketing_cookies')
            ->press('Save Preferences')
            ->waitUntilMissing('.cookie-settings-panel');

        $browser->visit('/clips')
            ->visit('/')
            ->assertDontSee('Cookie Settings');

        $cookies       = $browser->driver->manage()->getCookies();
        $cookieConsent = collect($cookies)->firstWhere('name', 'cookie_consent');
        expect($cookieConsent)->not->toBeNull();

        $consentData = json_decode($cookieConsent['value'], true);
        expect($consentData['analytics'])->toBeTrue();
        expect($consentData['marketing'])->toBeFalse();
    });
});

test('cookie banner handles consent withdrawal', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->press('Accept All Cookies')
            ->waitUntilMissing('.cookie-banner');

        $browser->press('Cookie Settings')
            ->waitFor('.cookie-settings-panel')
            ->uncheck('analytics_cookies')
            ->uncheck('marketing_cookies')
            ->press('Save Preferences')
            ->waitUntilMissing('.cookie-settings-panel');

        $cookies       = $browser->driver->manage()->getCookies();
        $cookieConsent = collect($cookies)->firstWhere('name', 'cookie_consent');
        $consentData   = json_decode($cookieConsent['value'], true);
        expect($consentData['analytics'])->toBeFalse();
        expect($consentData['marketing'])->toBeFalse();
    });
});
