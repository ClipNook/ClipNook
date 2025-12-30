<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('auth.login'));
        $middleware->redirectUsersTo(fn () => route('home'));

        // Register the SetLocale middleware alias and append it to the 'web' middleware group.
        // This is configured during bootstrap so middleware is available early in the app lifecycle
        // and works consistently across HTTP and console contexts.
        $middleware->alias([
            'setlocale'     => \App\Http\Middleware\SetLocale::class,
            'trackactivity' => \App\Http\Middleware\TrackLastActivity::class,
        ]);

        // Append to the 'web' group so the locale is applied to all regular HTTP routes
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackLastActivity::class);

        // Add Sanctum middleware to API routes
        $middleware->appendToGroup('api', \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
