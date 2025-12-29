<?php

return [
    'submit' => [
        'labels' => [
            'input'       => 'Clip URL or Clip ID',
            'placeholder' => 'https://clips.twitch.tv/...',
            'header'      => 'Clip information',
            'untitled'    => 'Untitled',
            'views'       => 'views',
            'creator'     => 'Creator',
            'id'          => 'ID:',
        ],

        'examples' => [
            'intro' => 'Enter a clip URL or ID. Examples:',
            'full'  => 'Full URL',
            'clips' => 'clips.twitch',
            'id'    => 'Clip ID',
        ],

        'actions' => [
            'check'    => 'Check clip',
            'recheck'  => 'Check again',
            'checking' => 'Checking...',
            'reset'    => 'Reset',

            'open_on_twitch' => 'Open on Twitch',
            'copy_id'        => 'Copy ID',
            'copied'         => 'Copied!',
            'watch_vod'      => 'Watch VOD',

            'save'   => 'Save clip',
            'saving' => 'Saving...',
        ],

        'alt' => [
            'thumbnail' => 'Thumbnail for clip: :title',
        ],

        'messages' => [
            'enter_input'                => 'Please enter a clip URL or ID.',
            'invalid_input'              => 'Invalid clip URL or ID.',
            'only_streamers'             => 'Only streamers can submit clips.',
            'token_failed'               => 'Failed to retrieve Twitch token.',
            'not_found'                  => 'Clip not found.',
            'broadcaster_not_registered' => 'Broadcaster is not registered with us.',
            'broadcaster_not_streamer'   => 'Broadcaster is not a streamer.',
            'validated'                  => 'Clip validated and accepted.',
            'no_clip_to_save'            => 'No clip to save.',
            'saved'                      => 'Clip saved successfully.',
        ],
    ],
];
