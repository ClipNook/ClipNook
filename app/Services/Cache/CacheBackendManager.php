<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use function class_exists;
use function config;
use function method_exists;
use function uniqid;

/**
 * Centralized cache backend manager for Redis availability checks
 * Provides unified interface for cache backend operations and availability checks
 * across the application, eliminating code duplication.
 */
final class CacheBackendManager
{
    /**
     * Check if Redis is available and responding.
     */
    public function isRedisAvailable(): bool
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
     * Check if the default cache store is available.
     */
    public function isCacheAvailable(): bool
    {
        try {
            $store = Cache::store();
            // Try to set and get a test value
            $testKey = 'cache_test_'.uniqid();
            $store->put($testKey, 'test', 1);
            $result = $store->get($testKey);
            $store->forget($testKey);

            return $result === 'test';
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the current cache backend type.
     */
    public function getCurrentBackend(): string
    {
        return config('cache.default', 'file');
    }

    /**
     * Get the recommended storage backend based on availability.
     */
    public function getRecommendedBackend(): string
    {
        return $this->isRedisAvailable() ? 'redis' : 'file';
    }

    /**
     * Execute callback with Redis if available, fallback otherwise.
     */
    public function withRedis(callable $redisCallback, callable $fallbackCallback): mixed
    {
        return $this->isRedisAvailable() ? $redisCallback() : $fallbackCallback();
    }

    /**
     * Execute callback with cache store if available, fallback otherwise.
     */
    public function withCache(callable $cacheCallback, callable $fallbackCallback): mixed
    {
        return $this->isCacheAvailable() ? $cacheCallback() : $fallbackCallback();
    }

    /**
     * Get cache statistics if available.
     */
    public function getCacheStats(): ?array
    {
        if (! $this->isCacheAvailable()) {
            return null;
        }

        try {
            $store = Cache::store();

            if (method_exists($store, 'getStats')) {
                return $store->getStats();
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get Redis connection info if available.
     */
    public function getRedisInfo(): ?array
    {
        if (! $this->isRedisAvailable()) {
            return null;
        }

        try {
            return Redis::info();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Check if Redis is configured in the application.
     */
    public function isRedisConfigured(): bool
    {
        return config('database.redis') !== null;
    }

    /**
     * Get health status for monitoring.
     */
    public function getHealthStatus(): array
    {
        return [
            'redis_available'     => $this->isRedisAvailable(),
            'cache_available'     => $this->isCacheAvailable(),
            'current_backend'     => $this->getCurrentBackend(),
            'recommended_backend' => $this->getRecommendedBackend(),
            'redis_configured'    => $this->isRedisConfigured(),
        ];
    }
}
