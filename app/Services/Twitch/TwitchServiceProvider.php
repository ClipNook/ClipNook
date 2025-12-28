<?php

declare(strict_types=1);

namespace App\Services\Twitch;

use App\Services\Twitch\Clips\ClipsService;
use App\Services\Twitch\Contracts\ClipsInterface;
use App\Services\Twitch\Contracts\HttpClientInterface;
use App\Services\Twitch\Contracts\OAuthInterface;
use App\Services\Twitch\Http\CurlHttpClient;
use App\Services\Twitch\OAuth\OAuthService;
use Illuminate\Support\ServiceProvider;

class TwitchServiceProvider extends ServiceProvider
{
    /**
     * Register Twitch services
     */
    public function register(): void
    {
        // Register HTTP Client
        $this->app->singleton(HttpClientInterface::class, function ($app) {
            $config = config('services.twitch.rate_limit', []);

            return new CurlHttpClient(
                rateLimitEnabled: $config['enabled'] ?? true,
                maxRequestsPerMinute: $config['max_requests'] ?? 800,
                retryAfter: $config['retry_after'] ?? 60,
            );
        });

        // Register OAuth Service
        $this->app->singleton(OAuthInterface::class, function ($app) {
            return new OAuthService(
                httpClient: $app->make(HttpClientInterface::class),
                config: config('services.twitch'),
            );
        });

        // Register Clips Service
        $this->app->singleton(ClipsInterface::class, function ($app) {
            return new ClipsService(
                httpClient: $app->make(HttpClientInterface::class),
                config: config('services.twitch'),
            );
        });

        // Aliases for easier access
        $this->app->alias(OAuthInterface::class, 'twitch.oauth');
        $this->app->alias(ClipsInterface::class, 'twitch.clips');
    }

    /**
     * Bootstrap Twitch services
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the services provided by the provider
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            HttpClientInterface::class,
            OAuthInterface::class,
            ClipsInterface::class,
            'twitch.oauth',
            'twitch.clips',
        ];
    }
}
