<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local')) {
            Mail::alwaysTo('local@localhost');
        }

        // Paginator::defaultView('vendor.pagination.tailwind');

        $this->configureLocale();
        $this->configureSecureUrls();
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
