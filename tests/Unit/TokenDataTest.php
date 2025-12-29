<?php

use App\Services\Twitch\DTOs\TokenData;

it('parses scopes when scope is string', function () {
    $data = [
        'access_token'  => 'access',
        'refresh_token' => 'refresh',
        'expires_in'    => 3600,
        'scope'         => 'user:read:email chat:read',
    ];

    $token = TokenData::fromArray($data);

    expect($token->scopes)->toBeArray()->toHaveCount(2)->toEqual(['user:read:email', 'chat:read']);
});

it('handles empty or missing scope as empty array', function () {
    $data = [
        'access_token'  => 'access',
        'refresh_token' => 'refresh',
        'expires_in'    => 3600,
    ];

    $token = TokenData::fromArray($data);

    expect($token->scopes)->toBeArray()->toHaveCount(0);
});
