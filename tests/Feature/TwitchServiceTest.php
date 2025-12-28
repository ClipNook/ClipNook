<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Twitch\Clips\ClipsService;
use App\Services\Twitch\Http\CurlHttpClient;
use App\Services\Twitch\OAuth\OAuthService;
use PHPUnit\Framework\TestCase;

class TwitchServiceTest extends TestCase
{
    public function test_clips_service_instantiation(): void
    {
        $client = new CurlHttpClient(rateLimitEnabled: false);
        $config = [
            'client_id' => 'test',
            'api_url'   => 'https://api.twitch.tv/helix',
        ];

        $service = new ClipsService($client, $config);

        $this->assertInstanceOf(ClipsService::class, $service);
    }

    public function test_oauth_service_instantiation(): void
    {
        $client = new CurlHttpClient(rateLimitEnabled: false);
        $config = [
            'client_id'     => 'test',
            'client_secret' => 'secret',
            'redirect_uri'  => 'https://example.test/callback',
            'scopes'        => ['user:read:email'],
        ];

        $service = new OAuthService($client, $config);

        $this->assertInstanceOf(OAuthService::class, $service);
    }
}
