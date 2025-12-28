<?php

return [
    // Brand settings — quick adjustments for title / tagline / beta
    'brand' => [
        'name'       => env('APP_NAME', 'ClipNook'),
        'tagline'    => 'Privacy‑first clip platform',
        'href'       => env('APP_URL', 'http://localhost'),
        'show_beta'  => true,
        'beta_label' => 'Beta',
    ],

    // Primary navigation — use translation keys for labels
    'nav' => [
        ['label' => 'ui.nav.home', 'route' => 'home'],
        ['label' => 'ui.nav.clips', 'href' => '/#clips'],
        ['label' => 'ui.nav.streamers', 'href' => '/#streamers'],
        ['label' => 'ui.nav.cutters', 'href' => '/#cutters'],
        ['label' => 'ui.nav.faq', 'href' => '/#faq'],
        // ['label' => 'ui.nav.dashboard', 'route' => 'dashboard'],
        // ['label' => 'ui.nav.docs', 'href' => 'https://docs.example.com'],
    ],
];
