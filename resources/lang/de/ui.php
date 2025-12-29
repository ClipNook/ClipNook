<?php

return [
    'nav' => [
        'home'      => 'Startseite',
        'clips'     => 'Clips',
        'streamers' => 'Streamer',
        'cutters'   => 'Cutter',
        'faq'       => 'FAQ',
    ],

    // Generic UI strings
    'skip'            => 'Zum Inhalt springen',
    'submit'          => 'Absenden',
    'change_language' => 'Sprache ändern',

    'close_menu' => 'Menü schließen',
    'open_menu'  => 'Menü öffnen',

    'user_menu' => 'Benutzermenü',
    'profile'   => 'Profil',
    'settings'  => 'Einstellungen',

    'color'        => 'Farbe',
    'color_choose' => 'Akzentfarbe wählen',

    'auth' => [
        'sign_in'             => 'Anmelden',
        'sign_in_with_twitch' => 'Mit Twitch anmelden',
        'remember_me'         => 'Angemeldet bleiben',
        'sign_out'            => 'Abmelden',
    ],

    'footer' => [
        'rights'  => 'Alle Rechte vorbehalten.',
        'terms'   => 'Nutzungsbedingungen',
        'privacy' => 'Datenschutz',
        'imprint' => 'Impressum',
    ],

    // Misc
    'language_changed' => 'Sprache geändert: :lang',

    'common' => [
        'errors_found' => 'Fehler gefunden',
        'note'         => 'Hinweis',
    ],
    // Settings
    'settings_saved'             => 'Einstellungen gespeichert.',
    'save'                       => 'Speichern',
    'settings_twitch'            => 'Twitch',
    'settings_twitch_connected'  => 'Mit Twitch verbunden als :name',
    'settings_disconnect_twitch' => 'Twitch trennen',
    'settings_connect_twitch'    => 'Mit Twitch verbinden',

    'settings_avatar'             => 'Avatar',
    'settings_avatar_description' => 'Dein Avatar wird, falls vorhanden, von Twitch bezogen. Hier kannst du ihn verwalten oder wiederherstellen.',
    'settings_avatar_disabled'    => 'Avatare sind deaktiviert',

    // Aktuelles Avatar und Quelle
    'current_avatar'         => 'Aktuelles Avatar',
    'avatar_from_twitch'     => 'Von Twitch',
    'avatar_custom'          => 'Eigenes Avatar',
    'avatar_remove_failed'   => 'Konnte Avatar nicht entfernen.',
    'avatar_remove_success'  => 'Avatar entfernt.',

    'remove_avatar'         => 'Avatar entfernen',
    'remove_avatar_title'   => 'Avatar entfernen',
    'remove_avatar_confirm' => 'Möchtest du das Avatar entfernen? Dadurch wird das lokal gespeicherte/verlinkte Avatar dauerhaft entfernt.',

    'restore_avatar'             => 'Avatar von Twitch wiederherstellen',
    'settings_restore_success'   => 'Avatar von Twitch wiederhergestellt.',
    'settings_restore_failed'    => 'Konnte Avatar nicht von Twitch wiederherstellen.',
    'settings_restore_no_avatar' => 'Auf Twitch wurde kein Avatar gefunden, den man wiederherstellen könnte.',
    'settings_not_connected'     => 'Nicht mit Twitch verbunden.',

    'delete_account'                 => 'Account löschen',
    'delete_account_explain'         => 'Das Löschen deines Accounts entfernt persönliche Daten aus unserem System, soweit möglich. Manche für die Systemintegrität benötigten Datensätze werden eventuell anonymisiert statt gelöscht. Diese Aktion ist unwiderruflich.',
    'delete_account_warning'         => 'Diese Aktion wird dein Konto und persönliche Daten dauerhaft entfernen.',
    'delete_account_consequence_1'   => 'Persönliche Daten werden, soweit möglich, entfernt.',
    'delete_account_consequence_2'   => 'Einige Datensätze können anonymisiert werden statt gelöscht zu werden.',
    'delete_account_consequence_3'   => 'Diese Aktion ist unwiderruflich.',
    'delete_confirm_label'           => 'Gib deinen Anzeigenamen (:name) ein, um das Löschen zu bestätigen',
    'type_to_confirm'                => 'Gib folgendes ein, um zu bestätigen:',
    'delete_confirm_placeholder'     => 'Gib deinen Anzeigenamen ein',
    'delete_confirm_js'              => 'Bist du sicher? Dadurch wird dein Account und persönliche Daten dauerhaft gelöscht.',
    'delete_account_button'          => 'Account löschen',
    'delete_my_account'              => 'Meinen Account löschen',
    'delete_confirmation_warning'    => 'Bitte bestätige, um fortzufahren.',
    'delete_confirm_mismatch'        => 'Der eingegebene Name stimmt nicht mit deinem Anzeigenamen überein.',
    'delete_success'                 => 'Dein Account wurde gelöscht.',
    'delete_failed'                  => 'Account-Löschung fehlgeschlagen. Bitte kontaktiere den Support.',
    'delete_anonymized'              => 'Der Account konnte nicht vollständig gelöscht werden; persönliche Daten wurden stattdessen anonymisiert.',
    'account_management'             => 'Account-Verwaltung',
    'account_management_description' => 'Gefährliche Account-Aktionen, bitte mit Vorsicht vorgehen.',
    'delete_account_title'           => 'Account löschen',
    'delete_account_confirm'         => 'Möchtest du deinen Account wirklich löschen?',
    'delete_permanently'             => 'Endgültig löschen',
    'confirm'                        => 'Bestätigen',

    'settings_description' => 'Verwalte deine Kontoeinstellungen und Präferenzen.',

    // Profil / Einstellungen
    'your_profile'             => 'Dein Profil',
    'profile_info_description' => 'Verwalte deine Profilinfos, E-Mail und Avatar-Einstellungen.',
    'actions'                  => 'Aktionen',
    'enable_avatar'            => 'Avatare aktivieren',

    'account_roles'     => 'Rollen im Account',
    'roles_description' => 'Wähle die Rollen aus, die dich auf der Plattform beschreiben.',

    'connected_to_twitch' => 'Mit Twitch verbunden',

    'danger_zone_description' => 'Gefährliche Account-Aktionen, bitte mit Vorsicht vorgehen.',
    'close'                   => 'Schließen',

    // Rollen
    'roles'                          => 'Rollen',
    'label_viewer'                   => 'Viewer',
    'viewer'                         => 'Viewer',
    'viewer_description'             => 'Immer aktiv.',
    'label_streamer'                 => 'Streamer',
    'streamer'                       => 'Streamer',
    'streamer_description'           => 'Aktiviere Streamer-Funktionen, inklusive einer Vorstellungsbox.',
    'label_cutter'                   => 'Cutter',
    'cutter'                         => 'Cutter',
    'cutter_description'             => 'Aktiviere zusätzliche Cutter-Features und werde als Cutter gefunden.',
    'intro_label'                    => 'Stell dich vor',
    'introduce_yourself'             => 'Stell dich vor',
    'intro_placeholder'              => 'Schreibe eine kurze Vorstellung für deine Zuschauer (optional)',
    'intro_help'                     => 'Schreibe eine kurze Vorstellung für deine Zuschauer; sie wird auf deinem Profil angezeigt.',
    'available_for_jobs_label'       => 'Steht für Aufträge zur Verfügung',
    'available_for_jobs'             => 'Steht für Aufträge zur Verfügung',
    'available_for_jobs_description' => 'Markiere dich als verfügbar für Aufträge und zeige es auf deinem Profil an.',
    'role_hint_streamer'             => 'Aktiviere dieses Label, damit du eine kurze Vorstellung schreiben kannst, die auf deinem Profil angezeigt wird.',
    'role_hint_cutter'               => 'Zeigt die Option an, dich als für Aufträge verfügbar zu markieren.',
    'label_viewer_note'              => 'Immer aktiv',
    'unsaved_changes'                => 'Nicht gespeicherte Änderungen vorhanden.',
    'save_changes'                   => 'Änderungen speichern',
    'settings_save_failed'           => 'Einstellungen konnten nicht gespeichert werden.',

    'avatar_settings'             => 'Avatar-Einstellungen',
    'avatar_settings_description' => 'Dein Avatar wird, falls vorhanden, von Twitch bezogen. Hier kannst du ihn verwalten oder wiederherstellen.',
    'settings_avatar_description' => 'Dein Avatar wird, falls vorhanden, von Twitch bezogen. Hier kannst du ihn verwalten oder wiederherstellen.',
    'your_avatar'                 => 'Dein Avatar',
    'avatar_source'               => 'Quelle',
    'avatar_actions'              => 'Aktionen',
    'settings_avatar_disabled'    => 'Avatare sind deaktiviert',
    'avatar_disabled'             => 'Avatare sind deaktiviert',
    'avatar_active'               => 'Avatare sind aktiviert',
    'avatar_source_disabled'      => 'Deaktiviert',
    'avatar_source_twitch'        => 'Twitch',
    'avatar_source_custom'        => 'Eigenes',
    'remove_avatar_title'         => 'Avatar entfernen',
    'remove_avatar_confirm'       => 'Möchtest du das Avatar entfernen? Dadurch wird das lokal gespeicherte/verlinkte Avatar dauerhaft entfernt.',
    'remove'                      => 'Entfernen',
    'restore_avatar_title'        => 'Avatar wiederherstellen',
    'restore_avatar_confirm'      => 'Avatar von Twitch wiederherstellen?',
    'restore'                     => 'Wiederherstellen',
    'enable_avatar_title'         => 'Avatare aktivieren',
    'enable_avatar_confirm'       => 'Avatare aktivieren? Dadurch werden Avatare von Twitch wieder aktiviert, falls vorhanden.',
    'enable'                      => 'Aktivieren',
    'cancel'                      => 'Abbrechen',

    'restore_avatar_title'   => 'Avatar wiederherstellen',
    'restore_avatar_confirm' => 'Avatar von Twitch wiederherstellen?',
    'restore'                => 'Wiederherstellen',

    'danger_zone' => 'Gefahrenzone',

    'delete_account_title'       => 'Account löschen',
    'delete_account_warning'     => 'Diese Aktion wird dein Konto und persönliche Daten dauerhaft entfernen.',
    'delete_account_description' => 'Das Löschen deines Accounts entfernt persönliche Daten aus unserem System, soweit möglich. Manche für die Systemintegrität benötigten Datensätze werden eventuell anonymisiert statt gelöscht. Diese Aktion ist unwiderruflich.',
    'delete_permanently'         => 'Endgültig löschen',

    'no_changes' => 'Keine Änderungen vorgenommen.', ];
