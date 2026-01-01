<?php

namespace App\Services\Twitch;

use App\Actions\Twitch\ExchangeCodeForTokenAction;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;

class TwitchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TwitchApiClient::class, function ($app) {
            return new TwitchApiClient(
                clientId: config('twitch.client_id'),
                clientSecret: config('twitch.client_secret'),
                timeout: config('twitch.timeout', 30),
            );
        });

        $this->app->bind(TwitchTokenManager::class, function ($app) {
            return new TwitchTokenManager(
                clientId: config('twitch.client_id'),
                clientSecret: config('twitch.client_secret'),
                cache: $app->make(Repository::class),
                timeout: config('twitch.timeout', 30),
            );
        });

        $this->app->bind(TwitchDataSanitizer::class, function ($app) {
            return new TwitchDataSanitizer;
        });

        $this->app->bind(TwitchService::class, function ($app) {
            return new TwitchService(
                apiClient: $app->make(TwitchApiClient::class),
                tokenManager: $app->make(TwitchTokenManager::class),
                sanitizer: $app->make(TwitchDataSanitizer::class),
            );
        });

        $this->app->bind(ExchangeCodeForTokenAction::class, function ($app) {
            return new ExchangeCodeForTokenAction(
                apiClient: $app->make(TwitchApiClient::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
