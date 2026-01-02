<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for tracking user activity and last activity times.
    | This helps with session management and user engagement analytics.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Database Tracking
    |--------------------------------------------------------------------------
    |
    | Settings for tracking user activity in the database.
    |
    */

    'update_interval' => env('ACTIVITY_UPDATE_INTERVAL', 300), // 5 minutes - minimum time between DB updates

    'session_key' => env('ACTIVITY_SESSION_KEY', 'last_activity'), // Session key for storing last activity

    'track_guests' => env('ACTIVITY_TRACK_GUESTS', false), // Track guest users

    'guest_cache_ttl' => env('ACTIVITY_GUEST_CACHE_TTL', 3600), // 1 hour cache for guest activity

    /*
    |--------------------------------------------------------------------------
    | Skip Tracking
    |--------------------------------------------------------------------------
    |
    | Routes and conditions to skip activity tracking for.
    |
    */

    'skip_ajax' => env('ACTIVITY_SKIP_AJAX', true), // Skip AJAX requests

    'skip_routes' => [
        'api/health',
        '_debugbar/*',
        'telescope/*',
        'horizon/*',
        'pulse/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Settings
    |--------------------------------------------------------------------------
    |
    | Settings for session-based activity tracking.
    |
    */

    'session_key' => 'last_activity',

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache configuration for activity tracking.
    |
    */

    'cache_prefix' => 'activity_',

    'cache_ttl' => env('ACTIVITY_CACHE_TTL', 3600), // 1 hour
];
