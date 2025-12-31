<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('user can submit clip through UI', function () {
    // Arrange
    Http::fake([
        'https://api.twitch.tv/helix/clips*' => Http::response([
            'data' => [[
                'id'               => 'TestClip123',
                'title'            => 'Epic Moment',
                'broadcaster_id'   => '123456',
                'broadcaster_name' => 'TestBroadcaster',
                'game_id'          => '33214',
                'created_at'       => now()->toISOString(),
                'thumbnail_url'    => 'https://clips.twitch.tv/TestClip123.jpg',
                'url'              => 'https://clips.twitch.tv/TestClip123',
                'view_count'       => 100,
                'duration'         => 30,
            ]],
        ]),
    ]);

    $user = User::factory()->create([
        'twitch_access_token' => encrypt('test_token'),
    ]);

    // Act & Assert
    $page = visit('/clips/submit')
        ->assertAuthenticated()
        ->assertSee('Submit Clip')
        ->fill('twitch_clip_url', 'https://clips.twitch.tv/TestClip123')
        ->click('Submit')
        ->assertSee('Clip submitted successfully')
        ->assertNoJavascriptErrors();
});

test('moderation workflow', function () {
    $moderator = User::factory()->moderator()->create();
    $clip      = \App\Models\Clip::factory()->pending()->create();

    $page = visit('/admin/clips')
        ->assertSee('Pending Clips')
        ->assertSee($clip->title)
        ->click("approve-{$clip->id}")
        ->waitFor('.toast-success')
        ->assertSee('Clip approved')
        ->assertDontSee($clip->title); // Removed from pending list

    expect($clip->fresh()->status)->toBe('approved');
});

test('security headers are present', function () {
    visit('/')
        ->assertHeaderPresent('X-Content-Type-Options', 'nosniff')
        ->assertHeaderPresent('X-Frame-Options', 'DENY')
        ->assertHeaderPresent('Content-Security-Policy');
});

test('rate limiting works', function () {
    $user = User::factory()->create();

    // Make 61 requests (over limit of 60)
    for ($i = 0; $i < 61; $i++) {
        $response = $this->actingAs($user)->get('/api/clips');

        if ($i < 60) {
            $response->assertOk();
        }
    }

    // 61st request should be rate limited
    $this->actingAs($user)
        ->get('/api/clips')
        ->assertStatus(429)
        ->assertJsonFragment(['error' => 'Too many attempts']);
});

describe('ClipBrowserTest', function () {
    it('does something', function () {
        // Add test logic here
    });
});
