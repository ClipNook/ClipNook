<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('complete clip submission flow', function () {
    $user = User::factory()->create([
        'twitch_id'           => 'test_user_123',
        'twitch_display_name' => 'TestUser',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/clips/submit')
            ->assertSee('Submit Clip');

        $browser->type('twitchClipId', 'DreamyComfortableKimchiBCWarrior')
            ->press('Check Clip')
            ->waitForText('Clip Information')
            ->assertSee('DreamyComfortableKimchiBCWarrior');

        $browser->assertSee('Data Protection Notice')
            ->press('Load Player')
            ->waitFor('.player-iframe')
            ->assertPresent('.player-iframe');

        $browser->press('Submit Clip')
            ->waitForText('Submission successful')
            ->assertSee('Submission successful');

        $this->assertDatabaseHas('clips', [
            'user_id'        => $user->id,
            'twitch_clip_id' => 'DreamyComfortableKimchiBCWarrior',
        ]);
    });
});

test('handles clip submission errors gracefully', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/clips/submit')
            ->type('twitchClipId', 'NonExistentClip123')
            ->press('Check Clip')
            ->waitForText('not found')
            ->assertSee('not found');
    });
});

test('enforces rate limiting on clip submissions', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user);

        for ($i = 0; $i < 15; $i++) {
            $browser->visit('/clips/submit')
                ->type('twitchClipId', 'TestClip'.$i)
                ->press('Check Clip')
                ->waitForText('Clip Information')
                ->press('Load Player')
                ->press('Submit Clip');
        }

        $browser->visit('/clips/submit')
            ->type('twitchClipId', 'RateLimitedClip')
            ->press('Check Clip')
            ->waitForText('Clip Information')
            ->press('Load Player')
            ->press('Submit Clip')
            ->waitForText('rate limit')
            ->assertSee('rate limit');
    });
});

test('cookie banner functionality', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Cookie Settings')
            ->press('Accept All Cookies')
            ->waitUntilMissing('.cookie-banner');

        $cookies       = $browser->driver->manage()->getCookies();
        $cookieConsent = collect($cookies)->firstWhere('name', 'cookie_consent');
        expect($cookieConsent)->not->toBeNull();
    });
});

test('responsive design on mobile devices', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->resize(375, 667)
            ->visit('/clips/submit')
            ->assertSee('Submit Clip')
            ->type('twitchClipId', 'MobileTestClip')
            ->press('Check Clip')
            ->waitForText('Clip Information')
            ->assertSee('MobileTestClip');
    });
});
