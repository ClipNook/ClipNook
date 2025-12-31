<?php

namespace App\Http\Controllers;

use App\Services\Cache\CacheBackendManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    public function __construct(
        private CacheBackendManager $cacheBackend,
    ) {}

    /**
     * Perform comprehensive health check.
     */
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database'     => $this->checkDatabase(),
            'cache'        => $this->checkCache(),
            'redis'        => $this->checkRedis(),
            'storage'      => $this->checkStorage(),
            'twitch_api'   => $this->checkTwitchApi(),
            'memory_usage' => $this->checkMemoryUsage(),
            'load_average' => $this->checkLoadAverage(),
        ];

        $healthy = ! in_array(false, $checks, true);

        return response()->json([
            'status'      => $healthy ? 'healthy' : 'degraded',
            'timestamp'   => now()->toISOString(),
            'version'     => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
            'checks'      => $checks,
        ], $healthy ? 200 : 503);
    }

    /**
     * Check database connectivity.
     */
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            // Test a simple query
            DB::select('SELECT 1');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check cache functionality.
     */
    private function checkCache(): bool
    {
        try {
            $testKey = 'health_check_'.time();
            Cache::put($testKey, true, 10);
            $result = Cache::get($testKey);
            Cache::forget($testKey);

            return $result === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check Redis connectivity (if available).
     */
    private function checkRedis(): bool
    {
        return $this->cacheBackend->isRedisAvailable();
    }

    /**
     * Check storage accessibility.
     */
    private function checkStorage(): bool
    {
        try {
            $testFile = 'health_check_'.time().'.tmp';
            Storage::disk('local')->put($testFile, 'test');
            $exists = Storage::disk('local')->exists($testFile);
            Storage::disk('local')->delete($testFile);

            return $exists;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check Twitch API connectivity.
     */
    private function checkTwitchApi(): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders([
                    'Client-ID'     => config('twitch.client_id'),
                    'Authorization' => 'Bearer '.config('twitch.client_secret'),
                ])
                ->get('https://api.twitch.tv/helix/games/top?first=1');

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check memory usage.
     */
    private function checkMemoryUsage(): array
    {
        $usage = memory_get_usage(true);
        $peak  = memory_get_peak_usage(true);
        $limit = $this->getMemoryLimit();

        return [
            'current'    => $this->formatBytes($usage),
            'peak'       => $this->formatBytes($peak),
            'limit'      => $this->formatBytes($limit),
            'percentage' => $limit > 0 ? round(($usage / $limit) * 100, 2) : 0,
            'healthy'    => $limit === 0 || ($usage / $limit) < 0.8, // Less than 80%
        ];
    }

    /**
     * Check system load average (Unix-like systems).
     */
    private function checkLoadAverage(): array
    {
        if (! function_exists('sys_getloadavg')) {
            return ['available' => false];
        }

        $load  = sys_getloadavg();
        $cores = $this->getCpuCores();

        return [
            '1min'    => round($load[0], 2),
            '5min'    => round($load[1], 2),
            '15min'   => round($load[2], 2),
            'cores'   => $cores,
            'healthy' => $cores > 0 ? $load[0] < $cores : true,
        ];
    }

    /**
     * Get memory limit in bytes.
     */
    private function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        if ($limit === '-1') {
            return 0; // Unlimited
        }

        return $this->parseSize($limit);
    }

    /**
     * Get number of CPU cores.
     */
    private function getCpuCores(): int
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return (int) getenv('NUMBER_OF_PROCESSORS') ?: 1;
        }

        return (int) shell_exec('nproc') ?: 1;
    }

    /**
     * Parse size string to bytes.
     */
    private function parseSize(string $size): int
    {
        $unit  = strtolower(substr($size, -1));
        $value = (int) substr($size, 0, -1);

        return match ($unit) {
            'g'     => $value * 1024 * 1024 * 1024,
            'm'     => $value * 1024 * 1024,
            'k'     => $value * 1024,
            default => (int) $size,
        };
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
