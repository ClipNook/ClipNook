<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring Configuration
    |--------------------------------------------------------------------------
    */

    'slow_query_threshold'    => env('PERFORMANCE_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
    'metrics_retention_hours' => env('PERFORMANCE_METRICS_RETENTION_HOURS', 24),
    'metrics_max_entries'     => env('PERFORMANCE_METRICS_MAX_ENTRIES', 1000),
    'storage_path'            => env('PERFORMANCE_STORAGE_PATH', 'performance'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        'window_size'  => env('RATE_LIMIT_WINDOW_SIZE', 60), // seconds
        'max_requests' => env('RATE_LIMIT_MAX_REQUESTS', 60),
        'burst_limit'  => env('RATE_LIMIT_BURST_LIMIT', 10),
        'storage_path' => env('RATE_LIMIT_STORAGE_PATH', 'rate_limiting'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Login Monitoring Configuration
    |--------------------------------------------------------------------------
    */

    'login_monitoring' => [
        'lockout_time'   => env('LOGIN_LOCKOUT_TIME', 3600), // seconds
        'max_attempts'   => env('LOGIN_MAX_ATTEMPTS', 5),
        'attempt_window' => env('LOGIN_ATTEMPT_WINDOW', 3600), // seconds
        'storage_path'   => env('LOGIN_MONITORING_STORAGE_PATH', 'login_monitoring'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Caching Configuration
    |--------------------------------------------------------------------------
    */

    'response_cache' => [
        'default_ttl'      => env('RESPONSE_CACHE_DEFAULT_TTL', 300), // seconds
        'max_cache_size'   => env('RESPONSE_CACHE_MAX_SIZE', 100), // MB
        'exclude_patterns' => explode(',', env('RESPONSE_CACHE_EXCLUDE_PATTERNS', 'admin,api/auth')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers Configuration
    |--------------------------------------------------------------------------
    */

    'security_headers' => [
        'hsts' => [
            'max_age'            => env('SECURITY_HSTS_MAX_AGE', 31536000),
            'include_subdomains' => env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
            'preload'            => env('SECURITY_HSTS_PRELOAD', false),
        ],
        'csp' => [
            'report_uri'         => env('SECURITY_CSP_REPORT_URI'),
            'report_only'        => env('SECURITY_CSP_REPORT_ONLY', false),
            'additional_sources' => [
                'script'      => explode(',', env('SECURITY_CSP_ADDITIONAL_SCRIPT_SRC', '')),
                'style'       => explode(',', env('SECURITY_CSP_ADDITIONAL_STYLE_SRC', '')),
                'font'        => explode(',', env('SECURITY_CSP_ADDITIONAL_FONT_SRC', '')),
                'img'         => explode(',', env('SECURITY_CSP_ADDITIONAL_IMG_SRC', '')),
                'connect'     => explode(',', env('SECURITY_CSP_ADDITIONAL_CONNECT_SRC', '')),
                'form_action' => explode(',', env('SECURITY_CSP_ADDITIONAL_FORM_ACTION_SRC', '')),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SQL Injection Audit Configuration
    |--------------------------------------------------------------------------
    */

    'sql_audit' => [
        'enabled'         => env('SQL_AUDIT_ENABLED', true),
        'exclude_paths'   => explode(',', env('SQL_AUDIT_EXCLUDE_PATHS', 'vendor,migrations')),
        'custom_patterns' => explode(',', env('SQL_AUDIT_CUSTOM_PATTERNS', '')),
    ],
];
