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
        'create'          => 'Submit Clip',
        'creating'        => 'Submitting clip...',
        'created'         => 'Clip submitted successfully',
        'create_failed'   => 'Failed to submit clip',
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
        'avatar_title'      => 'Avatar handling',
        'avatar_download'   => 'With your consent we download and store your Twitch profile image (avatar) locally so we can control retention and deletion according to GDPR.',
        'avatar_storage'    => 'Stored avatars are kept securely and remain stored until you delete them or delete your account.',
        'consent_error'     => 'Please confirm that you consent to the processing of your data to continue.',
        'short_intro'       => 'Privacy-first: Tokens are encrypted and retained for :days days',
    ],

    // Login / Privacy helper texts
    'login_title'          => 'Sign in',
    'login_subtitle'       => 'Sign in with Twitch to submit and manage clips, personalize your experience, and participate in the community.',
    'login_cta'            => 'Continue with Twitch',
    'login_privacy_intro'  => 'When you sign in with Twitch we will store minimal necessary data and use it only to provide the service. You can revoke access at any time.',
    'privacy_item_tokens'  => 'We store access and refresh tokens encrypted to maintain your session and support features like clip submission; tokens are kept until you log out or delete your account.',
    'privacy_item_ip'      => 'IP anonymization is enabled',
    'privacy_item_logging' => 'Request logging is enabled',
    'privacy_yes'          => 'Yes',
    'privacy_no'           => 'No',
    'login_privacy_more'   => 'Read more in our privacy policy',
    'login_privacy_note'   => 'We do not sell your data. Avatars are stored until you delete them or delete your account. Access and refresh tokens are stored encrypted until you log out or your account is deleted; tokens are replaced on each new login.',

    'login_cta_sub' => 'You will be redirected to Twitch to authorize; we only store access data encrypted and securely.',

    // Config / helper
    'login_need_config' => 'Twitch client configuration is missing. Please set TWITCH_CLIENT_ID and TWITCH_CLIENT_SECRET in your environment.',

];
