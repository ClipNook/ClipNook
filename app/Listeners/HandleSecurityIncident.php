<?php

namespace App\Listeners;

use App\Events\SecurityIncidentDetected;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Handle security incidents
 */
class HandleSecurityIncident
{
    public function handle(SecurityIncidentDetected $event): void
    {
        // Log incident
        Log::critical('Security incident detected', [
            'type'       => $event->type,
            'identifier' => $event->identifier,
            'severity'   => $event->severity,
            'details'    => $event->details,
        ]);

        // Lock account if needed
        if ($event->severity === 'critical') {
            $this->lockAccount($event->identifier);
        }

        // Notify security team
        $this->notifySecurityTeam($event);
    }

    protected function lockAccount(string $identifier): void
    {
        app(\App\Services\Security\LoginMonitor::class)
            ->lock($identifier);
    }

    protected function notifySecurityTeam(SecurityIncidentDetected $event): void
    {
        $admins = \App\Models\User::where('is_admin', true)->get();

        Notification::send($admins, new AdminAlert(
            title: 'Security Incident',
            message: "Security incident detected: {$event->type}",
            level: 'critical'
        ));
    }
}
