<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActivity
{
    /**
     * Routes to exclude from activity tracking.
     */
    protected array $excludeRoutes;

    public function __construct()
    {
        $this->excludeRoutes = config('activity.skip_routes', [
            'api/health',
            '_debugbar/*',
            'telescope/*',
            'horizon/*',
            'pulse/*',
        ]);
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip tracking for excluded routes
        if ($this->shouldSkipTracking($request)) {
            return $response;
        }

        $this->trackActivity($request);

        return $response;
    }

    /**
     * Check if activity tracking should be skipped.
     */
    protected function shouldSkipTracking(Request $request): bool
    {
        $currentRoute = $request->route()?->getName();

        // Skip API health checks and debug routes
        foreach ($this->excludeRoutes as $pattern) {
            if (str_contains($currentRoute, $pattern) || $request->is($pattern)) {
                return true;
            }
        }

        // Skip non-GET requests that are likely API calls
        if (! $request->isMethod('GET') && $request->is('api/*')) {
            return true;
        }

        // Skip AJAX requests if configured
        if (config('activity.skip_ajax', true) && $request->ajax()) {
            return true;
        }

        return false;
    }

    /**
     * Track user activity.
     */
    protected function trackActivity(Request $request): void
    {
        $now = now();

        // Track in session
        session([config('activity.session_key', 'last_activity') => $now->timestamp]);

        // Track in database for authenticated users
        if (Auth::check()) {
            $this->updateUserLastActivity(Auth::id(), $now);
        }

        // Track guest activity if enabled
        if (config('activity.track_guests', false) && ! Auth::check()) {
            $this->trackGuestActivity($request, $now);
        }
    }

    /**
     * Update user's last activity in database.
     */
    protected function updateUserLastActivity(int $userId, $timestamp): void
    {
        // Only update if it's been more than the configured interval
        $updateInterval = config('activity.update_interval', 300); // 5 minutes default

        $lastUpdate = cache()->get(config('activity.cache_prefix', 'activity_')."user_{$userId}");

        if (! $lastUpdate || ($timestamp->timestamp - $lastUpdate) > $updateInterval) {
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'last_activity_at' => $timestamp,
                    'updated_at'       => $timestamp,
                ]);

            cache()->put(config('activity.cache_prefix', 'activity_')."user_{$userId}", $timestamp->timestamp, $updateInterval);
        }
    }

    /**
     * Track guest activity.
     */
    protected function trackGuestActivity(Request $request, $timestamp): void
    {
        $sessionId = session()->getId();
        $ip        = $request->ip();
        $userAgent = $request->userAgent();

        // Store guest activity in cache or database
        cache()->put(
            config('activity.cache_prefix', 'activity_')."guest_{$sessionId}",
            [
                'ip'            => $ip,
                'user_agent'    => $userAgent,
                'last_activity' => $timestamp,
                'url'           => $request->fullUrl(),
            ],
            config('activity.guest_cache_ttl', 3600) // 1 hour
        );
    }
}
