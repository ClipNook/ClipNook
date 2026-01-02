<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Clip;
use App\Models\User;
use App\Repositories\ClipRepository;
use App\Repositories\Contracts\ClipRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Repository service provider.
 * Registers all repository bindings and implementations.
 */
final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // User Repository
        $this->app->bind(UserRepositoryInterface::class, static fn ($app) => new UserRepository(new User()));

        // Clip Repository
        $this->app->bind(ClipRepositoryInterface::class, static fn ($app) => new ClipRepository(new Clip()));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
