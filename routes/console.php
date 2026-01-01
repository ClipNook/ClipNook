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

// Schedule cleanup of expired API tokens
Schedule::command('tokens:cleanup')
    ->daily()
    ->at('03:00')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule IP salt rotation for GDPR compliance
Schedule::command('gdpr:rotate-ip-salts --cleanup')
    ->weekly()
    ->sundays()
    ->at('04:00')
    ->withoutOverlapping()
    ->runInBackground();
