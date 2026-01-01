<?php

declare(strict_types=1);

namespace App\Services\Twitch\Traits;

use Illuminate\Support\Facades\Cache;

trait ApiCaching
{
    protected function getCachedResponse(string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? config('twitch.cache_ttl', 3600);

        return Cache::remember($key, $ttl, $callback);
    }

    protected function forgetCache(string $key): void
    {
        Cache::forget($key);
    }
}
