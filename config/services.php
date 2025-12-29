<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'twitch' => [
        'client_id'     => env('TWITCH_CLIENT_ID'),
        'client_secret' => env('TWITCH_CLIENT_SECRET'),
        'redirect_uri'  => env('TWITCH_REDIRECT_URI'),
        'scopes'        => env('TWITCH_SCOPES', 'user:read:email'),

        // API Configuration
        'api_url'       => 'https://api.twitch.tv/helix',
        'auth_url'      => 'https://id.twitch.tv/oauth2',
        'timeout'       => (int) env('TWITCH_TIMEOUT', 30),

        // Rate Limiting (GDPR Compliance)
        'rate_limit'    => [
            'enabled'       => env('TWITCH_RATE_LIMIT_ENABLED', true),
            'max_requests'  => (int) env('TWITCH_RATE_LIMIT_MAX', 800), // Per minute
            'retry_after'   => (int) env('TWITCH_RATE_LIMIT_RETRY', 60),

            // Per-action rate limits (overrides defaults in code)
            'actions' => [
                'get_clips'        => ['max' => 60, 'decay' => 60],
                'get_clips_by_ids' => ['max' => 120, 'decay' => 60],
                'create_clip'      => ['max' => 10, 'decay' => 60],
            ],
        ],

        // Data Privacy (GDPR)
        'privacy' => [
            'log_requests'      => env('TWITCH_LOG_REQUESTS', false),
            'anonymize_ip'      => env('TWITCH_ANONYMIZE_IP', true),
            'data_retention'    => (int) env('TWITCH_DATA_RETENTION_DAYS', 30),
            // Store avatars locally to comply with privacy requirements
            'store_avatars'     => env('TWITCH_STORE_AVATARS', true),
            // Maximum allowed avatar size in bytes (default 2MB)
            'avatar_max_bytes'  => (int) env('TWITCH_AVATAR_MAX_BYTES', 2097152),
        ],
        // Remember option controls whether we set a persistent login cookie
        'remember' => env('TWITCH_REMEMBER', true),
    ],

];
