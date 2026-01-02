<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use function array_keys;
use function config;
use function explode;
use function in_array;
use function session;
use function trim;

final class SetLocale
{
    /**
     * Supported locales from config.
     */
    private array $supportedLocales;

    public function __construct()
    {
        $this->supportedLocales = array_keys(config('app.locales', ['en' => 'English']));
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        if ($locale && in_array($locale, $this->supportedLocales, true)) {
            App::setLocale($locale);

            // Store in session for persistence
            session(['locale' => $locale]);
        }

        return $next($request);
    }

    /**
     * Determine the locale from various sources.
     */
    private function determineLocale(Request $request): ?string
    {
        // 1. Check URL parameter (highest priority)
        if ($request->has('lang') && in_array($request->lang, $this->supportedLocales, true)) {
            return $request->lang;
        }

        // 2. Check authenticated user's preference
        if (Auth::check() && Auth::user()->locale ?? false) {
            return Auth::user()->locale;
        }

        // 3. Check session
        if (session()->has('locale')) {
            return session('locale');
        }

        // 4. Check cookie
        if ($request->cookie('locale')) {
            return $request->cookie('locale');
        }

        // 5. Check browser language (fallback)
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 6. Default to app locale
        return config('app.locale', 'en');
    }

    /**
     * Get browser's preferred language.
     */
    private function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (! $acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = explode(',', $acceptLanguage);

        foreach ($languages as $language) {
            $locale = trim(explode(';', $language)[0]);

            // Check for exact match
            if (in_array($locale, $this->supportedLocales, true)) {
                return $locale;
            }

            // Check for language prefix (e.g., 'en' from 'en-US')
            $prefix = explode('-', $locale)[0];
            if (in_array($prefix, $this->supportedLocales, true)) {
                return $prefix;
            }
        }

        return null;
    }
}
