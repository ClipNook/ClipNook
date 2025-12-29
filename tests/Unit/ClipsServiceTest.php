<?php

use App\Services\Twitch\Clips\ClipsService;
use App\Services\Twitch\Contracts\HttpClientInterface;

it('sends Client-Id header and Authorization when creating a clip', function () {
    $client = Mockery::mock(HttpClientInterface::class);

    $config = ['client_id' => 'cid', 'api_url' => 'https://api.twitch.tv/helix'];

    $client->shouldReceive('post')->once()->withArgs(function ($url, $data, $headers) {
        expect($headers)->toBeArray()->toHaveKey('Client-Id');
        expect($headers['Client-Id'])->toBe('cid');
        expect($headers)->toHaveKey('Authorization');

        return true;
    })->andReturn(['data' => [['id' => 'x', 'edit_url' => 'u']]]);

    $service = new ClipsService($client, $config);
    $service->setAccessToken('token');

    $res = $service->createClip('123');

    expect($res['id'])->toBe('x');
});
