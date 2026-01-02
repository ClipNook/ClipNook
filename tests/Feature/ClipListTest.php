<?php

declare(strict_types=1);

use App\Models\Clip;
use App\Models\User;
use Livewire\Livewire;

test('renders clip list component', static function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(App\Livewire\Clips\ClipList::class)
        ->assertOk()
        ->assertSet('perPage', 12)
        ->assertSet('search', '');
});

test('displays clips in list', static function (): void {
    $user        = User::factory()->create();
    $broadcaster = User::factory()->create();

    Clip::factory()->create([
        'submitter_id'   => $user->id,
        'broadcaster_id' => $broadcaster->id,
        'title'          => 'Test Clip',
        'status'         => 'approved',
    ]);

    Livewire::actingAs($user)
        ->test(App\Livewire\Clips\ClipList::class)
        ->assertOk()
        ->assertSee('Test Clip')
        ->assertDontSee('Approved');
});

test('searches clips by title', static function (): void {
    $user        = User::factory()->create();
    $broadcaster = User::factory()->create();

    Clip::factory()->create([
        'submitter_id'   => $user->id,
        'broadcaster_id' => $broadcaster->id,
        'title'          => 'Amazing Clip',
        'status'         => 'approved',
    ]);

    Clip::factory()->create([
        'submitter_id'   => $user->id,
        'broadcaster_id' => $broadcaster->id,
        'title'          => 'Boring Clip',
        'status'         => 'approved',
    ]);

    Livewire::actingAs($user)
        ->test(App\Livewire\Clips\ClipList::class)
        ->set('search', 'Amazing')
        ->assertSee('Amazing Clip')
        ->assertDontSee('Boring Clip');
});

test('paginates clips', static function (): void {
    $user        = User::factory()->create();
    $broadcaster = User::factory()->create();

    // Create more clips than perPage (12)
    Clip::factory()->count(15)->create([
        'submitter_id'   => $user->id,
        'broadcaster_id' => $broadcaster->id,
        'status'         => 'approved',
    ]);

    Livewire::actingAs($user)
        ->test(App\Livewire\Clips\ClipList::class)
        ->assertOk();
});
