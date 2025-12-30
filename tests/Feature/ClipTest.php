<?php

use App\Models\Clip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can view approved clips', function () {
    $user = User::factory()->create();
    $clip = Clip::factory()->create(['status' => 'approved']);

    $this->actingAs($user)
        ->getJson('/api/clips')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'url',
                    'status',
                    'user' => ['id', 'twitch_display_name', 'twitch_login'],
                ],
            ],
        ]);
});

test('user can submit a clip', function () {
    $user = User::factory()->create([
        'twitch_id'         => '12345',
        'email_verified_at' => now(),
    ]);

    // Mock the Twitch API response
    $this->mock(\App\Services\Twitch\TwitchApiClient::class, function ($mock) {
        $mock->shouldReceive('getClip')
            ->andReturn([
                'title'            => 'Test Clip',
                'url'              => 'https://clips.twitch.tv/test',
                'thumbnail_url'    => 'https://clips.twitch.tv/test.jpg',
                'duration'         => 30,
                'view_count'       => 100,
                'created_at'       => now()->toISOString(),
                'broadcaster_id'   => '12345',
                'broadcaster_name' => 'TestBroadcaster',
                'game_name'        => 'TestGame',
            ]);
    });

    $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ])
        ->assertStatus(201)
        ->assertJson([
            'message' => 'Clip submitted successfully and is pending moderation.',
        ]);

    expect(Clip::where('twitch_clip_id', 'TestClip123')->exists())->toBeTrue();
});

test('user cannot submit clip they do not own', function () {
    $user = User::factory()->create([
        'twitch_id'         => '12345',
        'email_verified_at' => now(),
    ]);

    // Mock the Twitch API response with different broadcaster
    $this->mock(\App\Services\Twitch\TwitchApiClient::class, function ($mock) {
        $mock->shouldReceive('getClip')
            ->andReturn([
                'title'            => 'Test Clip',
                'url'              => 'https://clips.twitch.tv/test',
                'thumbnail_url'    => 'https://clips.twitch.tv/test.jpg',
                'duration'         => 30,
                'view_count'       => 100,
                'created_at'       => now()->toISOString(),
                'broadcaster_id'   => '99999', // Different broadcaster
                'broadcaster_name' => 'OtherBroadcaster',
            ]);
    });

    $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['twitch_clip_id']);
});
