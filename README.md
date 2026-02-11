# Bücherverwaltung

Self-Hosted Webanwendung zur Verwaltung einer persönlichen Büchersammlung. Gebaut mit PHP, MySQL und Vanilla JavaScript im Dark-Mode-Design.

## Features

- **CRUD** — Bücher hinzufügen, bearbeiten, löschen
- **21 Kategorien** — Vordefiniertes Dropdown (Fantasy, Krimi & Thriller, Science-Fiction, Technik & IT, …)
- **Sternebewertung** — 1–5 Sterne per Klick
- **Gelesen-Status** — Toggle direkt in der Tabelle
- **Ausleih-Verwaltung** — Bücher als ausgeliehen oder geliehen markieren, mit optionalem Personennamen
- **Fuzzy-Suche** — Fehlertolerante Suche über Titel, Autor, ISBN, Kategorie und Notizen
- **Filter** — Nach Kategorie, Gelesen/Ungelesen, Ausgeliehen, Geliehen
- **Statistiken** — Gesamt, Gelesen, Ungelesen, Ø Bewertung, Ausgeliehen, Geliehen
- **Import/Export** — JSON-Format für Backup und Datentransfer
- **Dark Mode** — Durchgängiges dunkles Design
- **Responsive** — Optimiert für Desktop, Tablet und Smartphone
- **Tastenkürzel** — `Strg+N` (Neues Buch), `Strg+K` (Suche), `Escape` (Schließen)

## Screenshots

### Hauptansicht
Büchertabelle mit Sidebar-Kategorien, Sternebewertung, Status-Badges und Statistiken.

![Hauptansicht](screenshots/01_hauptansicht.png)

### Buch hinzufügen / bearbeiten
Formular mit Kategorie-Dropdown, Sternebewertung, Gelesen-Toggle und Ausleih-Checkboxen.

![Neues Buch](screenshots/02_neues_buch.png)

### Lösch-Bestätigung
Sicherheitsabfrage vor dem Löschen eines Buches.

![Löschen](screenshots/03_loeschen.png)

## Voraussetzungen

- Apache 2.4+ mit PHP-Modul
- PHP 7.4+ (PDO, pdo_mysql)
- MySQL 5.7+ / MariaDB 10.3+

## Installation

1. **Repository klonen:**
   ```bash
   git clone https://github.com/Frank-Jettenbach/buecherverwaltung.git /opt/buecherverwaltung
   ```

2. **Konfiguration erstellen:**
   ```bash
   cd /opt/buecherverwaltung
   cp config.example.php config.php
   ```
   Dann `config.php` bearbeiten und DB-Zugangsdaten eintragen.

3. **MySQL-Datenbank anlegen:**
   ```sql
   CREATE DATABASE buecherverwaltung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   GRANT ALL PRIVILEGES ON buecherverwaltung.* TO 'dein_user'@'localhost';
   ```

4. **Apache einrichten:**
   ```bash
   ln -s /opt/buecherverwaltung /var/www/html/buecherverwaltung
   ```

5. **Aufrufen:** `http://dein-server/buecherverwaltung/`

   Beim ersten Aufruf werden die Tabellen und 12 Beispielbücher automatisch angelegt.

## Technologie-Stack

| Komponente | Technologie |
|---|---|
| Backend | PHP (Vanilla, kein Framework) |
| Datenbank | MySQL/MariaDB mit PDO |
| Frontend | Vanilla JavaScript |
| Styling | CSS3 mit Custom Properties |
| Suche | Levenshtein-basierte Fuzzy-Suche |

## API

REST-API unter `/buecherverwaltung/api.php`:

| Endpunkt | Methode | Beschreibung |
|---|---|---|
| `?action=list` | GET | Bücher auflisten (mit Filtern) |
| `?action=get&id=1` | GET | Einzelnes Buch laden |
| `?action=create` | POST | Buch erstellen |
| `?action=update` | POST | Buch aktualisieren |
| `?action=delete` | POST | Buch löschen |
| `?action=toggle_gelesen` | POST | Gelesen-Status umschalten |
| `?action=categories` | GET | Kategorien mit Anzahl |
| `?action=stats` | GET | Statistiken |
| `?action=export` | GET | JSON-Export (Download) |
| `?action=import` | POST | JSON-Import |

## Dokumentation

Die vollständige technische und Bedienungs-Dokumentation befindet sich in [DOKUMENTATION.md](DOKUMENTATION.md).

## Lizenz

Privates Projekt.
