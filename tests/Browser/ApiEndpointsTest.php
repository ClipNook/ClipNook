<?php

use App\Models\Clip;
use App\Models\User;
use Laravel\Dusk\Browser;

test('API endpoints return correct data', function () {
    $user  = User::factory()->create();
    $clips = Clip::factory()->count(5)->create([
        'user_id'     => $user->id,
        'is_featured' => false,
    ]);

    $featuredClip = Clip::factory()->create([
        'user_id'     => $user->id,
        'is_featured' => true,
    ]);

    $this->browse(function (Browser $browser) use ($featuredClip) {
        $browser->visit('/api/clips/featured')
            ->assertJson([
                'data' => [
                    [
                        'id'             => $featuredClip->id,
                        'twitch_clip_id' => $featuredClip->twitch_clip_id,
                        'is_featured'    => true,
                    ],
                ],
            ]);

        $browser->visit('/api/clips/recent')
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'twitch_clip_id',
                        'title',
                        'broadcaster_name',
                        'game_name',
                        'created_at',
                        'user' => [
                            'id',
                            'twitch_display_name',
                        ],
                    ],
                ],
            ]);

        $browser->visit('/api/clips/search?q=test')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'twitch_clip_id',
                        'title',
                        'broadcaster_name',
                    ],
                ],
            ]);
    });
});

test('API endpoints handle authentication correctly', function () {
    $user = User::factory()->create();
    Clip::factory()->count(3)->create(['user_id' => $user->id]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->visit('/api/user/clips/statistics')
            ->assertStatus(401);

        $browser->loginAs($user)
            ->visit('/api/user/clips/statistics')
            ->assertStatus(200)
            ->assertJsonStructure([
                'total_clips',
                'featured_clips',
                'recent_clips',
            ]);
    });
});

test('API endpoints handle pagination', function () {
    $user = User::factory()->create();
    Clip::factory()->count(25)->create(['user_id' => $user->id]);

    $this->browse(function (Browser $browser) {
        $browser->visit('/api/clips/recent?page=1&per_page=10')
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data',
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $browser->visit('/api/clips/recent?page=2&per_page=10')
            ->assertJsonCount(10, 'data');
    });
});

test('API endpoints handle caching', function () {
    $user = User::factory()->create();
    Clip::factory()->count(5)->create(['user_id' => $user->id]);

    $this->browse(function (Browser $browser) {
        $startTime = microtime(true);
        $browser->visit('/api/clips/recent');
        $firstRequestTime = microtime(true) - $startTime;

        $startTime = microtime(true);
        $browser->visit('/api/clips/recent');
        $secondRequestTime = microtime(true) - $startTime;

        expect($secondRequestTime)->toBeLessThan($firstRequestTime * 0.5);
    });
});
