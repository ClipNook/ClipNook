<?php

namespace App\Providers;

use App\Models\Clip;
use App\Observers\ClipObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind service interfaces to implementations
        $this->bindServiceInterfaces();

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bind service contracts to their implementations.
     *
     * @return void
     */
    protected function bindServiceInterfaces(): void
    {
        // Twitch contracts
        $this->app->bind(
            \App\Services\Twitch\Contracts\DownloadInterface::class,
            \App\Services\Twitch\TwitchService::class
        );

        // Application contracts
        $this->app->bind(
            \App\Contracts\ClipServiceInterface::class,
            \App\Services\ClipService::class
        );

        $this->app->bind(
            \App\Contracts\GDPRServiceInterface::class,
            \App\Services\GDPR\GDPRService::class
        );

        $this->app->bind(
            \App\Contracts\ImageValidatorInterface::class,
            \App\Services\Image\ImageValidator::class
        );

        $this->app->bind(
            \App\Contracts\CacheServiceInterface::class,
            \App\Services\Cache\CacheBackendManager::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local')) {
            Mail::alwaysTo('local@localhost');
        }

        // Register model observers
        Clip::observe(ClipObserver::class);

        // Register custom notification channels
        $this->app->make(\Illuminate\Notifications\ChannelManager::class)->extend('ntfy', function ($app) {
            return new \App\Notifications\Channels\NtfyChannel;
        });

        // Paginator::defaultView('vendor.pagination.tailwind');

        $this->configureLocale();
        $this->configureSecureUrls();

        // Prevent lazy loading in local environment to catch N+1 problems
        if ($this->app->environment('local')) {
            \Illuminate\Database\Eloquent\Model::preventLazyLoading();
        }
    }

    protected function configureLocale(): void
    {
        // Use the same session key as middleware ('locale'). Set application and Carbon locale.
        $locale = Session::get('locale', config('app.locale'));
        if (is_string($locale) && $locale !== '') {
            App::setLocale($locale);
            \Carbon\Carbon::setLocale($locale);
        }
    }

    protected function configureSecureUrls(): void
    {
        // Determine if HTTPS should be enforced
        $enforceHttps = $this->app->environment(['production', 'staging'])
            && ! $this->app->runningUnitTests();

        // Force HTTPS for all generated URLs when appropriate.
        if ($enforceHttps) {
            // Use forceScheme('https') which exists on URL facade in Laravel.
            URL::forceScheme('https');

            // Ensure proper server variable is set for some runtime environments
            if (isset($this->app['request'])) {
                $this->app['request']->server->set('HTTPS', 'on');
            }
        }
    }
}
