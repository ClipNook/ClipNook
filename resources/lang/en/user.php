<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | User Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used throughout the application for
    | user-related messages, labels, and roles. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // User Roles
    'roles' => [
        'admin'     => 'Admin',
        'moderator' => 'Moderator',
        'streamer'  => 'Streamer',
        'cutter'    => 'Cutter',
        'viewer'    => 'Viewer',
    ],

    // User Status Messages
    'messages' => [
        'anonymous_user'      => 'Anonymous User',
        'profile_updated'     => 'Profile updated successfully.',
        'avatar_uploaded'     => 'Avatar uploaded successfully.',
        'avatar_deleted'      => 'Avatar deleted successfully.',
        'avatar_reset'        => 'Avatar reset to default successfully.',
        'preferences_saved'   => 'Preferences saved successfully.',
        'account_deactivated' => 'Account deactivated successfully.',
        'account_reactivated' => 'Account reactivated successfully.',
    ],

    'never' => 'Never',

    // User Profile Labels
    'labels' => [
        'display_name'        => 'Display Name',
        'twitch_username'     => 'Twitch Username',
        'email'               => 'Email',
        'intro'               => 'Introduction',
        'avatar'              => 'Avatar',
        'current_avatar'      => 'Current Avatar',
        'upload_new_avatar'   => 'Upload New Avatar',
        'avatar_requirements' => 'Avatar Requirements',
        'available_for_jobs'  => 'Available for Jobs',
        'allow_clip_sharing'  => 'Allow Clip Sharing',
        'last_activity'       => 'Last Activity',
        'member_since'        => 'Member Since',
    ],

    // Avatar Status
    'avatar' => [
        'custom'             => 'Custom',
        'twitch'             => 'Twitch',
        'using_custom'       => 'You are using a custom uploaded avatar.',
        'max_size'           => 'Max: 2MB, 250x250px',
        'max_file_size'      => 'Max file size: 2 MB',
        'max_resolution'     => 'Max resolution: 250×250px',
        'formats'            => 'Formats: JPEG, PNG, GIF, WebP',
        'secure_upload'      => 'Secure upload with validation',
        'click_to_select'    => 'Click to select or drag and drop',
        'formats_up_to'      => 'JPEG, PNG, GIF, WebP up to 2MB',
        'upload'             => 'Upload',
        'uploading'          => 'Uploading...',
        'sync_twitch'        => 'Sync Twitch',
        'syncing'            => 'Syncing...',
        'reset'              => 'Reset',
        'delete'             => 'Delete',
        'saving'             => 'Saving...',
    ],

    // User Actions
    'actions' => [
        'edit_profile'     => 'Edit Profile',
        'change_avatar'    => 'Change Avatar',
        'delete_avatar'    => 'Delete Avatar',
        'reset_avatar'     => 'Reset Avatar',
        'save_preferences' => 'Save Preferences',
        'deactivate'       => 'Deactivate Account',
        'reactivate'       => 'Reactivate Account',
        'view_profile'     => 'View Profile',
    ],

    // Validation Messages
    'validation' => [
        'intro_max'         => 'Introduction cannot exceed :max characters.',
        'avatar_required'   => 'Please select an avatar image.',
        'avatar_image'      => 'Avatar must be a valid image file.',
        'avatar_max'        => 'Avatar file size cannot exceed :max KB.',
        'avatar_sync_limit' => 'Too many avatar sync attempts. Please wait 2 hours before trying again.',
    ],

    // User Notifications
    'notifications' => [
        'welcome'           => 'Welcome to :name !',
        'profile_complete'  => 'Complete your profile to get started.',
        'token_expired'     => 'Your Twitch token has expired. Please log in again.',
        'account_suspended' => 'Your account has been suspended.',
    ],
];
