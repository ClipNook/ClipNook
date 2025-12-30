<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule GDPR data retention enforcement
Schedule::command('gdpr:enforce-retention')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->runInBackground();
