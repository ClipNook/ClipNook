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

it('rate limits getClips per broadcaster', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = [
        'client_id'          => 'cid',
        'api_url'            => 'https://api.twitch.tv/helix',
        'rate_limit_actions' => [
            'get_clips' => ['max' => 3, 'decay' => 60],
        ],
    ];

    $client->shouldReceive('get')->andReturn(['data' => []]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $broadcasterId = 'broad-1';

    // Ensure limiter is cleared for this test
    \Illuminate\Support\Facades\RateLimiter::clear("twitch:clips:{$broadcasterId}");

    // Should succeed up to limit
    for ($i = 0; $i < 3; $i++) {
        $res = $service->getClips($broadcasterId);
        expect($res)->toBeInstanceOf(\App\Services\Twitch\DTOs\PaginationData::class);
    }

    // Next call should be rate limited
    try {
        $service->getClips($broadcasterId);
        expect(false)->toBeTrue();
    } catch (\App\Services\Twitch\Exceptions\RateLimitException $e) {
        expect($e->retryAfter)->toBeInt()->toBeGreaterThan(0);
    }
});

it('rate limits createClip per broadcaster', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = [
        'client_id'          => 'cid',
        'api_url'            => 'https://api.twitch.tv/helix',
        'rate_limit_actions' => [
            'create_clip' => ['max' => 2, 'decay' => 60],
        ],
    ];

    $client->shouldReceive('post')->andReturn(['data' => [['id' => 'c1', 'edit_url' => 'u1']]]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $broadcasterId = 'broad-2';

    \Illuminate\Support\Facades\RateLimiter::clear("twitch:create_clip:{$broadcasterId}");

    $service->createClip($broadcasterId);
    $service->createClip($broadcasterId);

    try {
        $service->createClip($broadcasterId);
        expect(false)->toBeTrue();
    } catch (\App\Services\Twitch\Exceptions\RateLimitException $e) {
        expect($e->retryAfter)->toBeInt()->toBeGreaterThan(0);
    }
});

it('rate limits getClipsByIds for batches', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = [
        'client_id'          => 'cid',
        'api_url'            => 'https://api.twitch.tv/helix',
        'rate_limit_actions' => [
            'get_clips_by_ids' => ['max' => 2, 'decay' => 60],
        ],
    ];

    $client->shouldReceive('get')->andReturn(['data' => []]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $ids = ['a', 'b', 'c'];

    $limitKey = 'twitch:clips:ids:'.md5(implode(',', $ids));
    \Illuminate\Support\Facades\RateLimiter::clear($limitKey);

    $service->getClipsByIds($ids);
    $service->getClipsByIds($ids);

    try {
        $service->getClipsByIds($ids);
        expect(false)->toBeTrue();
    } catch (\App\Services\Twitch\Exceptions\RateLimitException $e) {
        expect($e->retryAfter)->toBeInt()->toBeGreaterThan(0);
    }
});

it('reads rate limit settings from config/services.php when not provided', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    // Set centralized config to a very small limit
    config(['services.twitch.rate_limit.actions' => ['get_clips' => ['max' => 1, 'decay' => 60]]]);

    $config = ['client_id' => 'cid', 'api_url' => 'https://api.twitch.tv/helix'];

    $client->shouldReceive('get')->andReturn(['data' => []]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $broadcasterId = 'cfg-1';

    \Illuminate\Support\Facades\RateLimiter::clear("twitch:clips:{$broadcasterId}");

    $service->getClips($broadcasterId); // first should pass

    $this->expectException(\App\Services\Twitch\Exceptions\RateLimitException::class);

    $service->getClips($broadcasterId); // second should throw due to config
});
