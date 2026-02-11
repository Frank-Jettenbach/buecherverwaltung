<?php
/**
 * Bücherverwaltung - MySQL Datenbankverbindung & Auto-Setup
 */

$config = require __DIR__ . '/config.php';
$db = $config['db'];

try {
    // Datenbank erstellen falls nicht vorhanden
    $pdoInit = new PDO(
        "mysql:host={$db['host']};charset=utf8mb4",
        $db['user'],
        $db['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$db['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdoInit = null;

    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4",
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("DB-Verbindung fehlgeschlagen: " . $e->getMessage());
}

// Bücher-Tabelle erstellen
$pdo->exec("CREATE TABLE IF NOT EXISTS buecher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL DEFAULT '',
    isbn VARCHAR(20) DEFAULT '',
    kategorie VARCHAR(100) NOT NULL DEFAULT '',
    erscheinungsjahr INT DEFAULT NULL,
    bewertung TINYINT DEFAULT 0,
    gelesen TINYINT(1) DEFAULT 0,
    notizen TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT INDEX ft_search (titel, autor, isbn, kategorie, notizen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Spalten ausgeliehen/geliehen hinzufügen falls nicht vorhanden
$cols = $pdo->query("SHOW COLUMNS FROM buecher")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('ausgeliehen', $cols)) {
    $pdo->exec("ALTER TABLE buecher ADD COLUMN ausgeliehen TINYINT(1) DEFAULT 0 AFTER gelesen");
    $pdo->exec("ALTER TABLE buecher ADD COLUMN ausgeliehen_an VARCHAR(255) DEFAULT '' AFTER ausgeliehen");
    $pdo->exec("ALTER TABLE buecher ADD COLUMN geliehen TINYINT(1) DEFAULT 0 AFTER ausgeliehen_an");
    $pdo->exec("ALTER TABLE buecher ADD COLUMN geliehen_von VARCHAR(255) DEFAULT '' AFTER geliehen");
}

// Seed-Daten laden wenn Tabelle leer
$count = $pdo->query("SELECT COUNT(*) FROM buecher")->fetchColumn();
if ($count == 0) {
    require __DIR__ . '/seed.php';
    seedBuecher($pdo);
}
