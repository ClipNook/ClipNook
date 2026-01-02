<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | GDPR & Privacy Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for GDPR compliance features
    | including data export, deletion, consent management, and privacy policy.
    |
    */

    // Privacy Policy
    'privacy_policy' => [
        'title'       => 'Privacy Policy',
        'description' => 'Your privacy is important to us. Learn how we collect, use, and protect your personal information.',
        'updated_at'  => 'Last updated: :date',
    ],

    // Data Protection Rights
    'rights' => [
        'title'              => 'Your Data Protection Rights',
        'description'        => 'Under GDPR, you have the following rights regarding your personal data:',
        'access'             => 'Right to Access',
        'access_desc'        => 'You can request a copy of your personal data at any time.',
        'rectification'      => 'Right to Rectification',
        'rectification_desc' => 'You can request correction of inaccurate personal data.',
        'erasure'            => 'Right to Erasure',
        'erasure_desc'       => 'You can request deletion of your personal data.',
        'portability'        => 'Right to Data Portability',
        'portability_desc'   => 'You can receive your data in a structured, machine-readable format.',
        'restriction'        => 'Right to Restriction',
        'restriction_desc'   => 'You can request limitation of processing of your personal data.',
        'objection'          => 'Right to Object',
        'objection_desc'     => 'You can object to processing of your personal data.',
    ],

    // Data Collection
    'data_collection' => [
        'title'               => 'What Data We Collect',
        'twitch_profile'      => 'Twitch Profile Information',
        'twitch_profile_desc' => 'We collect your Twitch ID, username, display name, and email address when you log in.',
        'activity'            => 'Activity Data',
        'activity_desc'       => 'We track your submissions, comments, votes, and moderation actions.',
        'technical'           => 'Technical Data',
        'technical_desc'      => 'We collect IP addresses (pseudonymized), browser information, and access logs.',
        'preferences'         => 'User Preferences',
        'preferences_desc'    => 'We store your language, notification, and display preferences.',
    ],

    // Data Usage
    'data_usage' => [
        'title'               => 'How We Use Your Data',
        'authentication'      => 'Authentication & Account Management',
        'authentication_desc' => 'To provide access to your account and verify your identity.',
        'functionality'       => 'Core Functionality',
        'functionality_desc'  => 'To enable clip submission, voting, commenting, and moderation features.',
        'communication'       => 'Communication',
        'communication_desc'  => 'To send notifications about your submissions and account activity.',
        'security'            => 'Security & Fraud Prevention',
        'security_desc'       => 'To protect against abuse, spam, and unauthorized access.',
        'improvement'         => 'Service Improvement',
        'improvement_desc'    => 'To analyze usage patterns and improve the platform.',
    ],

    // Data Sharing
    'data_sharing' => [
        'title'                  => 'Data Sharing',
        'description'            => 'We do not sell your personal data. We share data only in these cases:',
        'twitch'                 => 'Twitch Platform',
        'twitch_desc'            => 'When embedding clips, Twitch may collect data according to their privacy policy.',
        'legal'                  => 'Legal Requirements',
        'legal_desc'             => 'When required by law or to protect our legal rights.',
        'service_providers'      => 'Service Providers',
        'service_providers_desc' => 'With trusted third parties who help us operate the platform (e.g., hosting providers).',
    ],

    // Data Retention
    'data_retention' => [
        'title'                => 'Data Retention',
        'description'          => 'We retain your data as follows:',
        'active_accounts'      => 'Active Accounts',
        'active_accounts_desc' => 'Your data is retained while your account is active.',
        'inactive'             => 'Inactive Accounts',
        'inactive_desc'        => 'Accounts inactive for :days days may be automatically deleted.',
        'logs'                 => 'Activity Logs',
        'logs_desc'            => 'Activity logs are retained for :days days.',
        'deleted'              => 'Deleted Accounts',
        'deleted_desc'         => 'After deletion, personal data is removed within :days days.',
    ],

    // Cookies
    'cookies' => [
        'title'            => 'Cookies',
        'description'      => 'We use cookies for:',
        'essential'        => 'Essential Cookies',
        'essential_desc'   => 'Required for authentication and basic functionality.',
        'functional'       => 'Functional Cookies',
        'functional_desc'  => 'To remember your preferences and settings.',
        'analytics'        => 'Analytics Cookies',
        'analytics_desc'   => 'To understand how you use the platform (only with consent).',
        'third_party'      => 'Third-Party Cookies',
        'third_party_desc' => 'Twitch embeds may set cookies when you play clips.',
    ],

    // Data Export
    'export' => [
        'title'         => 'Export Your Data',
        'description'   => 'Download a complete copy of your personal data in JSON format.',
        'button'        => 'Export My Data',
        'processing'    => 'Preparing your data export...',
        'success'       => 'Your data has been exported successfully.',
        'error'         => 'Failed to export data. Please try again later.',
        'includes'      => 'Your export includes:',
        'profile'       => 'Profile information',
        'activity'      => 'Activity logs',
        'submissions'   => 'Clip submissions',
        'comments'      => 'Comments and votes',
        'preferences'   => 'Preferences and settings',
        'filename'      => 'data-export-:date.json',
    ],

    // Data Deletion
    'deletion' => [
        'title'              => 'Delete Your Account',
        'description'        => 'Permanently delete your account and all associated data.',
        'warning'            => 'Warning: This action cannot be undone!',
        'confirmation'       => 'Are you sure you want to delete your account?',
        'consequences_title' => 'What happens when you delete your account:',
        'consequence_1'      => 'Your profile and personal information will be permanently deleted',
        'consequence_2'      => 'All your submissions, comments, and votes will be anonymized',
        'consequence_3'      => 'You will be immediately logged out and cannot log in again',
        'consequence_4'      => 'This action is irreversible and cannot be undone',
        'type_to_confirm'    => 'Type :phrase to confirm:',
        'confirm_phrase'     => 'DELETE MY ACCOUNT',
        'button'             => 'Delete My Account',
        'processing'         => 'Deleting your account...',
        'success'            => 'Your account has been scheduled for deletion. You will be logged out shortly.',
        'error'              => 'Failed to delete account. Please contact support.',
        'cancelled'          => 'Account deletion cancelled.',
    ],

    // Consent Management
    'consent' => [
        'title'                 => 'Privacy Preferences',
        'description'           => 'Manage your data processing consents',
        'required'              => 'Required',
        'optional'              => 'Optional',
        'essential_title'       => 'Essential Functionality',
        'essential_desc'        => 'Required for the platform to function properly. Cannot be disabled.',
        'functional_title'      => 'Functional',
        'functional_desc'       => 'Enable additional features like theme preferences and saved filters.',
        'analytics_title'       => 'Analytics',
        'analytics_desc'        => 'Help us improve by collecting anonymous usage statistics.',
        'marketing_title'       => 'Marketing',
        'marketing_desc'        => 'Receive news about new features and updates.',
        'twitch_embed_title'    => 'Twitch Embeds',
        'twitch_embed_desc'     => 'Allow embedding Twitch clips. Required to preview and watch clips.',
        'save'                  => 'Save Preferences',
        'saved'                 => 'Your privacy preferences have been saved.',
        'version'               => 'Version :version',
        'last_updated'          => 'Last updated: :date',
    ],

    // Contact
    'contact' => [
        'title'       => 'Contact Us',
        'description' => 'For privacy-related questions or to exercise your rights, contact us:',
        'email'       => 'Email',
        'response'    => 'We will respond within :days business days.',
    ],

    // Legal
    'legal' => [
        'controller'      => 'Data Controller',
        'dpo'             => 'Data Protection Officer',
        'supervisory'     => 'Supervisory Authority',
        'jurisdiction'    => 'Jurisdiction',
        'applicable_law'  => 'This privacy policy is governed by the laws of the European Union and :country.',
    ],
];
