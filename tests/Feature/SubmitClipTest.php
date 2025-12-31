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

// test('rejects clips older than 7 days', function () {
//     $user = User::factory()->create(['twitch_id' => 'unique999']);

//     $clipDTO = new \App\Services\Twitch\DTOs\ClipDTO(
//         id: 'old-clip-id',
//         url: 'https://clips.twitch.tv/old-clip-id',
//         embedUrl: 'https://clips.twitch.tv/embed/old-clip-id',
//         broadcasterId: 'unique999',
//         broadcasterName: 'TestBroadcaster',
//         creatorId: '67890',
//         creatorName: 'TestCreator',
//         videoId: 'video123',
//         gameId: null,
//         language: 'en',
//         title: 'Old Test Clip',
//         viewCount: 1000,
//         createdAt: now()->subDays(20)->toISOString(), // 20 days old
//         thumbnailUrl: 'https://clips-media-assets.twitch.tv/old.jpg',
//         duration: 30,
//         vodOffset: null,
//         isFeatured: false
//     );

//     $this->mock(\App\Services\Twitch\TwitchService::class, function ($mock) use ($clipDTO) {
//         $mock->shouldReceive('getClip')
//             ->with('old-clip-id')
//             ->andReturn($clipDTO);
//     });

//     Livewire::actingAs($user)
//         ->test(\App\Livewire\Clips\SubmitClip::class)
//         ->set('twitchClipId', 'old-clip-id')
//         ->call('submit')
//         ->assertSet('errorMessage', 'Clip is older than 7 days and cannot be submitted.')
//         ->assertSet('successMessage', null);
// });
