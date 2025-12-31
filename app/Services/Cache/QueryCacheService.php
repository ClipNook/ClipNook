<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Services\Cache\DTOs\CacheConfigDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Intelligent Query Caching Service
 *
 * Automatically caches expensive queries with smart invalidation
 */
class QueryCacheService
{
    /**
     * Cache a query with automatic key generation
     */
    public function remember(CacheConfigDTO $config, Builder $query): Collection
    {
        $key    = $this->generateKey($config, $query);
        $result = Cache::remember($key, $config->ttl, fn () => $query->get());
        // Log cache hit/miss
        $this->logCacheMetrics($key, Cache::has($key));

        return $result;
    }

    /**
     * Invalidate cache by tag
     */
    public function invalidate(string|array $tags): void
    {
        $tagArray = is_array($tags) ? $tags : [$tags];
        $pattern  = 'query:*:'.implode(':', $tagArray).':*';
        // For database/file cache, we can't easily invalidate by pattern
        // This is a limitation - in production use Redis for proper tagging
        Cache::flush(); // Fallback: clear all cache
    }

    /**
     * Generate unique cache key from query
     */
    protected function generateKey(CacheConfigDTO $config, Builder $query): string
    {
        $sql       = $query->toSql();
        $bindings  = $query->getBindings();
        $tagString = ! empty($config->tags) ? ':'.implode(':', $config->tags) : '';
        $prefix    = $config->prefix ?: 'query';

        return sprintf('%s:%s%s:%s', $prefix, $config->key, $tagString, md5($sql.serialize($bindings)));
    }

    /**
     * Log cache metrics for monitoring
     */
    protected function logCacheMetrics(string $key, bool $hit): void
    {
        if (config('performance.cache.log_metrics', false)) {
            app(\App\Services\Monitoring\PerformanceMonitor::class)->recordMetric(
                new \App\Services\Monitoring\DTOs\PerformanceMetricDTO(
                    name: 'cache_hit',
                    value: $hit ? 1.0 : 0.0,
                    tags: ['key' => $key],
                    unit: 'boolean',
                    description: 'Cache hit/miss indicator'
                )
            );
        }
    }
}
