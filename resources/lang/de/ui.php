<?php

return [
    // Farbauswahl
    'accent_color'  => 'Akzentfarbe',
    'reset_color'   => 'Farbe zurücksetzen',
    'reset'         => 'Zurücksetzen',
    'change_color'  => 'Farbe ändern: :color',
    'color_options' => 'Farboptionen',
    'color_purple'  => 'Lila',
    'color_blue'    => 'Blau',
    'color_green'   => 'Grün',
    'color_red'     => 'Rot',
    'color_orange'  => 'Orange',
    'color_pink'    => 'Pink',
    'color_indigo'  => 'Indigo',
    'color_teal'    => 'Türkis',
    'color_amber'   => 'Bernstein',
    'color_slate'   => 'Schiefer',

    // Controller-Feedback (hinzugefügt für UserSettingsController)
    'profile_updated'           => 'Profil erfolgreich aktualisiert.',
    'profile_update_failed'     => 'Profil konnte nicht aktualisiert werden.',
    'accent_updated'            => 'Akzentfarbe aktualisiert.',
    'preferences_update_failed' => 'Einstellungen konnten nicht aktualisiert werden.',
    'roles_updated'             => 'Rollen erfolgreich aktualisiert.',
    'roles_update_failed'       => 'Rollen konnten nicht aktualisiert werden.',
    'invalid_action'            => 'Ungültige Aktion.',
    'avatar_upload_success'     => 'Avatar erfolgreich hochgeladen.',
    'avatar_upload_failed'      => 'Avatar konnte nicht hochgeladen werden.',
    'avatar_removed'            => 'Avatar entfernt.',
    'not_connected_to_twitch'   => 'Nicht mit Twitch verbunden.',
    'twitch_connection_failed'  => 'Verbindung zu Twitch fehlgeschlagen.',
    'no_twitch_avatar'          => 'Kein Twitch-Avatar zum Wiederherstellen gefunden.',
    'avatar_restored'           => 'Avatar von Twitch wiederhergestellt.',
    'avatar_restore_failed'     => 'Avatar konnte nicht von Twitch wiederhergestellt werden.',

    'nav' => [
        'home'      => 'Start',
        'clips'     => 'Clips',
        'streamers' => 'Streamer',
        'cutters'   => 'Cutter',
        'faq'       => 'FAQ',
    ],

    // Allgemeine UI-Begriffe
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

    // Sonstiges
    'language_changed' => 'Sprache geändert: :lang',

    'common' => [
        'errors_found' => 'Fehler gefunden',
        'note'         => 'Hinweis',
    ],

    // Einstellungen
    'settings_saved'             => 'Einstellungen gespeichert.',
    'save'                       => 'Speichern',
    'settings_twitch'            => 'Twitch',
    'settings_twitch_connected'  => 'Verbunden mit Twitch als :name',
    'settings_disconnect_twitch' => 'Twitch trennen',
    'settings_connect_twitch'    => 'Twitch verbinden',

    'settings_avatar'             => 'Avatar',
    'settings_avatar_description' => 'Dein Avatar wird, falls verfügbar, von Twitch übernommen. Verwalte oder stelle ihn hier wieder her.',
    'settings_avatar_disabled'    => 'Avatare sind deaktiviert',

    // Aktueller Avatar und Quell-Labels
    'current_avatar'         => 'Aktueller Avatar',
    'avatar_from_twitch'     => 'Von Twitch',
    'avatar_custom'          => 'Benutzerdefinierter Avatar',
    'avatar_remove_failed'   => 'Avatar konnte nicht entfernt werden.',
    'avatar_remove_success'  => 'Avatar entfernt.',

    'remove_avatar'         => 'Avatar entfernen',
    'remove_avatar_title'   => 'Avatar entfernen',
    'remove_avatar_confirm' => 'Bist du sicher, dass du deinen Avatar entfernen möchtest? Dadurch wird der lokal gespeicherte oder verlinkte Avatar dauerhaft gelöscht.',

    'restore_avatar'             => 'Avatar von Twitch wiederherstellen',
    'settings_restore_success'   => 'Avatar von Twitch wiederhergestellt.',
    'settings_restore_failed'    => 'Avatar konnte nicht von Twitch wiederhergestellt werden.',
    'settings_restore_no_avatar' => 'Auf Twitch wurde kein Avatar zum Wiederherstellen gefunden.',
    'settings_not_connected'     => 'Nicht mit Twitch verbunden.',

    'delete_account'                 => 'Konto löschen',
    'delete_account_explain'         => 'Das Löschen deines Kontos entfernt deine persönlichen Daten, wo möglich, aus unseren Systemen. Einige für die Systemintegrität erforderliche Datensätze können anstelle einer Löschung anonymisiert werden. Diese Aktion ist unwiderruflich.',
    'delete_account_warning'         => 'Diese Aktion wird dein Konto und deine persönlichen Daten dauerhaft entfernen.',
    'delete_account_consequence_1'   => 'Deine persönlichen Daten werden, wo möglich, entfernt.',
    'delete_account_consequence_2'   => 'Einige Datensätze können anstelle einer Löschung anonymisiert werden.',
    'delete_account_consequence_3'   => 'Diese Aktion ist unwiderruflich.',
    'delete_confirm_label'           => 'Gib deinen Anzeigenamen (:name) zur Bestätigung der Löschung ein',
    'type_to_confirm'                => 'Gib Folgendes zur Bestätigung ein:',
    'delete_confirm_placeholder'     => 'Gib deinen Anzeigenamen ein',
    'delete_confirm_js'              => 'Bist du sicher? Dadurch wird dein Konto und deine persönlichen Daten dauerhaft gelöscht.',
    'delete_account_button'          => 'Konto löschen',
    'delete_my_account'              => 'Mein Konto löschen',
    'delete_confirmation_warning'    => 'Bitte bestätige durch Eingabe deines Anzeigenamens, um fortzufahren.',
    'delete_confirm_mismatch'        => 'Der eingegebene Name stimmt nicht mit deinem Anzeigenamen überein.',
    'delete_success'                 => 'Dein Konto wurde gelöscht.',
    'delete_failed'                  => 'Dein Konto konnte nicht gelöscht werden. Bitte kontaktiere den Support.',
    'delete_anonymized'              => 'Dein Konto konnte nicht vollständig gelöscht werden; deine persönlichen Daten wurden stattdessen anonymisiert.',
    'account_management'             => 'Kontoverwaltung',
    'account_management_description' => 'Gefährliche Kontoaktionen, bitte mit Vorsicht fortfahren.',
    'delete_account_title'           => 'Konto löschen',
    'delete_account_confirm'         => 'Bist du sicher, dass du dein Konto löschen möchtest?',
    'delete_permanently'             => 'Dauerhaft löschen',
    'confirm'                        => 'Bestätigen',

    'settings_description' => 'Verwalte deine Kontoeinstellungen und Präferenzen.',

    // Profil / Einstellungen Hilfen
    'your_profile'             => 'Dein Profil',
    'profile_info_description' => 'Verwalte deine öffentlichen Profildetails, E-Mail- und Avatar-Einstellungen.',
    'actions'                  => 'Aktionen',
    'enable_avatar'            => 'Avatare aktivieren',

    'account_roles'     => 'Konto-Rollen',
    'roles_description' => 'Wähle die Rollen aus, die dich auf der Plattform beschreiben.',

    'connected_to_twitch' => 'Mit Twitch verbunden',

    'danger_zone_description' => 'Gefährliche Kontoaktionen, bitte mit Vorsicht fortfahren.',
    'close'                   => 'Schließen',

    // Rollen
    'roles'                          => 'Rollen',
    'label_viewer'                   => 'Zuschauer',
    'viewer'                         => 'Zuschauer',
    'viewer_description'             => 'Immer aktiv.',
    'label_streamer'                 => 'Streamer',
    'streamer'                       => 'Streamer',
    'streamer_description'           => 'Aktiviere Streamer-Funktionen, inklusive einer "Stell dich vor"-Box.',
    'label_cutter'                   => 'Cutter',
    'cutter'                         => 'Cutter',
    'cutter_description'             => 'Aktiviere zusätzliche Cutter-Funktionen und werde als Cutter auffindbar.',
    'intro_label'                    => 'Stell dich vor',
    'introduce_yourself'             => 'Stell dich vor',
    'intro_placeholder'              => 'Schreibe eine kurze Vorstellung für deine Zuschauer (optional)',
    'intro_help'                     => 'Schreibe eine kurze Vorstellung für deine Zuschauer; sie wird auf deinem Profil erscheinen.',
    'available_for_jobs_label'       => 'Für Aufträge verfügbar',
    'available_for_jobs'             => 'Für Aufträge verfügbar',
    'available_for_jobs_description' => 'Markiere dich als verfügbar für freiberufliche Schnittaufträge und zeige dies auf deinem Profil an.',
    'role_hint_streamer'             => 'Aktiviert eine kurze "Stell dich vor"-Box, in der du eine kurze Bio schreiben kannst, die auf deinem Profil angezeigt wird.',
    'role_hint_cutter'               => 'Zeigt eine Option an, um dich als verfügbar für Aufträge zu markieren und dies öffentlich anzuzeigen.',
    'label_viewer_note'              => 'Immer aktiv',
    'unsaved_changes'                => 'Du hast ungespeicherte Änderungen.',
    'save_changes'                   => 'Änderungen speichern',
    'settings_save_failed'           => 'Einstellungen konnten nicht gespeichert werden.',

    'clip_sharing'             => 'Clip-Weitergabe erlauben',
    'clip_sharing_description' => 'Erlaube anderen, Clips von deinen Streams zu teilen und zu verbreiten.',

    'availability'             => 'Verfügbarkeit',
    'availability_description' => 'Lege deine Verfügbarkeit für freiberufliche Schnittarbeit fest.',

    'active'   => 'Aktiv',
    'inactive' => 'Inaktiv',

    'avatar_settings'             => 'Avatar-Einstellungen',
    'avatar_settings_description' => 'Dein Avatar wird, falls verfügbar, von Twitch übernommen. Verwalte oder stelle ihn hier wieder her.',
    'settings_avatar_description' => 'Dein Avatar wird, falls verfügbar, von Twitch übernommen. Verwalte oder stelle ihn hier wieder her.',
    'your_avatar'                 => 'Dein Avatar',
    'avatar_source'               => 'Quelle',
    'avatar_actions'              => 'Aktionen',
    'avatar_disabled'             => 'Avatare sind deaktiviert',
    'avatar_active'               => 'Avatare aktiviert',
    'avatar_source_disabled'      => 'Deaktiviert',
    'avatar_source_twitch'        => 'Twitch',
    'avatar_source_custom'        => 'Benutzerdefiniert',
    'remove_avatar_title'         => 'Avatar entfernen',
    'remove_avatar_confirm'       => 'Bist du sicher, dass du deinen Avatar entfernen möchtest? Dadurch wird der lokal gespeicherte oder verlinkte Avatar dauerhaft gelöscht.',
    'remove'                      => 'Entfernen',
    'restore_avatar_title'        => 'Avatar wiederherstellen',
    'restore_avatar_confirm'      => 'Avatar von Twitch wiederherstellen?',
    'restore'                     => 'Wiederherstellen',
    'enable_avatar_title'         => 'Avatare aktivieren',
    'enable_avatar_confirm'       => 'Avatare aktivieren? Dadurch werden Avatare von Twitch, falls verfügbar, wieder aktiviert.',
    'enable'                      => 'Aktivieren',
    'cancel'                      => 'Abbrechen',

    'restore_avatar_title'   => 'Avatar wiederherstellen',
    'restore_avatar_confirm' => 'Avatar von Twitch wiederherstellen?',
    'restore'                => 'Wiederherstellen',

    'danger_zone' => 'Gefahrenzone',

    'delete_account_title'       => 'Konto löschen',
    'delete_account_warning'     => 'Diese Aktion wird dein Konto und deine persönlichen Daten dauerhaft entfernen.',
    'delete_account_description' => 'Das Löschen deines Kontos entfernt deine persönlichen Daten, wo möglich, aus unseren Systemen. Einige für die Systemintegrität erforderliche Datensätze können anstelle einer Löschung anonymisiert werden. Diese Aktion ist unwiderruflich.',
    'delete_permanently'         => 'Dauerhaft löschen',

    'no_changes' => 'Es wurden keine Änderungen vorgenommen.',

    // Validierungsnachrichten
    'validation' => [
        'confirm_name_required' => 'Bitte gib deinen Anzeigenamen zur Bestätigung ein.',
        'confirm_name_mismatch' => 'Der eingegebene Name stimmt nicht mit deinem Anzeigenamen überein.',
        'password_required'     => 'Bitte gib dein Passwort ein.',
        'password_incorrect'    => 'Das eingegebene Passwort ist falsch.',
        'display_name_max'      => 'Der Anzeigename darf maximal 255 Zeichen lang sein.',
        'email_required'        => 'E-Mail-Adresse ist erforderlich.',
        'email_invalid'         => 'Bitte gib eine gültige E-Mail-Adresse ein.',
        'email_taken'           => 'Diese E-Mail-Adresse wird bereits verwendet.',
        'intro_max'             => 'Die Vorstellung darf maximal 1000 Zeichen lang sein.',
        'avatar_required'       => 'Bitte wähle eine Avatar-Datei aus.',
        'avatar_image'          => 'Die hochgeladene Datei muss ein Bild sein.',
        'avatar_mimes'          => 'Der Avatar muss eine der folgenden Formate haben: JPG, JPEG, PNG, GIF, WebP.',
        'avatar_max_size'       => 'Der Avatar darf maximal 5 MB groß sein.',
        'avatar_dimensions'     => 'Der Avatar muss quadratisch sein (1:1 Seitenverhältnis) und zwischen 100x100 und 2000x2000 Pixel groß.',
    ],
];
