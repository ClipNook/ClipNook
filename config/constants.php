<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application Constants
    |--------------------------------------------------------------------------
    |
    | This file contains all magic numbers and strings used throughout
    | the application. These should be referenced instead of hardcoded values.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page'    => 15,
        'moderation_per_page' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'recent_clips_minutes'   => 15,
        'featured_clips_minutes' => 30,
        'user_stats_hours'       => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Limits
    |--------------------------------------------------------------------------
    */
    'time' => [
        'comment_edit_minutes' => 15,
        'hsts_max_age'         => 31536000, // 1 year in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits and Thresholds
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'featured_clips'           => 10,
        'popular_clips'            => 10,
        'recent_clips'             => 20,
        'clip_score_threshold'     => 10,
        'clip_view_threshold'      => 100,
        'clip_id_min_length'       => 5,
        'clip_id_max_length'       => 100,
        'reject_reason_min_length' => 10,
        'reject_reason_max_length' => 500,
        'daily_clip_submissions'   => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP and Network
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout_seconds' => 10,
        'retry_count'     => 3,
        'retry_delay_ms'  => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Locking
    |--------------------------------------------------------------------------
    */
    'lock' => [
        'twitch_api_timeout_seconds' => 30,
        'twitch_api_block_seconds'   => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Performance
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'slow_query_threshold_ms' => 1000,
        'metrics_max_entries'     => 1000,
        'query_log_max_length'    => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'submit_clip_max_attempts'  => 5,
        'submit_clip_decay_minutes' => 60,
        'vote_max_attempts'         => 10,
        'vote_decay_minutes'        => 1,
    ],
];
