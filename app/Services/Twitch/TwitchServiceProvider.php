<?php

declare(strict_types=1);

namespace App\Services\Twitch;

use App\Contracts\ImageValidatorInterface;
use App\Services\Twitch\Api\ClipApiService;
use App\Services\Twitch\Api\DataSanitizerService;
use App\Services\Twitch\Api\GameApiService;
use App\Services\Twitch\Api\StreamerApiService;
use App\Services\Twitch\Api\TwitchApiClient;
use App\Services\Twitch\Api\VideoApiService;
use App\Services\Twitch\Auth\TwitchTokenManager;
use App\Services\Twitch\Contracts\TwitchApiClientInterface;
use App\Services\Twitch\Media\TwitchMediaService;
use Illuminate\Support\ServiceProvider;

use function config;

final class TwitchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Core API Client
        $this->app->bind(TwitchApiClientInterface::class, static fn ($app) => new TwitchApiClient(
            clientId: config('twitch.client_id'),
            clientSecret: config('twitch.client_secret'),
        ));

        // Specialized API Services
        $this->app->bind(ClipApiService::class, static fn ($app) => new ClipApiService(
            apiClient: $app->make(TwitchApiClientInterface::class),
            sanitizer: $app->make(DataSanitizerService::class),
            tokenManager: $app->make(TwitchTokenManager::class),
        ));

        $this->app->bind(GameApiService::class, static fn ($app) => new GameApiService(
            apiClient: $app->make(TwitchApiClientInterface::class),
            sanitizer: $app->make(DataSanitizerService::class),
            tokenManager: $app->make(TwitchTokenManager::class),
        ));

        $this->app->bind(StreamerApiService::class, static fn ($app) => new StreamerApiService(
            apiClient: $app->make(TwitchApiClientInterface::class),
            sanitizer: $app->make(DataSanitizerService::class),
            tokenManager: $app->make(TwitchTokenManager::class),
        ));

        $this->app->bind(VideoApiService::class, static fn ($app) => new VideoApiService(
            apiClient: $app->make(TwitchApiClientInterface::class),
            sanitizer: $app->make(DataSanitizerService::class),
            tokenManager: $app->make(TwitchTokenManager::class),
        ));

        // Auth & Media Services
        $this->app->bind(TwitchTokenManager::class, static fn ($app) => new TwitchTokenManager(
            clientId: config('twitch.client_id'),
            clientSecret: config('twitch.client_secret'),
        ));

        $this->app->bind(TwitchMediaService::class, static fn ($app) => new TwitchMediaService(
            imageValidator: $app->make(ImageValidatorInterface::class),
        ));

        // Utility Services
        $this->app->bind(DataSanitizerService::class, static fn ($app) => new DataSanitizerService());
    }

    public function boot(): void {}
}
