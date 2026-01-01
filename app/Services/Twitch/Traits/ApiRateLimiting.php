<?php

declare(strict_types=1);

namespace App\Services\Twitch\Traits;

use Illuminate\Support\Facades\RateLimiter;

trait ApiRateLimiting
{
    protected function checkRateLimit(string $key, ?int $maxAttempts = null, int $decayMinutes = 60): bool
    {
        $maxAttempts  = $maxAttempts ?? config('twitch.rate_limit.max_requests', 800);
        $decayMinutes = config('twitch.rate_limit.retry_after', 60) / 60; // Convert to minutes

        return RateLimiter::attempt($key, $maxAttempts, function () {
            // Allow the request
        }, $decayMinutes);
    }

    protected function checkActionRateLimit(string $action, ?int $maxAttempts = null, ?int $decayMinutes = null): bool
    {
        $config       = config("twitch.rate_limit.actions.{$action}", ['max' => 60, 'decay' => 60]);
        $maxAttempts  = $maxAttempts ?? $config['max'];
        $decayMinutes = $decayMinutes ?? $config['decay'];

        return $this->checkRateLimit("twitch_{$action}", $maxAttempts, $decayMinutes);
    }
}
