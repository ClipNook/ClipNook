<?php

use App\Actions\Twitch\AuthenticateTwitchUserAction;
use App\Actions\Twitch\ExchangeCodeForTokenAction;
use App\Actions\Twitch\RedirectToTwitchAction;
use App\Models\User;
use App\Services\Twitch\DTOs\TokenDTO;
use App\Services\Twitch\TwitchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('redirects to twitch oauth with correct parameters', function () {
    config([
        'twitch.client_id'    => 'test_client_id',
        'twitch.redirect_uri' => 'http://localhost/auth/twitch/callback',
        'twitch.scopes'       => ['user:read:email'],
    ]);

    $action   = app(RedirectToTwitchAction::class);
    $response = $action->execute();

    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);

    $url = $response->getTargetUrl();
    expect($url)->toContain('https://id.twitch.tv/oauth2/authorize');
    expect($url)->toContain('client_id=test_client_id');
    expect($url)->toContain('redirect_uri=http%3A%2F%2Flocalhost%2Fauth%2Ftwitch%2Fcallback');
    expect($url)->toContain('scope=user%3Aread%3Aemail');
    expect($url)->toContain('response_type=code');
});

it('exchanges authorization code for token successfully', function () {
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'access_token'  => 'test_access_token',
            'refresh_token' => 'test_refresh_token',
            'expires_in'    => 3600,
            'token_type'    => 'bearer',
            'scope'         => 'user:read:email',
        ]),
    ]);

    $action = app(ExchangeCodeForTokenAction::class);
    $token  = $action->execute('test_code');

    expect($token)->toBeInstanceOf(TokenDTO::class);
    expect($token->accessToken)->toBe('test_access_token');
    expect($token->refreshToken)->toBe('test_refresh_token');
    expect($token->expiresIn)->toBe(3600);
});

it('handles token exchange failure gracefully', function () {
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([], 400),
    ]);

    $action = app(ExchangeCodeForTokenAction::class);
    $token  = $action->execute('invalid_code');

    expect($token)->toBeNull();
});

it('authenticates twitch user and creates account', function () {
    $token = new TokenDTO(
        accessToken: 'test_token',
        refreshToken: 'test_refresh',
        expiresIn: 3600,
        tokenType: 'bearer',
        scope: 'user:read:email',
        issuedAt: time(),
    );

    Http::fake([
        'https://api.twitch.tv/helix/users' => Http::response([
            'data' => [
                [
                    'id'                => '12345',
                    'login'             => 'testuser',
                    'display_name'      => 'TestUser',
                    'email'             => 'test@example.com',
                    'profile_image_url' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/test-profile_image-300x300.png',
                    'offline_image_url' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/test-offline_image-1920x1080.png',
                    'description'       => 'Test user description',
                    'view_count'        => 1000,
                    'created_at'        => '2020-01-01T00:00:00Z',
                ],
            ],
        ]),
    ]);

    $twitchService = app(TwitchService::class);
    $twitchService->setTokens($token);

    $action = app(AuthenticateTwitchUserAction::class);

    $user = $action->execute($token, $twitchService);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->twitch_id)->toBe('12345');
    expect($user->twitch_login)->toBe('testuser');
    expect($user->twitch_display_name)->toBe('TestUser');
    expect($user->twitch_email)->toBe('test@example.com');

    $this->assertDatabaseHas('users', [
        'twitch_id'    => '12345',
        'twitch_login' => 'testuser',
    ]);
});

it('updates existing user on re-authentication', function () {
    // Create existing user
    $existingUser = User::factory()->create([
        'twitch_id'           => '12345',
        'twitch_display_name' => 'OldName',
    ]);

    $token = new TokenDTO(
        accessToken: 'test_token',
        refreshToken: 'test_refresh',
        expiresIn: 3600,
        tokenType: 'bearer',
        scope: 'user:read:email',
        issuedAt: time(),
    );

    Http::fake([
        'https://api.twitch.tv/helix/users' => Http::response([
            'data' => [
                [
                    'id'                => '12345',
                    'login'             => 'testuser',
                    'display_name'      => 'UpdatedName',
                    'email'             => 'updated@example.com',
                    'profile_image_url' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/updated-profile_image-300x300.png',
                    'offline_image_url' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/updated-offline_image-1920x1080.png',
                    'description'       => 'Updated user description',
                    'view_count'        => 2000,
                    'created_at'        => '2020-01-01T00:00:00Z',
                ],
            ],
        ]),
    ]);

    $twitchService = app(TwitchService::class);
    $twitchService->setTokens($token);

    $action = app(AuthenticateTwitchUserAction::class);

    $user = $action->execute($token, $twitchService);

    expect($user->id)->toBe($existingUser->id);
    expect($user->twitch_display_name)->toBe('UpdatedName');
    expect($user->twitch_email)->toBe('updated@example.com');
});
