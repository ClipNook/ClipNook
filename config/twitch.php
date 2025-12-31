<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twitch API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Twitch API integration including OAuth, rate limiting,
    | privacy settings, and API endpoints. All settings support environment
    | variables for secure deployment.
    |
    */

    // OAuth Configuration
    'client_id'     => env('TWITCH_CLIENT_ID'),
    'client_secret' => env('TWITCH_CLIENT_SECRET'),
    'redirect_uri'  => env('TWITCH_REDIRECT_URI'),
    'scopes'        => explode(' ', env('TWITCH_SCOPES', 'user:read:email')),

    // Token Management
    'token_refresh_buffer' => (int) env('TWITCH_TOKEN_REFRESH_BUFFER', 300), // 5 minutes before expiry

    // API Endpoints
    'api_url'  => env('TWITCH_API_URL', 'https://api.twitch.tv/helix'),
    'auth_url' => env('TWITCH_AUTH_URL', 'https://id.twitch.tv/oauth2'),
    'timeout'  => (int) env('TWITCH_TIMEOUT', 30),

    // Rate Limiting (GDPR Compliance)
    'rate_limit' => [
        'enabled'      => env('TWITCH_RATE_LIMIT_ENABLED', true),
        'max_requests' => (int) env('TWITCH_RATE_LIMIT_MAX', 800), // Per hour
        'retry_after'  => (int) env('TWITCH_RATE_LIMIT_RETRY', 3600), // 1 hour

        // Per-action rate limits (more granular control)
        'actions' => [
            'get_clips'   => ['max' => (int) env('TWITCH_RATE_CLIPS_MAX', 60), 'decay' => 60],
            'get_games'   => ['max' => (int) env('TWITCH_RATE_GAMES_MAX', 60), 'decay' => 60],
            'get_users'   => ['max' => (int) env('TWITCH_RATE_USERS_MAX', 60), 'decay' => 60],
            'get_videos'  => ['max' => (int) env('TWITCH_RATE_VIDEOS_MAX', 60), 'decay' => 60],
            'create_clip' => ['max' => (int) env('TWITCH_RATE_CREATE_CLIP_MAX', 10), 'decay' => 60],
        ],
    ],

    // Data Privacy & GDPR Compliance
    'privacy' => [
        'log_requests'     => env('TWITCH_LOG_REQUESTS', false),
        'anonymize_ip'     => env('TWITCH_ANONYMIZE_IP', true),
        'store_avatars'    => env('TWITCH_STORE_AVATARS', true),
        'avatar_max_bytes' => (int) env('TWITCH_AVATAR_MAX_BYTES', 2097152), // 2MB
    ],

    // Session & Cookies
    'remember' => env('TWITCH_REMEMBER', true),

    // Caching
    'cache_ttl' => (int) env('TWITCH_CACHE_TTL', 3600), // 1 hour

    // Security Settings
    'security' => [
        'allowed_domains' => ['twitch.tv', 'www.twitch.tv', 'static-cdn.jtvnw.net', 'clips.twitch.tv'],
        'max_text_length' => (int) env('TWITCH_MAX_TEXT_LENGTH', 1000),
        'max_view_count'  => (int) env('TWITCH_MAX_VIEW_COUNT', 100000000), // 100M
        'require_https'   => env('TWITCH_REQUIRE_HTTPS', true),
    ],

    // Caching
    'cache_ttl' => (int) env('TWITCH_CACHE_TTL', 3600), // 1 hour

    // Debug Mode
    'debug' => env('TWITCH_DEBUG', false),

    // Clip Submission Validation Rules
    'validation_rules' => [
        'max_clip_age_days' => (int) env('TWITCH_MAX_CLIP_AGE_DAYS', 7),
        'max_view_count'    => (int) env('TWITCH_MAX_VIEW_COUNT', 100000),
        'max_duration'      => (int) env('TWITCH_MAX_DURATION', 60),
    ],
];
