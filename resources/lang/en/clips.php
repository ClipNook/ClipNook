<?php

/*
|--------------------------------------------------------------------------
| Clip Submission Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used by the clip submission components
| for various messages that are displayed to the user. You are free to
| modify these language lines according to your application's requirements.
|
*/

return [
    // =========================================================================
    // UI LABELS & FORM ELEMENTS
    // =========================================================================

    // Page Headers & Navigation
    'ui_title'            => 'Submit a Twitch Clip',
    'ui_description'      => 'Share your favorite Twitch clips with the community. Just paste the clip ID or the full Twitch URL.',
    'submit_page_title'   => 'Submit a Clip',
    'submit_page_subtitle' => 'Share your favorite Twitch clips with the community',
    'library_page_title'  => 'Clip Library',
    'library_page_subtitle' => 'Discover and explore submitted clips',
    'library_title'       => 'Clip Library',
    'library_subtitle'    => 'Browse all submitted clips',
    'view_page_title'     => 'View Clip',

    // Form Labels & Inputs
    'clip_id_label'       => 'Twitch Clip ID',
    'clip_id_placeholder' => 'e.g., PluckyInventiveCarrotPastaThat or https://twitch.tv/.../clip/...',
    'clip_id_description' => 'Enter the Twitch clip ID or paste the full URL',
    'clip_id_help'        => 'You can paste either the clip ID (e.g., :example) or the full Twitch URL.',

    // Buttons & Actions
    'check_clip_button'   => 'Check Clip',
    'checking_button'     => 'Checking...',
    'submit_button'       => 'Submit Clip',
    'submitting_button'   => 'Submitting...',
    'submit_clip_button'  => 'Submit This Clip',
    'reset_button'        => 'Check Another Clip',
    'cancel'              => 'Cancel',
    'load_player_button'  => 'Load Player',
    'consent_button'      => 'Load Player',

    // =========================================================================
    // HELP & GUIDANCE
    // =========================================================================

    // Help Section
    'help_title'          => 'How to find a Clip ID',
    'help_subtitle'       => 'Need help finding the clip ID?',
    'help_step_1'         => 'Go to a Twitch clip URL (e.g., :example_url)',
    'help_step_2'         => 'The clip ID is the last part of the URL: :example_id',
    'help_step_3'         => 'Paste just the ID (no full URL) in the field above',

    // =========================================================================
    // CLIP INFORMATION & DISPLAY
    // =========================================================================

    // Clip Info Headers
    'clip_info_title'     => 'Clip Information',
    'clip_info_subtitle'  => 'Review the clip details before submitting',
    'clip_preview_title'  => 'Clip Preview',
    'clip_preview_subtitle' => 'Preview the clip before submitting',

    // Clip Metadata Labels
    'broadcaster_label'   => 'Broadcaster',
    'title_label'         => 'Title',
    'created_at_label'    => 'Created At',
    'view_count_label'    => 'Views',
    'duration_label'      => 'Duration',
    'submitted_by_label'  => 'Submitted by',
    'created_by_label'    => 'Created by',
    'added_on_label'      => 'Added on :date',
    'views_count'         => ':count views',
    'duration_seconds'    => ':seconds seconds',

    // =========================================================================
    // TWITCH PLAYER & CONSENT
    // =========================================================================

    // Player States & Messages
    'submit_info'         => 'No external content loaded',
    'preview_optional'    => 'Want to preview the clip first?',
    'loading_player'      => 'Loading player...',
    'player_ready'        => 'Player ready to load',
    'click_to_play'       => 'Click to play clip',
    'external_content'    => 'External content from Twitch',

    // GDPR & Privacy
    'gdpr_warning'        => 'By loading the player, external content from Twitch will be embedded. This may involve data transmission to third parties according to Twitch\'s privacy policy.',
    'gdpr_explanation'    => 'You can consent to load the player to preview the clip.',
    'confirm_load'        => 'I understand and want to load the player.',

    // Twitch Consent Modal
    'twitch_consent_title'          => 'Twitch Clip Player',
    'twitch_consent_description'    => 'This clip is hosted on Twitch. By clicking on the player, you agree to load content from Twitch.tv.',
    'twitch_consent_privacy_title'  => 'Privacy Notice',
    'twitch_consent_privacy_notice' => 'This content is provided by Twitch and may use cookies or tracking technologies. By loading, you agree to Twitch\'s privacy policy.',
    'twitch_consent_load_button'    => 'Load Clip',
    'twitch_consent_cancel_button'  => 'Cancel',

    // =========================================================================
    // VALIDATION & ERROR MESSAGES
    // =========================================================================

    // Rate Limiting
    'rate_limit_exceeded' => 'Too many submissions. Try again in :seconds seconds.',

    // Form Validation
    'validation_clip_id_required' => 'Clip ID is required.',
    'validation_clip_id_string'   => 'Clip ID must be a string.',
    'validation_clip_id_min'      => 'Clip ID must be at least 5 characters.',
    'validation_clip_id_max'      => 'Clip ID cannot exceed 100 characters.',
    'validation_clip_id_format'   => 'Clip ID contains invalid characters. Only letters, numbers, underscores, and hyphens are allowed.',

    // Error Messages
    'clip_not_found'             => 'This clip was not found on Twitch. Please check the ID and try again.',
    'broadcaster_not_registered' => 'The broadcaster of this clip is not registered with our service.',
    'permission_denied'          => 'You do not have permission to submit clips for this broadcaster.',
    'unauthorized'               => 'You must be logged in to submit clips.',
    'please_check_clip_first'    => 'Please check the clip first before submitting.',
    'unexpected_error'           => 'An unexpected error occurred. Please try again later.',

    // =========================================================================
    // SUCCESS MESSAGES
    // =========================================================================

    'submission_success' => 'Clip submitted successfully! It will be processed in the background.',

    // =========================================================================
    // STEP LABELS & WORKFLOW
    // =========================================================================

    'step_check'     => 'Check',
    'step_info'      => 'Info',
    'step_submit'    => 'Submit',
    'clip_preview'   => 'Clip Preview',

    // =========================================================================
    // MESSAGE TITLES
    // =========================================================================

    'success_title'  => 'Successfully Submitted!',
    'error_title'    => 'Error Occurred',

    // =========================================================================
    // SEARCH & FILTERING
    // =========================================================================

    'search_placeholder' => 'Search clips...',
    'clear_search'       => 'Clear search',
    'sort_by'            => 'Sort by',
    'sort_recent'        => 'Most Recent',
    'sort_popular'       => 'Most Popular',
    'sort_views'         => 'Most Viewed',
    'active_filters'     => 'Active filters',

    // =========================================================================
    // EMPTY STATES
    // =========================================================================

    'no_clips_found'  => 'No clips found',
    'no_clips_yet'    => 'No clips have been submitted yet',
    'no_clips_search' => 'No clips match your search for ":search"',

    // =========================================================================
    // FEATURE CARDS
    // =========================================================================

    'feature_secure_title'          => 'Secure',
    'feature_secure_description'    => 'All clips are processed securely with privacy in mind.',
    'feature_fast_title'            => 'Fast',
    'feature_fast_description'      => 'Quick submission process with background processing.',
    'feature_community_title'       => 'Community',
    'feature_community_description' => 'Share clips with fellow Twitch enthusiasts.',
    'secure_private'                => 'Secure & Private',

    // =========================================================================
    // VOTING SYSTEM
    // =========================================================================

    'upvote'       => 'Upvote',
    'downvote'     => 'Downvote',
    'votes'        => ':count votes',
    'vote_success' => 'Vote recorded',
    'vote_removed' => 'Vote removed',

    // =========================================================================
    // COMMENTS SYSTEM
    // =========================================================================

    'comments'                => 'Comments',
    'comments_count'          => 'Comments (:count)',
    'add_comment'             => 'Add a comment...',
    'post_comment'            => 'Post Comment',
    'login_to_comment'        => 'to comment',
    'no_comments'             => 'No comments yet',
    'reply'                   => 'Reply',
    'delete_comment'          => 'Delete',
    'comment_deleted'         => '[Comment deleted]',
    'unknown'                 => 'Unknown',
    'comment_posted'          => 'Comment posted successfully',
    'comment_required'        => 'Comment cannot be empty',
    'comment_deleted_success' => 'Comment deleted',

    // =========================================================================
    // REPORTING SYSTEM
    // =========================================================================

    'report_clip'              => 'Report',
    'report_title'             => 'Report Clip',
    'report_reason'            => 'Reason',
    'report_description'       => 'Description (optional)',
    'submit_report'            => 'Submit Report',
    'report_success'           => 'Report submitted successfully',
    'report_already_submitted' => 'You have already reported this clip',

    'report_reasons' => [
        'inappropriate' => 'Inappropriate Content',
        'spam'          => 'Spam',
        'copyright'     => 'Copyright Violation',
        'misleading'    => 'Misleading Information',
        'other'         => 'Other',
    ],

    // =========================================================================
    // RELATED CONTENT
    // =========================================================================

    'related_clips'    => 'Related Clips',
    'no_related_clips' => 'No related clips',

    // =========================================================================
    // SHARING
    // =========================================================================

    'share' => 'Share',
    'copied' => 'Copied to clipboard!',

    // =========================================================================
    // STATUS & MODERATION
    // =========================================================================

    'status' => [
        'pending'  => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'flagged'  => 'Flagged',
    ],

    'clip_approved' => 'Clip has been approved successfully',
    'clip_rejected' => 'Clip has been rejected',
];
