<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Services\Cache\CacheBackendManager;
use Illuminate\Support\Facades\Redis;
use Log;

use function array_filter;
use function array_map;
use function array_values;
use function config;
use function count;
use function date;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function storage_path;
use function time;
use function uniqid;
use function unlink;

final class LoginMonitor
{
    public function __construct(
        private CacheBackendManager $cacheBackend,
    ) {}

    public function recordAttempt(string $identifier, bool $successful): void
    {
        $this->cacheBackend->withRedis(
            function () use ($identifier): void {
                $key = "login_attempts:{$identifier}";
                Redis::zadd($key, time(), uniqid('', true));
                Redis::expire($key, $this->getAttemptWindow());
            },
            function () use ($identifier): void {
                // Fallback: Use file-based storage
                $filename = "attempts_{$identifier}";
                $data     = $this->readJsonFile($filename);
                $data[]   = time();
                $this->writeJsonFile($filename, $data);
            }
        );

        if (! $successful) {
            $this->checkForSuspiciousActivity($identifier);
        }
    }

    public function isLocked(string $identifier): bool
    {
        return $this->cacheBackend->withRedis(
            static function () use ($identifier) {
                $lockKey = "login_locked:{$identifier}";

                return Redis::exists($lockKey) > 0;
            },
            function () use ($identifier) {
                // Fallback: Check file-based lock
                $filename = "locked_{$identifier}";
                $data     = $this->readJsonFile($filename);

                if (empty($data) || ! isset($data['locked_until'])) {
                    return false;
                }

                if (time() > $data['locked_until']) {
                    // Lock has expired, remove it
                    unlink($this->getStoragePath($filename));

                    return false;
                }

                return true;
            }
        );
    }

    public function getRemainingAttempts(string $identifier): int
    {
        if ($this->isLocked($identifier)) {
            return 0;
        }

        $attempts = $this->cacheBackend->withRedis(
            function () use ($identifier) {
                $key         = "login_attempts:{$identifier}";
                $now         = time();
                $windowStart = $now - $this->getAttemptWindow();

                Redis::zremrangebyscore($key, '-inf', $windowStart);

                return Redis::zcard($key);
            },
            function () use ($identifier) {
                $filename = "attempts_{$identifier}";
                $data     = $this->readJsonFile($filename);

                // Remove old entries
                $windowStart = time() - $this->getAttemptWindow();
                $data        = array_filter($data, static fn ($timestamp) => $timestamp > $windowStart);

                $attempts = count($data);
                // Save cleaned data
                $this->writeJsonFile($filename, $data);

                return $attempts;
            }
        );

        $this->cacheBackend->withRedis(
            function () use ($identifier): void {
                $lockKey = "login_locked:{$identifier}";
                Redis::setex($lockKey, $this->getLockoutTime(), 1);
            },
            function () use ($identifier): void {
                // Fallback: Use file-based lock
                $filename = "locked_{$identifier}";
                $data     = [
                    'locked_until' => time() + $this->getLockoutTime(),
                    'identifier'   => $identifier,
                ];
                $this->writeJsonFile($filename, $data);
            }
        );
    }

    public function getAttemptHistory(string $identifier, int $days = 7): array
    {
        return $this->cacheBackend->withRedis(
            static function () use ($identifier, $days) {
                $key   = "login_attempts:{$identifier}";
                $since = time() - ($days * 86400);

                $attempts = Redis::zrangebyscore($key, $since, '+inf', ['WITHSCORES' => true]);

                return array_map(static fn ($timestamp) => date('Y-m-d H:i:s', $timestamp), array_values($attempts));
            },
            function () use ($identifier, $days) {
                // Fallback: Use file-based storage
                $filename = "attempts_{$identifier}";
                $data     = $this->readJsonFile($filename);
                $since    = time() - ($days * 86400);

                $recentAttempts = array_filter($data, static fn ($timestamp) => $timestamp > $since);

                return array_map(static fn ($timestamp) => date('Y-m-d H:i:s', $timestamp), $recentAttempts);
            }
        );
    }

    private function getLockoutTime(): int
    {
        return config('performance.login_monitoring.lockout_time', 3600);
    }

    private function getMaxAttempts(): int
    {
        return config('performance.login_monitoring.max_attempts', 5);
    }

    private function getAttemptWindow(): int
    {
        return config('performance.login_monitoring.attempt_window', 3600);
    }

    private function getStoragePath(string $filename): string
    {
        $path = config('performance.login_monitoring.storage_path', 'login_monitoring');

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

    private function checkForSuspiciousActivity(string $identifier): void
    {
        $remaining = $this->getRemainingAttempts($identifier);

        if ($remaining === 0) {
            $this->lock($identifier);
            $this->notifyAdmins($identifier);
        }
    }

    private function notifyAdmins(string $identifier): void
    {
        $admins = \App\Models\User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            // For now, just log - email would need to be configured
            Log::warning("Suspicious login attempts detected for: {$identifier}");
        }
    }
}
