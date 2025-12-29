<?php

return [
    'submit' => [
        'labels' => [
            'input'       => 'Clip URL oder Clip ID',
            'placeholder' => 'https://clips.twitch.tv/...',
            'header'      => 'Clip Informationen',
            'untitled'    => 'Ohne Titel',
            'views'       => 'Aufrufe',
            'creator'     => 'Ersteller',
            'id'          => 'ID:',
        ],

        'examples' => [
            'intro' => 'Gib eine Clip-URL oder ID ein. Beispiele:',
            'full'  => 'Volle URL',
            'clips' => 'clips.twitch',
            'id'    => 'Clip-ID',
        ],

        'actions' => [
            'check'    => 'Clip prüfen',
            'recheck'  => 'Erneut prüfen',
            'checking' => 'Prüfe...',
            'reset'    => 'Zurücksetzen',

            'open_on_twitch' => 'Auf Twitch öffnen',
            'copy_id'        => 'ID kopieren',
            'copied'         => 'Kopiert!',
            'watch_vod'      => 'VOD ansehen',

            'save'   => 'Clip speichern',
            'saving' => 'Speichere...',
        ],

        'alt' => [
            'thumbnail' => 'Thumbnail für Clip: :title',
        ],

        'messages' => [
            'enter_input'                => 'Bitte eine Clip-URL oder ID eingeben.',
            'invalid_input'              => 'Ungültige Clip URL oder ID.',
            'token_failed'               => 'Fehler beim Abrufen des Twitch-Tokens.',
            'not_found'                  => 'Clip nicht gefunden.',
            'broadcaster_not_registered' => 'Broadcaster ist nicht bei uns registriert.',
            'broadcaster_not_streamer'   => 'Der Broadcaster ist kein Streamer.',
            'broadcaster_no_sharing'     => 'Der Broadcaster erlaubt kein Clip-Sharing.',
            'only_users'                 => 'Nur registrierte Nutzer können Clips einreichen.',
            'validated'                  => 'Clip validiert und akzeptiert.',
            'no_clip_to_save'            => 'Kein Clip zum Speichern.',
            'saved'                      => 'Clip erfolgreich gespeichert.',
            'save_failed'                => 'Clip konnte nicht gespeichert werden. Bitte versuche es später erneut.',
            'limit_reached'              => 'Du kannst heute maximal 10 Clips einreichen.',
        ],
    ],
];
