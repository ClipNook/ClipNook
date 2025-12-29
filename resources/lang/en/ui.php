<?php

return [
    'nav' => [
        'home'      => 'Home',
        'clips'     => 'Clips',
        'streamers' => 'Streamers',
        'cutters'   => 'Cutters',
        'faq'       => 'FAQ',
    ],

    // Generic UI strings
    'skip'            => 'Skip to content',
    'submit'          => 'Submit',
    'change_language' => 'Change language',

    'close_menu' => 'Close menu',
    'open_menu'  => 'Open menu',

    'user_menu' => 'User menu',
    'profile'   => 'Profile',
    'settings'  => 'Settings',

    'color'        => 'Color',
    'color_choose' => 'Choose accent color',

    'auth' => [
        'sign_in'             => 'Sign in',
        'sign_in_with_twitch' => 'Sign in with Twitch',
        'remember_me'         => 'Remember me',
        'sign_out'            => 'Sign out',
    ],

    'footer' => [
        'rights'  => 'All rights reserved.',
        'terms'   => 'Terms of Service',
        'privacy' => 'Privacy Policy',
        'imprint' => 'Imprint',
    ],

    // Misc
    'language_changed' => 'Language changed: :lang',

    'common' => [
        'errors_found' => 'Errors found',
        'note'         => 'Note',
    ],

    // Settings
    'settings_saved'             => 'Settings saved.',
    'save'                       => 'Save',
    'settings_twitch'            => 'Twitch',
    'settings_twitch_connected'  => 'Connected to Twitch as :name',
    'settings_disconnect_twitch' => 'Disconnect Twitch',
    'settings_connect_twitch'    => 'Connect Twitch',

    'settings_avatar'             => 'Avatar',
    'settings_avatar_description' => 'Your avatar is taken from Twitch if available. Manage or restore it here.',
    'settings_avatar_disabled'    => 'Avatars are disabled',

    // Current avatar and source labels
    'current_avatar'         => 'Current avatar',
    'avatar_from_twitch'     => 'From Twitch',
    'avatar_custom'          => 'Custom avatar',
    'avatar_remove_failed'   => 'Failed to remove avatar.',
    'avatar_remove_success'  => 'Avatar removed.',

    'remove_avatar'         => 'Remove avatar',
    'remove_avatar_title'   => 'Remove avatar',
    'remove_avatar_confirm' => 'Are you sure you want to remove your avatar? This will permanently delete the locally stored or linked avatar.',

    'restore_avatar'             => 'Restore avatar from Twitch',
    'settings_restore_success'   => 'Avatar restored from Twitch.',
    'settings_restore_failed'    => 'Failed to restore avatar from Twitch.',
    'settings_restore_no_avatar' => 'No avatar found on Twitch to restore.',
    'settings_not_connected'     => 'Not connected to Twitch.',

    'delete_account'                 => 'Delete account',
    'delete_account_explain'         => 'Deleting your account will remove your personal data from our systems where possible. Some records required for system integrity may be anonymized instead of deleted. This action is irreversible.',
    'delete_account_warning'         => 'This action will permanently remove your account and personal data.',
    'delete_account_consequence_1'   => 'Your personal data will be removed where possible.',
    'delete_account_consequence_2'   => 'Some records may be anonymized instead of deleted.',
    'delete_account_consequence_3'   => 'This action is irreversible.',
    'delete_confirm_label'           => 'Type your display name (:name) to confirm deletion',
    'type_to_confirm'                => 'Type the following to confirm:',
    'delete_confirm_placeholder'     => 'Enter your display name',
    'delete_confirm_js'              => 'Are you sure? This will permanently delete your account and personal data.',
    'delete_account_button'          => 'Delete account',
    'delete_my_account'              => 'Delete my account',
    'delete_confirmation_warning'    => 'Please confirm by typing your display name to continue.',
    'delete_confirm_mismatch'        => 'The entered name does not match your display name.',
    'delete_success'                 => 'Your account has been deleted.',
    'delete_failed'                  => 'Failed to delete your account. Please contact support.',
    'delete_anonymized'              => 'Could not delete your account completely; your personal data has been anonymized instead.',
    'account_management'             => 'Account management',
    'account_management_description' => 'Dangerous account actions, please proceed with caution.',
    'delete_account_title'           => 'Delete account',
    'delete_account_confirm'         => 'Are you sure you want to delete your account?',
    'delete_permanently'             => 'Delete permanently',
    'confirm'                        => 'Confirm',

    'settings_description' => 'Manage your account settings and preferences.',

    // Profile / Settings helpers
    'your_profile'             => 'Your profile',
    'profile_info_description' => 'Manage your public profile details, email and avatar preferences.',
    'actions'                  => 'Actions',
    'enable_avatar'            => 'Enable avatars',

    'account_roles'     => 'Account roles',
    'roles_description' => 'Select the roles that describe you on the platform.',

    'connected_to_twitch' => 'Connected to Twitch',

    'danger_zone_description' => 'Dangerous account actions, please proceed with caution.',
    'close'                   => 'Close',

    // Roles
    'roles'                          => 'Roles',
    'label_viewer'                   => 'Viewer',
    'viewer'                         => 'Viewer',
    'viewer_description'             => 'Always active.',
    'label_streamer'                 => 'Streamer',
    'streamer'                       => 'Streamer',
    'streamer_description'           => 'Enable streamer features, including an "Introduce yourself" box.',
    'label_cutter'                   => 'Cutter',
    'cutter'                         => 'Cutter',
    'cutter_description'             => 'Enable additional cutter features and be discoverable as a cutter.',
    'intro_label'                    => 'Introduce yourself',
    'introduce_yourself'             => 'Introduce yourself',
    'intro_placeholder'              => 'Write a short introduction for your viewers (optional)',
    'intro_help'                     => 'Write a short introduction for your viewers; it will appear on your profile.',
    'available_for_jobs_label'       => 'Available for jobs',
    'available_for_jobs'             => 'Available for jobs',
    'available_for_jobs_description' => 'Mark yourself as available for freelance editing jobs and show it on your profile.',
    'role_hint_streamer'             => 'Enables a short "Introduce yourself" box where you can write a short bio shown on your profile.',
    'role_hint_cutter'               => 'Shows an option to mark yourself as available for jobs and display it publicly.',
    'label_viewer_note'              => 'Always active',
    'unsaved_changes'                => 'You have unsaved changes.',
    'save_changes'                   => 'Save changes',
    'settings_save_failed'           => 'Failed to save settings.',

    'avatar_settings'             => 'Avatar settings',
    'avatar_settings_description' => 'Your avatar is taken from Twitch if available. Manage or restore it here.',
    'settings_avatar_description' => 'Your avatar is taken from Twitch if available. Manage or restore it here.',
    'your_avatar'                 => 'Your avatar',
    'avatar_source'               => 'Source',
    'avatar_actions'              => 'Actions',
    'avatar_disabled'             => 'Avatars are disabled',
    'avatar_active'               => 'Avatars enabled',
    'avatar_source_disabled'      => 'Disabled',
    'avatar_source_twitch'        => 'Twitch',
    'avatar_source_custom'        => 'Custom',
    'remove_avatar_title'         => 'Remove avatar',
    'remove_avatar_confirm'       => 'Are you sure you want to remove your avatar? This will permanently delete the locally stored or linked avatar.',
    'remove'                      => 'Remove',
    'restore_avatar_title'        => 'Restore avatar',
    'restore_avatar_confirm'      => 'Restore avatar from Twitch?',
    'restore'                     => 'Restore',
    'enable_avatar_title'         => 'Enable avatars',
    'enable_avatar_confirm'       => 'Enable avatars? This will re-enable avatars taken from Twitch if available.',
    'enable'                      => 'Enable',
    'cancel'                      => 'Cancel',

    'restore_avatar_title'   => 'Restore avatar',
    'restore_avatar_confirm' => 'Restore avatar from Twitch?',
    'restore'                => 'Restore',

    'danger_zone' => 'Danger zone',

    'delete_account_title'       => 'Delete account',
    'delete_account_warning'     => 'This action will permanently remove your account and personal data.',
    'delete_account_description' => 'Deleting your account will remove your personal data from our systems where possible. Some records required for system integrity may be anonymized instead of deleted. This action is irreversible.',
    'delete_permanently'         => 'Delete permanently',

    'no_changes' => 'No changes were made.',
];
