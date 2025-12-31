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
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // User Repository
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository(new User);
        });

        // Clip Repository
        $this->app->bind(ClipRepositoryInterface::class, function ($app) {
            return new ClipRepository(new Clip);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
