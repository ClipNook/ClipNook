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
                    'submitter' => ['id', 'twitch_display_name', 'twitch_login'],
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

    $broadcaster = User::factory()->streamer()->create([
        'twitch_id' => '99999',
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
        ->assertStatus(403)
        ->assertJson([
            'error' => 'permission_denied',
        ]);
});

test('moderator can approve clips', function () {
    $broadcaster = User::factory()->streamer()->create();
    $moderator   = User::factory()->moderator()->create();
    $clip        = Clip::factory()->create([
        'status'         => 'pending',
        'broadcaster_id' => $broadcaster->id,
    ]);

    // Grant moderation permission to the moderator for this broadcaster
    $broadcaster->grantClipModerationPermission($moderator);

    $this->actingAs($moderator)
        ->patchJson("/api/clips/{$clip->id}", [
            'action' => 'approve',
        ])
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Clip approved successfully.',
        ]);

    $clip->refresh();
    expect($clip->status)->toBe('approved');
    expect($clip->moderated_by)->toBe($moderator->id);
});

test('broadcaster can moderate their own clips', function () {
    $broadcaster = User::factory()->streamer()->create();
    $clip        = Clip::factory()->create([
        'status'         => 'pending',
        'broadcaster_id' => $broadcaster->id,
    ]);

    $this->actingAs($broadcaster)
        ->patchJson("/api/clips/{$clip->id}", [
            'action' => 'approve',
        ])
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Clip approved successfully.',
        ]);

    $clip->refresh();
    expect($clip->status)->toBe('approved');
    expect($clip->moderated_by)->toBe($broadcaster->id);
});

test('broadcaster cannot moderate other broadcasters clips', function () {
    $broadcaster1 = User::factory()->streamer()->create();
    $broadcaster2 = User::factory()->streamer()->create();
    $clip         = Clip::factory()->create([
        'status'         => 'pending',
        'broadcaster_id' => $broadcaster2->id,
    ]);

    $this->actingAs($broadcaster1)
        ->patchJson("/api/clips/{$clip->id}", [
            'action' => 'approve',
        ])
        ->assertStatus(403);
});

test('regular user cannot moderate clips', function () {
    $broadcaster = User::factory()->streamer()->create();
    $user        = User::factory()->create();
    $clip        = Clip::factory()->create([
        'status'         => 'pending',
        'broadcaster_id' => $broadcaster->id,
    ]);

    $this->actingAs($user)
        ->patchJson("/api/clips/{$clip->id}", [
            'action' => 'approve',
        ])
        ->assertStatus(403);
});

test('broadcaster can submit their own clips', function () {
    $broadcaster = User::factory()->streamer()->create([
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
                'broadcaster_id'   => '12345', // Same as user
                'broadcaster_name' => 'TestBroadcaster',
                'game_name'        => 'TestGame',
            ]);
    });

    $this->actingAs($broadcaster)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ])
        ->assertStatus(201)
        ->assertJson([
            'message' => 'Clip submitted successfully and is pending moderation.',
        ]);

    expect(Clip::where('twitch_clip_id', 'TestClip123')->exists())->toBeTrue();
});

test('user cannot submit clips for unregistered broadcaster', function () {
    $user = User::factory()->create([
        'twitch_id'         => '99999', // Different from clip broadcaster
        'email_verified_at' => now(),
    ]);

    // Mock the Twitch API response with unregistered broadcaster
    $this->mock(\App\Services\Twitch\TwitchApiClient::class, function ($mock) {
        $mock->shouldReceive('getClip')
            ->andReturn([
                'title'            => 'Test Clip',
                'url'              => 'https://clips.twitch.tv/test',
                'thumbnail_url'    => 'https://clips.twitch.tv/test.jpg',
                'duration'         => 30,
                'view_count'       => 100,
                'created_at'       => now()->toISOString(),
                'broadcaster_id'   => '88888', // Unregistered broadcaster
                'broadcaster_name' => 'UnregisteredBroadcaster',
                'game_name'        => 'TestGame',
            ]);
    });

    $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ])
        ->assertStatus(400)
        ->assertJson([
            'error' => 'broadcaster_not_registered',
        ]);
});

test('user can submit clips when broadcaster allows public submissions', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create([
        'twitch_id'         => '99999',
        'email_verified_at' => now(),
    ]);

    // Enable public submissions for broadcaster
    $broadcaster->broadcasterSettings()->create([
        'allow_public_clip_submissions' => true,
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
        ->assertStatus(201);

    expect(Clip::where('twitch_clip_id', 'TestClip123')->exists())->toBeTrue();
});

test('user can submit clips when granted specific permission', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create([
        'twitch_id'         => '99999',
        'email_verified_at' => now(),
    ]);

    // Grant specific permission
    $broadcaster->grantClipSubmissionPermission($user);

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
        ->assertStatus(201);

    expect(Clip::where('twitch_clip_id', 'TestClip123')->exists())->toBeTrue();
});

test('user cannot submit clips without permission', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create([
        'twitch_id'         => '99999',
        'email_verified_at' => now(),
    ]);

    // Broadcaster does NOT allow public submissions and user has no specific permission

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
        ->assertStatus(403)
        ->assertJson([
            'error' => 'permission_denied',
        ]);
});

test('user with edit permission can edit clips', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create([
        'twitch_id'         => '99999',
        'email_verified_at' => now(),
    ]);

    // Grant edit permission
    $broadcaster->grantClipEditingPermission($user);

    $clip = Clip::factory()->create([
        'broadcaster_id' => $broadcaster->id,
        'status'         => 'approved',
    ]);

    // Note: This test assumes there's an update endpoint that allows editing clip details
    // For now, we'll test the permission method directly
    expect($user->canEditClipsFor($broadcaster))->toBeTrue();
});

test('user with delete permission can delete clips', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create([
        'twitch_id'         => '99999',
        'email_verified_at' => now(),
    ]);

    // Grant delete permission
    $broadcaster->grantClipDeletionPermission($user);

    $clip = Clip::factory()->create([
        'broadcaster_id' => $broadcaster->id,
        'status'         => 'approved',
    ]);

    // Test the permission method
    expect($user->canDeleteClipsFor($broadcaster))->toBeTrue();
});

test('user with moderation permission can moderate clips', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create([
        'twitch_id'         => '99999',
        'email_verified_at' => now(),
    ]);

    // Grant moderation permission
    $broadcaster->grantClipModerationPermission($user);

    $clip = Clip::factory()->create([
        'broadcaster_id' => $broadcaster->id,
        'status'         => 'pending',
    ]);

    // Test the permission method
    expect($user->canModerateClipsFor($broadcaster))->toBeTrue();
});

test('broadcaster can always perform all actions on their clips', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);

    // Broadcaster should have all permissions for their own clips
    expect($broadcaster->canSubmitClipsFor($broadcaster))->toBeTrue();
    expect($broadcaster->canEditClipsFor($broadcaster))->toBeTrue();
    expect($broadcaster->canDeleteClipsFor($broadcaster))->toBeTrue();
    expect($broadcaster->canModerateClipsFor($broadcaster))->toBeTrue();
});

test('returns proper error when broadcaster is not registered', function () {
    $user = User::factory()->create(['twitch_id' => '99999', 'email_verified_at' => now()]);

    // Mock the Twitch API response with a broadcaster that doesn't exist
    $this->mock(\App\Services\Twitch\TwitchApiClient::class, function ($mock) {
        $mock->shouldReceive('getClip')
            ->andReturn([
                'title'            => 'Test Clip',
                'url'              => 'https://clips.twitch.tv/test',
                'thumbnail_url'    => 'https://clips.twitch.tv/test.jpg',
                'duration'         => 30,
                'view_count'       => 100,
                'created_at'       => now()->toISOString(),
                'broadcaster_id'   => 'nonexistent123', // This broadcaster doesn't exist
                'broadcaster_name' => 'NonExistentBroadcaster',
            ]);
    });

    $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ])
        ->assertStatus(400)
        ->assertJson([
            'error'   => 'broadcaster_not_registered',
            'message' => 'Broadcaster with Twitch ID nonexistent123 is not registered on this platform',
        ]);
});

test('returns proper error when clip not found on twitch', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create(['twitch_id' => '99999', 'email_verified_at' => now()]);

    // Mock Twitch service to return null
    $this->mock(\App\Services\Twitch\TwitchApiClient::class, function ($mock) {
        $mock->shouldReceive('getClip')->andReturn(null);
    });

    $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'NonExistentClip',
        ])
        ->assertStatus(404)
        ->assertJson([
            'error'   => 'clip_not_found',
            'message' => 'Clip NonExistentClip not found on Twitch',
        ]);
});

test('returns proper error when user lacks permission', function () {
    $broadcaster = User::factory()->streamer()->create(['twitch_id' => '12345']);
    $user        = User::factory()->create(['twitch_id' => '99999', 'email_verified_at' => now()]);

    // Disable public submissions and don't give specific permission
    $broadcaster->broadcasterSettings()->create([
        'allow_public_clip_submissions' => false,
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
            ]);
    });

    $this->actingAs($user)
        ->postJson('/api/clips', [
            'twitch_clip_id' => 'TestClip123',
        ])
        ->assertStatus(403)
        ->assertJson([
            'error' => 'permission_denied',
        ]);
});
