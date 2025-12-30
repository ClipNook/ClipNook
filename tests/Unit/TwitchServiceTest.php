<?php

use App\Services\Twitch\TwitchService;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can extract user id from twitch url', function () {
    $url    = 'https://twitch.tv/someuser';
    $userId = TwitchService::extractUserIdFromUrl($url);

    expect($userId)->toBe('someuser');
});

it('can extract user id from twitch url with query params', function () {
    $url    = 'https://twitch.tv/someuser?tab=following';
    $userId = TwitchService::extractUserIdFromUrl($url);

    expect($userId)->toBe('someuser');
});

it('returns null for invalid twitch url', function () {
    $url    = 'https://example.com/someuser';
    $userId = TwitchService::extractUserIdFromUrl($url);

    expect($userId)->toBeNull();
});
