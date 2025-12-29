<?php

use App\Services\Twitch\Clips\ClipsService;
use App\Services\Twitch\Contracts\HttpClientInterface;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

it('sends Client-ID header and Authorization when creating a clip', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = ['client_id' => 'cid', 'api_url' => 'https://api.twitch.tv/helix'];

    $client->shouldReceive('post')->once()->withArgs(function ($url, $data, $headers) {
        expect($headers)->toBeArray()->toHaveKey('Client-ID');
        expect($headers['Client-ID'])->toBe('cid');
        expect($headers)->toHaveKey('Authorization');
        expect($headers)->toHaveKey('Content-Type');
        expect($headers['Content-Type'])->toBe('application/json');

        return true;
    })->andReturn(['data' => [['id' => 'x', 'edit_url' => 'u']]]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $res = $service->createClip('123');

    expect($res['id'])->toBe('x');
});

it('does not include Content-Type for GET requests', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = ['client_id' => 'cid', 'api_url' => 'https://api.twitch.tv/helix'];

    $client->shouldReceive('get')->once()->withArgs(function ($url, $data, $headers) {
        expect($headers)->toBeArray()->toHaveKey('Client-ID');
        expect($headers)->not->toHaveKey('Content-Type');

        return true;
    })->andReturn(['data' => []]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $res = $service->getClipsByIds(['123']);

    expect($res)->toBeArray();
});

it('throws RateLimitException when game fetch is rate limited', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = ['client_id' => 'cid', 'api_url' => 'https://api.twitch.tv/helix'];

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $gameId      = 'rl-test-game';
    $maxAttempts = 30; // matches the production limit for games

    // Pre-hit the limiter so the next call will immediately throw
    $limitKey = "twitch:games:{$gameId}";

    for ($i = 0; $i < $maxAttempts; $i++) {
        \Illuminate\Support\Facades\RateLimiter::hit($limitKey, 60);
    }

    // Clear cache so the closure executes and the limiter is checked
    Cache::forget("twitch:game:{$gameId}");

    try {
        $service->getGameById($gameId);
        expect(false)->toBeTrue(); // should not reach here
    } catch (\App\Services\Twitch\Exceptions\RateLimitException $e) {
        expect($e->retryAfter)->toBeInt()->toBeGreaterThan(0);
    }
});
