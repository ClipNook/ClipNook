<?php

use App\Livewire\Clips\SubmitClip;
use App\Models\User;
use App\Services\Twitch\Contracts\ClipsInterface;
use App\Services\Twitch\DTOs\ClipData;
use App\Services\Twitch\TokenRefreshService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

// Ensure DB is refreshed between tests
uses(RefreshDatabase::class);
it('accepts clip when broadcaster is registered and is a streamer', function () {
    $submitter   = User::factory()->create(['is_streamer' => true]);
    $broadcaster = User::factory()->create(['twitch_id' => '67955580', 'is_streamer' => true]);

    $clipArr = [
        'id'               => 'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN',
        'url'              => 'https://clips.twitch.tv/DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN',
        'embed_url'        => 'https://clips.twitch.tv/embed?clip=DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN',
        'broadcaster_id'   => '67955580',
        'broadcaster_name' => 'ChewieMelodies',
        'creator_id'       => '53834192',
        'creator_name'     => 'BlackNova03',
        'video_id'         => '205586603',
        'game_id'          => '488191',
        'language'         => 'en',
        'title'            => 'babymetal',
        'view_count'       => 10,
        'created_at'       => '2017-11-30T22:34:18Z',
        'thumbnail_url'    => 'https://clips-media-assets.twitch.tv/157589949-preview-480x272.jpg',
        'duration'         => 60,
        'vod_offset'       => 480,
        'is_featured'      => false,
    ];

    $clipData = ClipData::fromArray($clipArr);

    // Mock token service
    $this->mock(TokenRefreshService::class, function ($m) use ($submitter) {
        $m->shouldReceive('getValidToken')->once()->withArgs(function ($user) use ($submitter) {
            return $user->id === $submitter->id;
        })->andReturn('token');
    });

    // Mock ClipsInterface
    $this->mock(ClipsInterface::class, function ($m) use ($clipData) {
        $m->shouldReceive('setAccessToken')->once()->with('token')->andReturnSelf();
        $m->shouldReceive('getClipById')->once()->with('DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN')->andReturn($clipData);
        $m->shouldReceive('getGameById')->once()->with('488191')->andReturn(['id' => '488191', 'name' => 'Custom Game']);
        $m->shouldReceive('getVideoById')->once()->with('205586603')->andReturn(['id' => '205586603', 'title' => 'VOD']);
    });

    Livewire::actingAs($submitter)
        ->test(SubmitClip::class)
        ->set('input', 'https://clips.twitch.tv/DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN')
        ->call('check')
        ->assertSet('accepted', true)
        ->assertSet('message', __('clip.submit.messages.validated'))
        ->assertSee(__('clip.submit.labels.header'))
        ->assertSee($clipData->id);
});

it('shows token failure when token cannot be retrieved', function () {
    $submitter = User::factory()->create(['is_streamer' => true]);

    $this->mock(TokenRefreshService::class, function ($m) {
        $m->shouldReceive('getValidToken')->once()->andReturn(null);
    });

    Livewire::actingAs($submitter)
        ->test(SubmitClip::class)
        ->set('input', 'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN')
        ->call('check')
        ->assertSet('accepted', false)
        ->assertSet('message', __('clip.submit.messages.token_failed'));
});

it('rejects when broadcaster is not registered', function () {
    $submitter = User::factory()->create(['is_streamer' => true]);

    $clipArr = [
        'id'             => 'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN',
        'broadcaster_id' => '67955580',
    ];

    $clipData = ClipData::fromArray(array_merge(['url' => '', 'embed_url' => '', 'broadcaster_name' => '', 'creator_id' => '', 'creator_name' => '', 'video_id' => '', 'game_id' => '', 'language' => '', 'title' => '', 'view_count' => 0, 'created_at' => '2017-11-30T22:34:18Z', 'thumbnail_url' => '', 'duration' => 0, 'vod_offset' => 0, 'is_featured' => false], $clipArr));

    $this->mock(TokenRefreshService::class, function ($m) {
        $m->shouldReceive('getValidToken')->andReturn('token');
    });

    $this->mock(ClipsInterface::class, function ($m) use ($clipData) {
        $m->shouldReceive('setAccessToken')->andReturnSelf();
        $m->shouldReceive('getClipById')->andReturn($clipData);
    });

    Livewire::actingAs($submitter)
        ->test(SubmitClip::class)
        ->set('input', 'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN')
        ->call('check')
        ->assertSet('accepted', false)
        ->assertSet('message', __('clip.submit.messages.broadcaster_not_registered'));
});

it('rejects when broadcaster is not a streamer', function () {
    $submitter   = User::factory()->create(['is_streamer' => true]);
    $broadcaster = User::factory()->create(['twitch_id' => '67955580', 'is_streamer' => false]);

    $clipArr = [
        'id'             => 'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN',
        'broadcaster_id' => '67955580',
    ];

    $clipData = ClipData::fromArray(array_merge(['url' => '', 'embed_url' => '', 'broadcaster_name' => '', 'creator_id' => '', 'creator_name' => '', 'video_id' => '', 'game_id' => '', 'language' => '', 'title' => '', 'view_count' => 0, 'created_at' => '2017-11-30T22:34:18Z', 'thumbnail_url' => '', 'duration' => 0, 'vod_offset' => 0, 'is_featured' => false], $clipArr));

    $this->mock(TokenRefreshService::class, function ($m) {
        $m->shouldReceive('getValidToken')->andReturn('token');
    });

    $this->mock(ClipsInterface::class, function ($m) use ($clipData) {
        $m->shouldReceive('setAccessToken')->andReturnSelf();
        $m->shouldReceive('getClipById')->andReturn($clipData);
    });

    Livewire::actingAs($submitter)
        ->test(SubmitClip::class)
        ->set('input', 'DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN')
        ->call('check')
        ->assertSet('accepted', false)
        ->assertSet('message', __('clip.submit.messages.broadcaster_not_streamer'));
});
