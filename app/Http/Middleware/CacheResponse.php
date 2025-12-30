<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request with response caching.
     */
    public function handle(Request $request, Closure $next, ?int $ttl = null): Response
    {
        $ttl = $ttl ?? config('performance.response_cache.default_ttl', 300);

        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Skip caching for authenticated requests (personalized content)
        if ($request->user()) {
            return $next($request);
        }

        // Generate cache key from URL and query params
        $key = 'response:'.md5($request->fullUrl());

        // Check if cached response exists
        if (Cache::has($key)) {
            $cached = Cache::get($key);

            return response()->json($cached['data'], $cached['status'])
                ->header('X-Cache-Hit', 'true')
                ->header('X-Cache-TTL', Cache::ttl($key));
        }

        // Process request
        $response = $next($request);

        // Cache successful responses
        if ($response->isSuccessful() && $response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);

            // Only cache if we have valid JSON data
            if ($data !== null) {
                Cache::put($key, [
                    'data'      => $data,
                    'status'    => $response->status(),
                    'cached_at' => now()->toISOString(),
                ], $ttl);
            }
        }

        return $response->header('X-Cache-Hit', 'false');
    }
}
