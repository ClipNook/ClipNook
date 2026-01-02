<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Redis;

use function class_exists;

/**
 * Trait for checking Redis availability.
 *
 * Eliminates code duplication across monitoring services
 */
trait RedisAvailability
{
    /**
     * Check if Redis is available.
     */
    protected function isRedisAvailable(): bool
    {
        if (! class_exists('Redis')) {
            return false;
        }

        try {
            return Redis::ping() !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get storage backend (redis or file).
     */
    protected function getStorageBackend(): string
    {
        return $this->isRedisAvailable() ? 'redis' : 'file';
    }

    /**
     * Execute callback with appropriate storage backend.
     */
    protected function withStorage(callable $redisCallback, callable $fileCallback): mixed
    {
        return $this->isRedisAvailable()
            ? $redisCallback()
            : $fileCallback();
    }
}
