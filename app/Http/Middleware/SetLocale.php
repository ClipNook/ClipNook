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
 * Precedence: query param 'lang' -> session 'locale' -> cookie 'locale' -> Accept-Language header.
 * When a supported locale is found, the middleware sets the application and Carbon locale.
 */
class SetLocale
{
    /**
     * Handle an incoming request and set locale if supported.
     */
    public function handle(Request $request, Closure $next)
    {
        // Determine locale according to the documented precedence
        $locale = $request->query('lang') ?: $request->session()->get('locale') ?: Cookie::get('locale');

        $available = array_keys(Config::get('app.locales', []));

        if (! $locale) {
            // try from Accept-Language header
            $locale = $request->getPreferredLanguage($available) ?: null;
        }

        if ($locale && in_array($locale, $available, true)) {
            AppFacade::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
