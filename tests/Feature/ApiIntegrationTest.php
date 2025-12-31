<?php

namespace Tests\Feature;

use App\Models\Clip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('clip submission integrates with Twitch API', function () {
    // Mock the TwitchService
    $mockClipData = new \App\Services\Twitch\DTOs\ClipDTO(
        id: 'TestClip123',
        url: 'https://clips.twitch.tv/TestClip123',
        embedUrl: 'https://clips.twitch.tv/embed?clip=TestClip123',
        broadcasterId: '123456',
        broadcasterName: 'TestBroadcaster',
        creatorId: '123456',
        creatorName: 'TestCreator',
        videoId: '123456789',
        gameId: '33214',
        language: 'en',
        title: 'Epic Moment',
        viewCount: 100,
        createdAt: now()->toISOString(),
        thumbnailUrl: 'https://clips.twitch.tv/TestClip123.jpg',
        duration: 30,
        vodOffset: null,
        isFeatured: false,
    );

    $this->mock(\App\Services\Twitch\TwitchService::class, function ($mock) use ($mockClipData) {
        $mock->shouldReceive('getClip')
            ->with('TestClip123')
            ->andReturn($mockClipData);
    });

    // Mock the TwitchGameService
    $mockGame = \App\Models\Game::create([
        'twitch_game_id' => '33214',
        'name'           => 'Fortnite',
        'box_art_url'    => 'https://static-cdn.jtvnw.net/ttv-boxart/33214-{width}x{height}.jpg',
    ]);

    $this->mock(\App\Services\Twitch\TwitchGameService::class, function ($mock) use ($mockGame) {
        $mock->shouldReceive('getOrCreateGame')
            ->with('33214')
            ->andReturn($mockGame);
    });

    $user        = User::factory()->create();
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '123456']);

    // Give user permission to submit clips for this broadcaster
    $broadcaster->grantClipSubmissionPermission($user);

    // Use sync mode for testing
    config(['app.use_sync_clip_submission' => true]);

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ]);

    // Assert
    $response->assertStatus(201);
    expect(Clip::where('twitch_clip_id', 'TestClip123')->exists())->toBeTrue();

    $clip = Clip::where('twitch_clip_id', 'TestClip123')->first();
    expect($clip->game)->not->toBeNull();
    expect($clip->game->name)->toBe('Fortnite');
});

test('game creation from Twitch API', function () {
    // Arrange
    Http::fake([
        'https://api.twitch.tv/helix/games*' => Http::response([
            'data' => [[
                'id'          => '33214',
                'name'        => 'Fortnite',
                'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/33214-{width}x{height}.jpg',
                'igdb_id'     => '1905',
            ]],
        ]),
    ]);

    $service = app(\App\Services\Twitch\TwitchGameService::class);

    // Act
    $game = $service->getOrCreateGame('33214');

    // Assert
    expect($game)->not->toBeNull();
    expect($game->name)->toBe('Fortnite');
    expect($game->twitch_game_id)->toBe('33214');
});

test('notification channels work', function () {
    // Arrange
    $user = User::factory()->create([
        'notifications_email' => true,
        'notifications_web'   => false, // Disable database notifications for this test
        'notifications_ntfy'  => true,
        'ntfy_server_url'     => 'https://ntfy.example.com',
        'ntfy_topic'          => 'test-topic',
        'ntfy_auth_token'     => 'test-token',
    ]);

    Http::fake([
        'https://ntfy.example.com/test-topic' => Http::response([], 200),
    ]);

    // Act
    $user->notify(new \App\Notifications\AdminAlert(
        title: 'Test Alert',
        message: 'This is a test',
        level: 'info'
    ));

    // Assert
    Http::assertSent(function ($request) {
        return $request->url() === 'https://ntfy.example.com/test-topic' &&
               $request->hasHeader('Authorization', 'Bearer test-token') &&
               $request['title'] === 'Test Alert' &&
               $request['message'] === 'This is a test';
    });
});
