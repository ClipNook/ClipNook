<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Services\Cache\DTOs\CacheConfigDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

use function app;
use function config;
use function implode;
use function is_array;
use function md5;
use function serialize;
use function sprintf;

/**
 * Intelligent Query Caching Service.
 *
 * Automatically caches expensive queries with smart invalidation
 */
final class QueryCacheService
{
    /**
     * Cache a query with automatic key generation.
     */
    public function remember(CacheConfigDTO $config, Builder $query): Collection
    {
        $key    = $this->generateKey($config, $query);
        $result = Cache::remember($key, $config->ttl, static fn () => $query->get());
        // Log cache hit/miss
        $this->logCacheMetrics($key, Cache::has($key));

        return $result;
    }

    /**
     * Invalidate cache by tag.
     */
    public function invalidate(string|array $tags): void
    {
        $tagArray = is_array($tags) ? $tags : [$tags];

        // Use cache tags if available (Redis)
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags($tagArray)->flush();

            return;
        }

        // Fallback: Delete only specific keys
        $pattern = 'query:*:'.implode(':', $tagArray).':*';
        $keys    = Cache::get('cache_keys:'.md5($pattern), []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Generate unique cache key from query.
     */
    private function generateKey(CacheConfigDTO $config, Builder $query): string
    {
        $sql       = $query->toSql();
        $bindings  = $query->getBindings();
        $tagString = ! empty($config->tags) ? ':'.implode(':', $config->tags) : '';
        $prefix    = $config->prefix ?: 'query';

        return sprintf('%s:%s%s:%s', $prefix, $config->key, $tagString, md5($sql.serialize($bindings)));
    }

    /**
     * Log cache metrics for monitoring.
     */
    private function logCacheMetrics(string $key, bool $hit): void
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
