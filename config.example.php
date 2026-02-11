<?php
/**
 * Bücherverwaltung - Konfiguration
 *
 * Kopiere diese Datei nach config.php und passe die Werte an:
 *   cp config.example.php config.php
 */
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'buecherverwaltung',
        'user' => 'dein_db_user',
        'pass' => 'dein_db_passwort',
    ],
    'app' => [
        'title'    => 'Bücherverwaltung',
        'base_url' => '/buecherverwaltung',
    ],
];
