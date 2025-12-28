<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twitch Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for Twitch integration messages
    | and errors. Modify them according to your application requirements.
    |
    */

    // OAuth
    'oauth' => [
        'login_with_twitch'      => 'Login with Twitch',
        'authorize'              => 'Authorize',
        'authorizing'            => 'Authorizing...',
        'authorization_required' => 'Twitch authorization required',
        'authorization_failed'   => 'Authorization failed',
        'token_expired'          => 'Your Twitch token has expired. Please login again.',
        'token_invalid'          => 'Invalid Twitch token',
        'logout_success'         => 'Successfully logged out from Twitch',
        'login_success'          => 'Successfully signed in with Twitch',
    ],

    // Login / Config helper
    'login_config_missing' => 'Twitch API is not configured. Please set',
    'or'                   => 'or',
    'login_config_doc'     => 'See .env.example or the project README for configuration instructions.',

    // Clips
    'clips' => [
        'title'           => 'Clips',
        'create'          => 'Create Clip',
        'creating'        => 'Creating clip...',
        'created'         => 'Clip created successfully',
        'create_failed'   => 'Failed to create clip',
        'not_found'       => 'Clip not found',
        'loading'         => 'Loading clips...',
        'no_clips'        => 'No clips available',
        'view_count'      => 'Views',
        'duration'        => 'Duration',
        'created_at'      => 'Created at',
        'broadcaster'     => 'Broadcaster',
        'creator'         => 'Creator',
        'watch_on_twitch' => 'Watch on Twitch',
        'share'           => 'Share',
    ],

    // Errors
    'errors' => [
        'api_error'         => 'Twitch API Error',
        'rate_limit'        => 'Too many requests. Please try again in :seconds seconds.',
        'connection_failed' => 'Connection to Twitch failed',
        'invalid_request'   => 'Invalid request',
        'unauthorized'      => 'Unauthorized',
        'forbidden'         => 'Access denied',
        'not_found'         => 'Resource not found',
        'server_error'      => 'Twitch server error',
        'timeout'           => 'Request timeout',
        'unknown'           => 'Unknown error',
    ],

    // Privacy (GDPR)
    'privacy' => [
        'consent_required' => 'Consent for data processing required',
        'data_usage'       => 'Your Twitch data will be processed according to our privacy policy.',
        'revoke_access'    => 'Revoke access',
        'revoke_confirm'   => 'Are you sure you want to revoke Twitch access?',
        'data_retention'   => 'Data will be stored for :days days',

        // Avatar handling / GDPR
        'avatar_title'     => 'Avatar handling',
        'avatar_download'  => 'With your consent we download and store your Twitch profile image (avatar) locally so we can control retention and deletion according to GDPR.',
        'avatar_storage'   => 'Stored avatars are kept securely and deleted after :days days or when you revoke access.',
    ],

    // Login / Privacy helper texts
    'login_title'          => 'Sign in',
    'login_subtitle'       => 'Sign in with Twitch to create and manage clips, personalize your experience, and participate in the community.',
    'login_cta'            => 'Continue with Twitch',
    'login_privacy_intro'  => 'When you sign in with Twitch we will store minimal necessary data and use it only to provide the service. You can revoke access at any time.',
    'privacy_item_tokens'  => 'We store access and refresh tokens for :days days to maintain session and features like clip creation.',
    'privacy_item_ip'      => 'IP anonymization is enabled',
    'privacy_item_logging' => 'Request logging is enabled',
    'privacy_yes'          => 'Yes',
    'privacy_no'           => 'No',
    'login_privacy_more'   => 'Read more in our privacy policy',
    'login_privacy_note'   => 'We do not sell your data. Tokens are stored encrypted and removed automatically after the retention period.',

    // Config / helper
    'login_need_config' => 'Twitch client configuration is missing. Please set TWITCH_CLIENT_ID and TWITCH_CLIENT_SECRET in your environment.',

];
