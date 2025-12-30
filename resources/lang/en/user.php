<?php

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
        'preferences_saved'   => 'Preferences saved successfully.',
        'account_deactivated' => 'Account deactivated successfully.',
        'account_reactivated' => 'Account reactivated successfully.',
    ],

    // User Profile Labels
    'labels' => [
        'display_name'       => 'Display Name',
        'twitch_username'    => 'Twitch Username',
        'email'              => 'Email',
        'intro'              => 'Introduction',
        'avatar'             => 'Avatar',
        'available_for_jobs' => 'Available for Jobs',
        'allow_clip_sharing' => 'Allow Clip Sharing',
        'last_activity'      => 'Last Activity',
        'member_since'       => 'Member Since',
    ],

    // User Actions
    'actions' => [
        'edit_profile'     => 'Edit Profile',
        'change_avatar'    => 'Change Avatar',
        'delete_avatar'    => 'Delete Avatar',
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
    ],

    // User Notifications
    'notifications' => [
        'welcome'           => 'Welcome to :name !',
        'profile_complete'  => 'Complete your profile to get started.',
        'token_expired'     => 'Your Twitch token has expired. Please log in again.',
        'account_suspended' => 'Your account has been suspended.',
    ],
];
