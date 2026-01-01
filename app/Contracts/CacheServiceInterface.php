<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface for cache operations.
 *
 * Provides a unified interface for cache backend selection
 * and cache operations with automatic fallback support.
 */
interface CacheServiceInterface
{
    /**
     * Get a value from the cache.
     *
     * @param  string  $key  Cache key
     * @param  mixed  $default  Default value if key doesn't exist
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Store a value in the cache.
     *
     * @param  string  $key  Cache key
     * @param  mixed  $value  Value to store
     * @param  int  $ttl  Time to live in seconds
     * @return bool True if successful
     */
    public function put(string $key, mixed $value, int $ttl): bool;

    /**
     * Store a value in the cache forever.
     *
     * @param  string  $key  Cache key
     * @param  mixed  $value  Value to store
     * @return bool True if successful
     */
    public function forever(string $key, mixed $value): bool;

    /**
     * Remove a value from the cache.
     *
     * @param  string  $key  Cache key
     * @return bool True if successful
     */
    public function forget(string $key): bool;

    /**
     * Remove all values from the cache.
     *
     * @return bool True if successful
     */
    public function flush(): bool;

    /**
     * Check if a key exists in the cache.
     *
     * @param  string  $key  Cache key
     * @return bool True if key exists
     */
    public function has(string $key): bool;

    /**
     * Get the currently active cache driver.
     *
     * @return string Driver name (e.g., 'redis', 'file')
     */
    public function getActiveDriver(): string;

    /**
     * Check if the cache backend is healthy.
     *
     * @return bool True if cache is operational
     */
    public function isHealthy(): bool;
}
