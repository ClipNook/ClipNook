<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twitch Service Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the Twitch service for various
    | messages that we need to display to the user or log. You are free to
    | modify these language lines according to your application's requirements.
    |
    */

    // UI Labels
    'ui' => [
        'home' => 'Home',
    ],

    // OAuth Messages
    'oauth_failed_invalid_state'  => 'OAuth failed: Invalid state or code.',
    'oauth_failed_exchange_token' => 'OAuth failed: Unable to exchange code for token.',
    'oauth_success_login'         => 'Successfully logged in with Twitch!',
    'oauth_redirect_dashboard'    => 'Redirecting to dashboard...',

    // API Error Messages
    'api_rate_limit_exceeded'  => 'Rate limit exceeded for Twitch API.',
    'api_no_refresh_token'     => 'No refresh token available.',
    'api_refresh_token_failed' => 'Failed to refresh access token: :error',
    'api_invalid_config'       => 'Twitch client ID and secret must be configured.',
    'api_request_failed'       => 'Failed to fetch :type from Twitch API: :error',

    // Validation Messages
    'validation_code_required'   => 'Authorization code is required.',
    'validation_code_string'     => 'Authorization code must be a string.',
    'validation_code_min'        => 'Authorization code is too short.',
    'validation_code_max'        => 'Authorization code is too long.',
    'validation_state_required'  => 'State parameter is required.',
    'validation_state_string'    => 'State must be a string.',
    'validation_state_size'      => 'State parameter has invalid length.',
    'validation_state_csrf'      => 'Invalid CSRF token.',
    'validation_clip_id_invalid' => 'The :attribute is not a valid Twitch Clip ID.',
    'validation_clip_id_string'  => 'The :attribute must be a string.',

    // Sanitizer Messages
    'sanitizer_invalid_url'        => 'Invalid URL provided.',
    'sanitizer_https_required'     => 'Only HTTPS URLs are allowed.',
    'sanitizer_domain_not_allowed' => 'URL domain not allowed.',
    'sanitizer_invalid_int'        => 'Invalid integer value provided.',

    // Download Messages
    'download_thumbnail_success' => 'Downloaded thumbnail from :url to :path',
    'download_profile_success'   => 'Downloaded profile image from :url to :path',
    'download_failed'            => 'Failed to download :type: :error',

    // Token Messages
    'token_expired'            => 'Access token has expired.',
    'token_refresh_success'    => 'Access token refreshed successfully.',
    'token_refresh_failed'     => 'Failed to refresh access token.',
    'authentication_required'  => 'User authentication required.',
    'no_access_token'          => 'No Twitch access token available. Please re-authenticate with Twitch.',
    'token_expired_no_refresh' => 'Access token has expired and no refresh token is available. Please re-authenticate with Twitch.',

    // General Messages
    'service_unavailable' => 'Twitch service is currently unavailable.',
    'invalid_clip_id'     => 'Invalid clip ID format.',
    'clip_not_found'      => 'Clip not found.',
    'game_not_found'      => 'Game not found.',
    'user_not_found'      => 'User not found.',
    'video_not_found'     => 'Video not found.',

    // Validation Messages
    'clip_too_old'     => 'Clip is older than :days days and cannot be submitted.',
    'too_many_views'   => 'Clip has too many views to be submitted.',
    'clip_too_long'    => 'Clip is too long (max :seconds seconds).',

];
