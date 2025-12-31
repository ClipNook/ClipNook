<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('responsive design works on all screen sizes', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user);

        $browser->resize(1920, 1080)
            ->visit('/clips/submit')
            ->assertSee('Submit Clip')
            ->assertVisible('.card')
            ->assertPresent('.btn-primary');

        $browser->resize(768, 1024)
            ->visit('/clips/submit')
            ->assertSee('Submit Clip')
            ->assertVisible('.card')
            ->assertPresent('.btn-primary');

        $browser->resize(375, 667)
            ->visit('/clips/submit')
            ->assertSee('Submit Clip')
            ->assertVisible('.card')
            ->assertPresent('.btn-primary');

        $browser->resize(320, 568)
            ->visit('/clips/submit')
            ->assertSee('Submit Clip')
            ->assertVisible('.card')
            ->assertPresent('.btn-primary');
    });
});

test('navigation works correctly', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/')
            ->assertSee('ClipNook')
            ->clickLink('Submit Clip')
            ->waitForLocation('/clips/submit')
            ->assertPathIs('/clips/submit')
            ->assertSee('Submit Clip');

        $browser->back()
            ->assertPathIs('/')
            ->assertSee('ClipNook');
    });
});

test('forms handle validation errors properly', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/clips/submit')
            ->press('Check Clip')
            ->waitForText('required')
            ->assertSee('required');

        $browser->type('twitchClipId', 'invalid-clip-id-123')
            ->press('Check Clip')
            ->waitForText('not found')
            ->assertSee('not found');
    });
});

test('loading states are displayed correctly', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/clips/submit')
            ->type('twitchClipId', 'DreamyComfortableKimchiBCWarrior')
            ->press('Check Clip')
            ->waitFor('.loading')
            ->assertPresent('.loading')
            ->waitUntilMissing('.loading')
            ->assertSee('Clip Information');
    });
});

test('error handling displays user-friendly messages', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/clips/submit')
            ->type('twitchClipId', 'NonExistentClip123456789')
            ->press('Check Clip')
            ->waitForText('error')
            ->assertSee('error')
            ->assertDontSee('Exception')
            ->assertDontSee('Stack trace');
    });
});

test('accessibility features work correctly', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertPresent('[aria-label]')
            ->assertPresent('[role="button"]')
            ->assertPresent('[tabindex]');

        $browser->keys('', '{tab}')
            ->assertFocused('button, a, input, select, textarea')
            ->keys('', '{tab}')
            ->assertFocused('button, a, input, select, textarea');
    });
});

test('dark mode toggle works', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertDontSee('dark')
            ->press('Toggle Dark Mode')
            ->waitFor('.dark')
            ->assertPresent('.dark');

        $browser->refresh()
            ->assertPresent('.dark');
    });
});
