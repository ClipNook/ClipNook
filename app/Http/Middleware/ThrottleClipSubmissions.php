<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for rate limiting clip submissions.
 *
 * Implements tiered rate limiting based on user verification status
 * to prevent spam while allowing legitimate users reasonable access.
 */
class ThrottleClipSubmissions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return response()->json([
                'message' => __('clips.unauthorized'),
            ], 401);
        }

        $key          = $this->resolveRequestSignature($request);
        $maxAttempts  = $this->getMaxAttempts($request);
        $decaySeconds = $this->getDecaySeconds($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message'     => __('clips.rate_limit_exceeded', ['seconds' => $seconds]),
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }

    /**
     * Resolve the rate limiter key for the request.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return 'clip-submission:'.$request->user()->id;
    }

    /**
     * Get the maximum number of attempts based on user status.
     */
    protected function getMaxAttempts(Request $request): int
    {
        return $request->user()->hasVerifiedEmail() ? 20 : 5;
    }

    /**
     * Get the decay time in seconds (24 hours).
     */
    protected function getDecaySeconds(Request $request): int
    {
        return 86400; // 24 hours
    }
}
