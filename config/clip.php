<?php

return [
    // Maximum number of clips a user can submit per day
    'max_per_user_per_day' => 10,

    // Rate limiting configuration
    'rate_limiting' => [
        'submit_clip' => [
            'max_attempts'  => 5,
            'decay_minutes' => 1,
        ],
    ],
];
