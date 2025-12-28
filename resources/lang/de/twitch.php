<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twitch Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for Twitch integration messages
    | and errors. Modify them according to your application requirements.
    |
    */

    // OAuth
    'oauth' => [
        'login_with_twitch'      => 'Mit Twitch anmelden',
        'authorize'              => 'Autorisieren',
        'authorizing'            => 'Autorisieren…',
        'authorization_required' => 'Twitch-Autorisierung erforderlich',
        'authorization_failed'   => 'Autorisierung fehlgeschlagen',
        'token_expired'          => 'Ihr Twitch-Token ist abgelaufen. Bitte melden Sie sich erneut an.',
        'token_invalid'          => 'Ungültiges Twitch-Token',
        'logout_success'         => 'Erfolgreich von Twitch abgemeldet',
        'login_success'          => 'Erfolgreich mit Twitch angemeldet',
    ],

    // Login / Config helper
    'login_config_missing' => 'Die Twitch API ist nicht konfiguriert. Bitte setze',
    'or'                   => 'oder',
    'login_config_doc'     => 'Siehe .env.example oder das Projekt-README für Konfigurationshinweise.',

    // Clips
    'clips' => [
        'title'           => 'Clips',
        'create'          => 'Clip einreichen',
        'creating'        => 'Reiche Clip ein...',
        'created'         => 'Clip erfolgreich eingereicht',
        'create_failed'   => 'Clip konnte nicht eingereicht werden',
        'not_found'       => 'Clip nicht gefunden',
        'loading'         => 'Clips werden geladen...',
        'no_clips'        => 'Keine Clips verfügbar',
        'view_count'      => 'Aufrufe',
        'duration'        => 'Dauer',
        'created_at'      => 'Erstellt am',
        'broadcaster'     => 'Streamer',
        'creator'         => 'Ersteller',
        'watch_on_twitch' => 'Auf Twitch ansehen',
        'share'           => 'Teilen',
    ],

    // Errors
    'errors' => [
        'api_error'         => 'Twitch API Fehler',
        'rate_limit'        => 'Zu viele Anfragen. Bitte versuchen Sie es in :seconds Sekunden erneut.',
        'connection_failed' => 'Verbindung zu Twitch fehlgeschlagen',
        'invalid_request'   => 'Ungültige Anfrage',
        'unauthorized'      => 'Nicht autorisiert',
        'forbidden'         => 'Zugriff verweigert',
        'not_found'         => 'Ressource nicht gefunden',
        'server_error'      => 'Twitch Server Fehler',
        'timeout'           => 'Anfrage-Timeout',
        'unknown'           => 'Unbekannter Fehler',
    ],

    // Privacy (GDPR)
    'privacy' => [
        'consent_required' => 'Zustimmung zur Datenverarbeitung erforderlich',
        'data_usage'       => 'Ihre Twitch-Daten werden gemäß unserer Datenschutzrichtlinie verarbeitet.',
        'revoke_access'    => 'Zugriff widerrufen',
        'revoke_confirm'   => 'Möchten Sie den Twitch-Zugriff wirklich widerrufen?',
        'data_retention'   => 'Daten werden für :days Tage gespeichert',

        // Avatar-Verarbeitung / DSGVO
        'avatar_title'      => 'Avatar-Verwaltung',
        'avatar_download'   => 'Mit Ihrer Zustimmung laden wir Ihr Twitch-Profilbild (Avatar) herunter und speichern es lokal, damit wir Aufbewahrung und Löschung DSGVO-konform steuern können.',
        'avatar_storage'    => 'Gespeicherte Avatare werden sicher aufbewahrt und verbleiben, bis Sie sie löschen oder Ihr Konto löschen.',
        'consent_error'     => 'Bitte bestätige, dass du der Verarbeitung deiner Daten zustimmst, um fortzufahren.',
        'short_intro'       => 'Datenschutz zuerst: Token werden verschlüsselt und für :days Tage aufbewahrt',
    ],

    // Login / Privacy helper texts
    'login_title'          => 'Anmelden',
    'login_subtitle'       => 'Melde dich mit Twitch an, um Clips einzureichen, dein Erlebnis zu personalisieren und mit der Community zu interagieren.',
    'login_cta'            => 'Mit Twitch fortfahren',
    'login_privacy_intro'  => 'Wenn Sie sich mit Twitch anmelden, speichern wir nur die minimal notwendigen Daten und verwenden sie ausschließlich zur Bereitstellung des Dienstes. Sie können den Zugriff jederzeit widerrufen.',
    'privacy_item_tokens'  => 'Wir speichern Zugangstoken und Refresh-Token verschlüsselt, um Ihre Sitzung zu erhalten und Funktionen wie das Einreichen von Clips zu ermöglichen; Tokens werden gespeichert, bis Sie sich abmelden oder Ihr Konto löschen.',
    'privacy_item_ip'      => 'IP-Anonymisierung ist aktiviert',
    'privacy_item_logging' => 'Request-Logging ist aktiviert',
    'privacy_yes'          => 'Ja',
    'privacy_no'           => 'Nein',
    'login_privacy_more'   => 'Lesen Sie mehr in unserer Datenschutzrichtlinie',
    'login_privacy_note'   => 'Wir verkaufen Ihre Daten nicht. Avatare werden gespeichert, bis Sie sie löschen oder Ihr Konto löschen. Zugangstoken und Refresh-Token werden verschlüsselt gespeichert, bis Sie sich abmelden oder Ihr Konto gelöscht wird; Tokens werden bei jeder neuen Anmeldung ersetzt.',

    'login_cta_sub' => 'Sie werden zu Twitch weitergeleitet, um die Autorisierung vorzunehmen; wir speichern Zugriffsdaten nur verschlüsselt und sicher.',

    // Config / helper
    'login_need_config' => 'Die Twitch-Client-Konfiguration fehlt. Bitte setze TWITCH_CLIENT_ID und TWITCH_CLIENT_SECRET in deiner Umgebung.',

];
