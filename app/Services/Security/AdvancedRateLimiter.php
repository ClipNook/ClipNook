<?php

namespace App\Services\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AdvancedRateLimiter
{
    private function getWindowSize(): int
    {
        return config('performance.rate_limiting.window_size', 60);
    }

    private function getMaxRequests(): int
    {
        return config('performance.rate_limiting.max_requests', 60);
    }

    private function getBurstLimit(): int
    {
        return config('performance.rate_limiting.burst_limit', 10);
    }

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
        $path = config('performance.rate_limiting.storage_path', 'rate_limiting');

        return storage_path($path.'/'.$filename.'.json');
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

    public function attempt(Request $request, string $action): bool
    {
        $key         = $this->getKey($request, $action);
        $safeKey     = $this->getSafeFilename($key);
        $now         = time();
        $windowStart = $now - $this->getWindowSize();

        if ($this->isRedisAvailable()) {
            // Remove old entries
            Redis::zremrangebyscore($key, '-inf', $windowStart);

            // Count requests in current window
            $requestCount = Redis::zcard($key);

            // Check burst protection
            $recentRequests = Redis::zcount($key, $now - 5, $now);
            if ($recentRequests >= $this->getBurstLimit()) {
                return false;
            }

            // Check overall limit
            if ($requestCount >= $this->getMaxRequests()) {
                return false;
            }

            // Add current request
            Redis::zadd($key, $now, uniqid('', true));
            Redis::expire($key, $this->getWindowSize());

            return true;
        }

        // Fallback: Use file-based storage
        $data = $this->readJsonFile($safeKey);

        // Remove old entries
        $data = array_filter($data, function ($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });

        // Count requests in current window
        $requestCount = count($data);

        // Check burst protection (requests in last 5 seconds)
        $recentRequests = count(array_filter($data, function ($timestamp) use ($now) {
            return $timestamp > $now - 5;
        }));

        if ($recentRequests >= $this->getBurstLimit()) {
            return false;
        }

        // Check overall limit
        if ($requestCount >= $this->getMaxRequests()) {
            return false;
        }

        // Add current request
        $data[] = $now;
        $this->writeJsonFile($safeKey, $data);

        return true;
    }

    public function remaining(Request $request, string $action): int
    {
        $key     = $this->getKey($request, $action);
        $safeKey = $this->getSafeFilename($key);

        if ($this->isRedisAvailable()) {
            $count = Redis::zcard($key);
        } else {
            $data = $this->readJsonFile($safeKey);
            // Remove old entries
            $windowStart = time() - $this->getWindowSize();
            $data        = array_filter($data, function ($timestamp) use ($windowStart) {
                return $timestamp > $windowStart;
            });
            $count = count($data);
            // Save cleaned data
            $this->writeJsonFile($safeKey, $data);
        }

        return max(0, $this->getMaxRequests() - $count);
    }

    public function resetIn(Request $request, string $action): int
    {
        $key     = $this->getKey($request, $action);
        $safeKey = $this->getSafeFilename($key);

        if ($this->isRedisAvailable()) {
            $oldest = Redis::zrange($key, 0, 0, ['WITHSCORES' => true]);

            if (empty($oldest)) {
                return 0;
            }

            $oldestTimestamp = array_values($oldest)[0];
            $windowEnd       = $oldestTimestamp + $this->getWindowSize();

            return max(0, $windowEnd - time());
        }

        // Fallback: Use file-based storage
        $data = $this->readJsonFile($safeKey);

        if (empty($data)) {
            return 0;
        }

        $oldestTimestamp = min($data);
        $windowEnd       = $oldestTimestamp + $this->getWindowSize();

        return max(0, $windowEnd - time());
    }

    private function getKey(Request $request, string $action): string
    {
        $identifier = $request->user()?->id ?? pseudonymize_ip($request->ip());

        return "rate_limit:{$action}:{$identifier}";
    }

    private function getSafeFilename(string $key): string
    {
        // Replace problematic characters with underscores
        return preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $key);
    }
}
