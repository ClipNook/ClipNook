<?php

declare(strict_types=1);

namespace App\Services\Logging;

use Illuminate\Support\Facades\Log;

use function array_merge;
use function auth;
use function config;
use function now;
use function request;

use const PHP_FLOAT_MAX;

final class StructuredLogger
{
    public static function security(string $event, array $context = []): void
    {
        Log::channel('security')->warning($event, array_merge($context, [
            'timestamp' => now()->toIso8601String(),
            'ip'        => request()->ip(),
            'user_id'   => auth()->id(),
        ]));
    }

    public static function performance(string $metric, float $value, array $context = []): void
    {
        if ($value > config('performance.thresholds.'.$metric, PHP_FLOAT_MAX)) {
            Log::channel('performance')->warning("Performance threshold exceeded: {$metric}", [
                'metric'    => $metric,
                'value'     => $value,
                'threshold' => config('performance.thresholds.'.$metric),
                'context'   => $context,
            ]);
        }
    }

    public static function gdpr(string $action, int $userId, array $details = []): void
    {
        Log::channel('gdpr')->info($action, [
            'user_id'   => $userId,
            'action'    => $action,
            'details'   => $details,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
