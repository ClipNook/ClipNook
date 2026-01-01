<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PerformanceThresholdExceeded;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Notify admins of performance issues
 */
class NotifyPerformanceIssue
{
    public function handle(PerformanceThresholdExceeded $event): void
    {
        Log::warning('Performance threshold exceeded', [
            'metric'    => $event->metric,
            'value'     => $event->value,
            'threshold' => $event->threshold,
            'context'   => $event->context,
        ]);

        // Notify admins
        $admins = \App\Models\User::where('is_admin', true)->get();

        Notification::send($admins, new AdminAlert(
            title: 'Performance Alert',
            message: "{$event->metric} exceeded threshold: {$event->value} (threshold: {$event->threshold})",
            level: 'warning'
        ));
    }
}
