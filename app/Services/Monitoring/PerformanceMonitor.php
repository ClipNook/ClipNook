<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use App\Services\Cache\CacheBackendManager;
use App\Services\Monitoring\DTOs\PerformanceMetricDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

use function app;
use function array_column;
use function array_filter;
use function array_slice;
use function array_sum;
use function config;
use function count;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function function_exists;
use function implode;
use function is_dir;
use function memory_get_peak_usage;
use function memory_get_usage;
use function mkdir;
use function now;
use function storage_path;
use function substr;
use function sys_getloadavg;
use function usort;

final class PerformanceMonitor
{
    public function __construct(
        private CacheBackendManager $cacheBackend,
    ) {}

    public function recordCacheHit(bool $hit): void
    {
        $this->cacheBackend->withRedis(
            static function () use ($hit): void {
                Redis::incr($hit ? 'cache:hits' : 'cache:misses');
            },
            function () use ($hit): void {
                // Fallback: Use file-based storage
                $key        = $hit ? 'cache_hits' : 'cache_misses';
                $data       = $this->readJsonFile('counters');
                $data[$key] = ($data[$key] ?? 0) + 1;
                $this->writeJsonFile('counters', $data);
            }
        );
    }

    public function recordDatabaseQuery(string $query, float $time): void
    {
        $threshold = config('performance.slow_query_threshold', 1000);
        if ($time > $threshold) { // Log slow queries
            $this->recordMetric(new PerformanceMetricDTO(
                name: 'slow_query',
                value: $time,
                tags: ['query' => substr($query, 0, 100)],
                unit: 'ms',
                description: 'Slow database query execution time'
            ));
        }
    }

    public function recordResponseTime(string $route, float $time): void
    {
        $this->recordMetric(new PerformanceMetricDTO(
            name: 'response_time',
            value: $time,
            tags: ['route' => $route],
            unit: 'ms',
            description: 'HTTP response time for route'
        ));
    }

    public function getSystemStats(): array
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak'  => memory_get_peak_usage(true),
            'cpu_load'     => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
            'uptime'       => file_exists('/proc/uptime') ? (int) file_get_contents('/proc/uptime') : null,
        ];
    }

    public function getDatabaseStats(): array
    {
        $stats = [
            'connections'   => DB::getConnections(),
            'queries_count' => 0,
            'slow_queries'  => 0,
        ];
        // Get query count from this request (if available)
        if (app()->bound('db.query_count')) {
            $stats['queries_count'] = app('db.query_count');
        }

        return $stats;
    }

    public function getCacheHitRate(): float
    {
        [$hits, $misses] = $this->cacheBackend->withRedis(
            static function () {
                $hits   = Redis::get('cache:hits') ?? 0;
                $misses = Redis::get('cache:misses') ?? 0;

                return [(int) $hits, (int) $misses];
            },
            function () {
                $data   = $this->readJsonFile('counters');
                $hits   = $data['cache_hits'] ?? 0;
                $misses = $data['cache_misses'] ?? 0;

                return [$hits, $misses];
            }
        );

        $total = $hits + $misses;

        return $total > 0 ? ($hits / $total) * 100 : 0;
    }

    public function getAverageResponseTime(int $minutes = 60): float
    {
        $metrics = $this->getMetrics('response_time', $minutes / 60);
        if (empty($metrics)) {
            return 0;
        }
        $sum = array_sum(array_column($metrics, 'value'));

        return $sum / count($metrics);
    }

    public function getMetrics(string $name, int $hours = 1): array
    {
        return $this->cacheBackend->withRedis(
            static function () use ($name, $hours) {
                $key     = "metrics:{$name}:*";
                $since   = now()->subHours($hours)->timestamp;
                $keys    = Redis::keys($key);
                $metrics = [];
                foreach ($keys as $k) {
                    $data = Redis::zrangebyscore($k, $since, '+inf');
                    foreach ($data as $item) {
                        $metrics[] = json_decode($item, true);
                    }
                }

                return $metrics;
            },
            function () use ($name, $hours) {
                // Fallback: Use file-based storage
                $filename   = "metrics_{$name}";
                $allMetrics = $this->readJsonFile($filename);
                $since      = now()->subHours($hours)->timestamp;

                return array_filter($allMetrics, static fn ($metric) => $metric['timestamp'] >= $since);
            }
        );
    }

    public function recordMetric(PerformanceMetricDTO $metric): void
    {
        $timestamp  = $metric->timestamp ?? now()->timestamp;
        $metricData = [
            'value'       => $metric->value,
            'timestamp'   => $timestamp,
            'tags'        => $metric->tags,
            'unit'        => $metric->unit,
            'description' => $metric->description,
        ];

        $this->cacheBackend->withRedis(
            static function () use ($metric, $timestamp, $metricData): void {
                $key = "metrics:{$metric->name}:".implode(':', $metric->tags);
                Redis::zadd($key, $timestamp, json_encode($metricData));
                // Keep only last 24 hours
                Redis::zremrangebyscore($key, '-inf', $timestamp - 86400);
            },
            function () use ($metric, $timestamp, $metricData): void {
                // Fallback: Use file-based storage
                $filename = "metrics_{$metric->name}";
                $metrics  = $this->readJsonFile($filename);

                // Add new metric
                $metrics[] = $metricData;

                // Keep only last configured hours and limit to configured max entries
                $retentionHours = config('performance.metrics_retention_hours', 24);
                $maxEntries     = config('performance.metrics_max_entries', 1000);
                $cutoff         = $timestamp - ($retentionHours * 3600);
                $metrics        = array_filter($metrics, static fn ($m) => $m['timestamp'] >= $cutoff);

                // Sort by timestamp and keep only recent entries
                usort($metrics, static fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

                $metrics = array_slice($metrics, 0, $maxEntries);

                $this->writeJsonFile($filename, $metrics);
            }
        );
    }

    private function getStoragePath(string $filename): string
    {
        return storage_path(config('performance.storage_path', 'performance').'/'.$filename.'.json');
    }

    private function readJsonFile(string $filename): array
    {
        $path = $this->getStoragePath($filename);
        if (! file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);

        return json_decode($content, true) ?: [];
    }

    private function writeJsonFile(string $filename, array $data): void
    {
        $path = $this->getStoragePath($filename);
        $dir  = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode($data));
    }
}
