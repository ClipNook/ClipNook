<?php

use App\Models\User;
use Livewire\Livewire;

test('renders submit clip component', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\Clips\SubmitClip::class)
        ->assertOk()
        ->assertSet('twitchClipId', '')
        ->assertSet('isSubmitting', false)
        ->assertSet('successMessage', null)
        ->assertSet('errorMessage', null);
});

test('validates twitch clip id format', function () {
    $user = User::factory()->create();

    // Test invalid characters
    Livewire::actingAs($user)
        ->test(\App\Livewire\Clips\SubmitClip::class)
        ->set('twitchClipId', 'invalid@clip!')
        ->call('submit')
        ->assertHasErrors(['twitchClipId']);

    // Test valid clip ID
    Livewire::actingAs($user)
        ->test(\App\Livewire\Clips\SubmitClip::class)
        ->set('twitchClipId', 'DreamyComfortableKimchiBCWarrior')
        ->call('submit')
        ->assertHasNoErrors(['twitchClipId']);

    // Test valid Twitch URL
    Livewire::actingAs($user)
        ->test(\App\Livewire\Clips\SubmitClip::class)
        ->set('twitchClipId', 'https://www.twitch.tv/zurret/clip/DreamyComfortableKimchiBCWarrior-KHiH2nw1Hgh2vAGN')
        ->call('submit')
        ->assertHasNoErrors(['twitchClipId']);
});

test('handles clip not found error', function () {
    $user = User::factory()->create();

    $this->mock(\App\Services\Twitch\TwitchService::class, function ($mock) {
        $mock->shouldReceive('getClip')
            ->with('NonExistentClip')
            ->andReturn(null);
    });

    Livewire::actingAs($user)
        ->test(\App\Livewire\Clips\SubmitClip::class)
        ->set('twitchClipId', 'NonExistentClip')
        ->call('submit')
        ->assertSet('errorMessage', 'This clip was not found on Twitch. Please check the ID and try again.')
        ->assertSet('successMessage', null);
});

test('handles broadcaster not registered error', function () {
    $user = User::factory()->create();

    $clipDTO = new \App\Services\Twitch\DTOs\ClipDTO(
        id: 'test-clip-id',
        url: 'https://clips.twitch.tv/test-clip-id',
        embedUrl: 'https://clips.twitch.tv/embed/test-clip-id',
        broadcasterId: 'unregistered-broadcaster',
        broadcasterName: 'UnregisteredBroadcaster',
        creatorId: '67890',
        creatorName: 'TestCreator',
        videoId: 'video123',
        gameId: null,
        language: 'en',
        title: 'Test Clip',
        viewCount: 1000,
        createdAt: '2023-01-01T00:00:00Z',
        thumbnailUrl: 'https://clips-media-assets.twitch.tv/test.jpg',
        duration: 30,
        vodOffset: null,
        isFeatured: false
    );

    $this->mock(\App\Services\Twitch\TwitchService::class, function ($mock) use ($clipDTO) {
        $mock->shouldReceive('getClip')
            ->with('BroadcasterNotRegistered')
            ->andReturn($clipDTO);
    });

    Livewire::actingAs($user)
        ->test(\App\Livewire\Clips\SubmitClip::class)
        ->set('twitchClipId', 'BroadcasterNotRegistered')
        ->call('submit')
        ->assertSet('errorMessage', 'The broadcaster of this clip is not registered with our service.')
        ->assertSet('successMessage', null);
});
