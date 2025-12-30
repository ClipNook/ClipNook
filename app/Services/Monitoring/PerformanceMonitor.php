<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class PerformanceMonitor
{
    private function isRedisAvailable(): bool
    {
        if (! class_exists('Redis')) {
            return false;
        }

        try {
            Redis::ping();

            return true;
        } catch (\Exception $e) {
            return false;
        }
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

    public function recordCacheHit(bool $hit): void
    {
        if ($this->isRedisAvailable()) {
            Redis::incr($hit ? 'cache:hits' : 'cache:misses');

            return;
        }

        // Fallback: Use file-based storage
        $key        = $hit ? 'cache_hits' : 'cache_misses';
        $data       = $this->readJsonFile('counters');
        $data[$key] = ($data[$key] ?? 0) + 1;
        $this->writeJsonFile('counters', $data);
    }

    public function recordDatabaseQuery(string $query, float $time): void
    {
        $threshold = config('performance.slow_query_threshold', 1000);
        if ($time > $threshold) { // Log slow queries
            $this->recordMetric('slow_query', $time, ['query' => substr($query, 0, 100)]);
        }
    }

    public function recordResponseTime(string $route, float $time): void
    {
        $this->recordMetric('response_time', $time, ['route' => $route]);
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
        if ($this->isRedisAvailable()) {
            $hits   = Redis::get('cache:hits') ?? 0;
            $misses = Redis::get('cache:misses') ?? 0;
        } else {
            $data   = $this->readJsonFile('counters');
            $hits   = $data['cache_hits'] ?? 0;
            $misses = $data['cache_misses'] ?? 0;
        }

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
        if ($this->isRedisAvailable()) {
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
        }

        // Fallback: Use file-based storage
        $filename   = "metrics_{$name}";
        $allMetrics = $this->readJsonFile($filename);
        $since      = now()->subHours($hours)->timestamp;

        return array_filter($allMetrics, function ($metric) use ($since) {
            return $metric['timestamp'] >= $since;
        });
    }

    public function recordMetric(string $name, float $value, array $tags = []): void
    {
        $timestamp = now()->timestamp;
        $metric    = [
            'value'     => $value,
            'timestamp' => $timestamp,
            'tags'      => $tags,
        ];

        if ($this->isRedisAvailable()) {
            $key = "metrics:{$name}:".implode(':', $tags);
            Redis::zadd($key, $timestamp, json_encode($metric));
            // Keep only last 24 hours
            Redis::zremrangebyscore($key, '-inf', $timestamp - 86400);

            return;
        }

        // Fallback: Use file-based storage
        $filename = "metrics_{$name}";
        $metrics  = $this->readJsonFile($filename);

        // Add new metric
        $metrics[] = $metric;

        // Keep only last configured hours and limit to configured max entries
        $retentionHours = config('performance.metrics_retention_hours', 24);
        $maxEntries     = config('performance.metrics_max_entries', 1000);
        $cutoff         = $timestamp - ($retentionHours * 3600);
        $metrics        = array_filter($metrics, function ($m) use ($cutoff) {
            return $m['timestamp'] >= $cutoff;
        });

        // Sort by timestamp and keep only recent entries
        usort($metrics, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        $metrics = array_slice($metrics, 0, $maxEntries);

        $this->writeJsonFile($filename, $metrics);
    }
}
