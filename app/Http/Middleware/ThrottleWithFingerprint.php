<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleWithFingerprint
{
    public function __construct(private RateLimiter $limiter) {}

    /**
     * Handle an incoming request with fingerprint-based rate limiting.
     */
    public function handle(Request $request, Closure $next, string $key = 'global', int $maxAttempts = 5, int $decayMinutes = 1): Response
    {
        $fingerprint  = $this->generateFingerprint($request);
        $rateLimitKey = "{$key}:{$fingerprint}";

        if ($this->limiter->tooManyAttempts($rateLimitKey, $maxAttempts)) {
            return response()->json([
                'error'       => 'Too many attempts',
                'retry_after' => $this->limiter->availableIn($rateLimitKey),
                'fingerprint' => $fingerprint, // For debugging (remove in production)
            ], 429, [
                'Retry-After'           => $this->limiter->availableIn($rateLimitKey),
                'X-RateLimit-Limit'     => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $this->limiter->hit($rateLimitKey, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers to response
        $remaining = max(0, $maxAttempts - $this->limiter->attempts($rateLimitKey));
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', $this->limiter->availableAt($rateLimitKey));

        return $response;
    }

    /**
     * Generate a fingerprint based on IP, User-Agent, and Accept-Language.
     */
    private function generateFingerprint(Request $request): string
    {
        return hash('sha256', implode('|', [
            pseudonymize_ip($request->ip()),
            $request->userAgent() ?? '',
            $request->header('Accept-Language') ?? '',
        ]));
    }
}
