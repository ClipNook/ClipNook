<?php

declare(strict_types=1);

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can find user by twitch id', function () {
    $user = User::factory()->create(['twitch_id' => '12345']);

    $repository = new UserRepository(new User);

    $foundUser = $repository->findByTwitchId('12345');

    expect($foundUser)->not->toBeNull();
    expect($foundUser->id)->toBe($user->id);
});

it('returns null when user not found by twitch id', function () {
    $repository = new UserRepository(new User);

    $foundUser = $repository->findByTwitchId('nonexistent');

    expect($foundUser)->toBeNull();
});

it('can find user by twitch login', function () {
    $user = User::factory()->create(['twitch_login' => 'testuser']);

    $repository = new UserRepository(new User);

    $foundUser = $repository->findByTwitchLogin('testuser');

    expect($foundUser)->not->toBeNull();
    expect($foundUser->id)->toBe($user->id);
});

it('can get all streamers', function () {
    User::factory()->count(3)->create(['is_streamer' => true]);
    User::factory()->count(2)->create(['is_streamer' => false]);

    $repository = new UserRepository(new User);

    $streamers = $repository->getStreamers();

    expect($streamers)->toHaveCount(3);
    $streamers->each(function ($user) {
        expect($user->is_streamer)->toBeTrue();
    });
});

it('can get all moderators', function () {
    User::factory()->count(2)->create(['is_moderator' => true]);
    User::factory()->count(3)->create(['is_moderator' => false]);

    $repository = new UserRepository(new User);

    $moderators = $repository->getModerators();

    expect($moderators)->toHaveCount(2);
    $moderators->each(function ($user) {
        expect($user->is_moderator)->toBeTrue();
    });
});

it('can check if user exists by twitch id', function () {
    User::factory()->create(['twitch_id' => '12345']);

    $repository = new UserRepository(new User);

    expect($repository->existsByTwitchId('12345'))->toBeTrue();
    expect($repository->existsByTwitchId('nonexistent'))->toBeFalse();
});

it('can count users by role', function () {
    User::factory()->count(3)->create(['is_streamer' => true]);
    User::factory()->count(2)->create(['is_moderator' => true]);
    User::factory()->count(1)->create(['is_admin' => true]);

    $repository = new UserRepository(new User);

    expect($repository->countByRole('streamer'))->toBe(3);
    expect($repository->countByRole('moderator'))->toBe(2);
    expect($repository->countByRole('admin'))->toBe(1);
    expect($repository->countByRole('unknown'))->toBe(0);
});
