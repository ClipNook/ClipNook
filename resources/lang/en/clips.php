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
    // UI Labels
    'ui_title'            => 'Submit a Twitch Clip',
    'ui_description'      => 'Share your favorite Twitch clips with the community. Just paste the clip ID or the full Twitch URL.',
    'clip_id_label'       => 'Twitch Clip ID',
    'clip_id_placeholder' => 'e.g., PluckyInventiveCarrotPastaThat or https://twitch.tv/.../clip/...',
    'clip_id_help'        => 'You can paste either the clip ID (e.g., :example) or the full Twitch URL.',
    'check_clip_button'   => 'Check Clip',
    'checking_button'     => 'Checking...',
    'submit_button'       => 'Submit Clip',
    'submitting_button'   => 'Submitting...',
    'secure_private'      => 'Secure & Private',
    'help_title'          => 'How to find a Clip ID',
    'help_step_1'         => 'Go to a Twitch clip URL (e.g., :example_url)',
    'help_step_2'         => 'The clip ID is the last part of the URL: :example_id',
    'help_step_3'         => 'Paste just the ID (no full URL) in the field above',

    // Clip Info Display
    'clip_info_title'     => 'Clip Information',
    'broadcaster_label'   => 'Broadcaster',
    'title_label'         => 'Title',
    'created_at_label'    => 'Created At',
    'view_count_label'    => 'Views',
    'duration_label'      => 'Duration',
    'load_player_button'  => 'Load Player',
    'gdpr_warning'        => 'By loading the player, external content from Twitch will be embedded. This may involve data transmission to third parties according to Twitch\'s privacy policy.',
    'confirm_load'        => 'I understand and want to load the player.',
    'reset_button'        => 'Check Another Clip',
    'submit_clip_button'  => 'Submit This Clip',

    // Rate Limiting Messages
    'rate_limit_exceeded' => 'Too many submissions. Try again in :seconds seconds.',

    // Success Messages
    'submission_success' => 'Clip submitted successfully! It will be processed in the background.',

    // Error Messages
    'clip_not_found'             => 'This clip was not found on Twitch. Please check the ID and try again.',
    'broadcaster_not_registered' => 'The broadcaster of this clip is not registered with our service.',
    'permission_denied'          => 'You do not have permission to submit clips for this broadcaster.',
    'unexpected_error'           => 'An unexpected error occurred. Please try again later.',

    // Step Labels
    'step_check'     => 'Check',
    'step_info'      => 'Info',
    'step_submit'    => 'Submit',
    'clip_preview'   => 'Clip Preview',

    // Message Titles
    'success_title'  => 'Successfully Submitted!',
    'error_title'    => 'Error Occurred',
];
