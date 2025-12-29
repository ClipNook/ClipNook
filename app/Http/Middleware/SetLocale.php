<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

/**
 * Middleware to determine and apply the request's locale.
 *
 * Precedence: query param 'lang' > session 'locale' > cookie 'locale' > Accept-Language header.
 * If a supported locale is found, sets the application and Carbon locale.
 */
class SetLocale
{
    /**
     * Handle an incoming request and set the locale if supported.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get available locales from config
        $availableLocales = array_keys(Config::get('app.locales', []));

        // Determine locale by precedence
        $locale = $this->resolveLocale($request, $availableLocales);

        // Set locale if valid
        if ($locale) {
            AppFacade::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Resolve the locale from request by precedence.
     */
    protected function resolveLocale(Request $request, array $availableLocales): ?string
    {
        // 1. Query parameter
        $locale = $request->query('lang');
        if ($locale && in_array($locale, $availableLocales, true)) {
            return $locale;
        }

        // 2. Session
        $locale = $request->session()->get('locale');
        if ($locale && in_array($locale, $availableLocales, true)) {
            return $locale;
        }

        // 3. Cookie
        $locale = Cookie::get('locale');
        if ($locale && in_array($locale, $availableLocales, true)) {
            return $locale;
        }

        // 4. Accept-Language header
        $locale = $request->getPreferredLanguage($availableLocales);
        if ($locale && in_array($locale, $availableLocales, true)) {
            return $locale;
        }

        // No valid locale found
        return null;
    }
}
