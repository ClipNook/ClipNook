<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\ClipStatusChanged;
use App\Events\PerformanceThresholdExceeded;
use App\Events\SecurityIncidentDetected;
use App\Listeners\HandleSecurityIncident;
use App\Listeners\NotifyPerformanceIssue;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ClipStatusChanged::class => [
            \App\Listeners\LogClipActivity::class,
            \App\Listeners\SendClipNotifications::class,
        ],

        PerformanceThresholdExceeded::class => [
            NotifyPerformanceIssue::class,
        ],

        SecurityIncidentDetected::class => [
            HandleSecurityIncident::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
