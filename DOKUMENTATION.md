# Bücherverwaltung - Dokumentation

## Inhaltsverzeichnis

1. [Übersicht](#übersicht)
2. [Bedienungsanleitung](#bedienungsanleitung)
   - [Startseite](#startseite)
   - [Bücher hinzufügen](#bücher-hinzufügen)
   - [Bücher bearbeiten](#bücher-bearbeiten)
   - [Bücher löschen](#bücher-löschen)
   - [Suche und Filter](#suche-und-filter)
   - [Ausgeliehen / Geliehen](#ausgeliehen--geliehen)
   - [Sternebewertung](#sternebewertung)
   - [Import und Export](#import-und-export)
   - [Tastenkürzel](#tastenkürzel)
3. [Technische Dokumentation](#technische-dokumentation)
   - [Architektur](#architektur)
   - [Verzeichnisstruktur](#verzeichnisstruktur)
   - [Voraussetzungen](#voraussetzungen)
   - [Installation](#installation)
   - [Datenbank](#datenbank)
   - [REST API](#rest-api)
   - [Frontend](#frontend)
   - [Sicherheit](#sicherheit)

---

## Übersicht

Die Bücherverwaltung ist eine Self-Hosted Webanwendung zur Verwaltung einer persönlichen Büchersammlung. Sie bietet eine moderne, dunkle Benutzeroberfläche und ermöglicht das Hinzufügen, Bearbeiten, Löschen und Suchen von Büchern. Zusätzlich können Bücher als ausgeliehen oder geliehen markiert werden.

**Zugriff:** `http://<server-ip>/buecherverwaltung/`

**Technologien:** PHP 7.4+, MySQL/MariaDB, Vanilla JavaScript, CSS3

![Hauptansicht](screenshots/01_hauptansicht.png)

---

## Bedienungsanleitung

### Startseite

Die Anwendung besteht aus drei Bereichen:

```
┌─────────────────────────────────────────────────────────┐
│  Navigation (oben)                                      │
├──────────────┬──────────────────────────────────────────┤
│              │  Suchleiste + Aktionsbuttons             │
│  Kategorien  │  Filter-Tabs (Alle/Gelesen/...)         │
│              │──────────────────────────────────────────│
│              │                                          │
│  Statistiken │  Bücher-Tabelle                          │
│              │  (gruppiert nach Kategorie)              │
│              │                                          │
└──────────────┴──────────────────────────────────────────┘
```

- **Linke Sidebar:** Kategorien (klickbar zum Filtern) und Statistiken
- **Hauptbereich:** Suchleiste, Filter-Tabs und die Büchertabelle
- **Navigation:** Verknüpfungen zu anderen Anwendungen (Linkmanager, Befehlsmanager, Host2Host)

---

### Bücher hinzufügen

![Neues Buch Formular](screenshots/02_neues_buch.png)

1. Klicke auf den Button **„+ Neues Buch"** in der Toolbar (oder drücke `Strg+N`)
2. Fülle das Formular aus:
   - **Titel** (Pflichtfeld): Name des Buches
   - **Autor** (Pflichtfeld): Name des Autors
   - **ISBN** (optional): ISBN-Nummer, z.B. `978-3608938289`
   - **Kategorie** (Pflichtfeld): Wähle eine Kategorie aus dem Dropdown
   - **Erscheinungsjahr** (optional): z.B. `2024`
   - **Bewertung** (optional): Klicke auf 1–5 Sterne (nochmal klicken zum Zurücksetzen)
   - **Status**: Schalte den Toggle auf „Gelesen" oder „Ungelesen"
   - **Ausgeliehen**: Checkbox ankreuzen und optional den Namen der Person eintragen
   - **Geliehen**: Checkbox ankreuzen und optional den Namen der Person eintragen
   - **Notizen** (optional): Eigene Anmerkungen zum Buch
3. Klicke auf **„Speichern"**

**Verfügbare Kategorien im Dropdown:**

| Kategorie | Kategorie | Kategorie |
|---|---|---|
| Belletristik | Kinderbuch | Religion & Spiritualität |
| Biografie | Kochen | Roman |
| Fantasy | Krimi & Thriller | Sachbuch |
| Geschichte | Kunst & Musik | Science-Fiction |
| Humor | Lyrik | Technik & IT |
|  | Philosophie | Wirtschaft |
|  | Psychologie | Wissenschaft |
|  | Ratgeber |  |
|  | Reise |  |

---

### Bücher bearbeiten

1. In der Büchertabelle auf das **Stift-Symbol** (✏) in der Zeile des Buches klicken
2. Das Formular öffnet sich vorausgefüllt mit den aktuellen Daten
3. Gewünschte Änderungen vornehmen
4. Auf **„Speichern"** klicken

**Schnell-Aktion — Gelesen-Status umschalten:**
Klicke direkt auf das Status-Badge (z.B. „✓ Gelesen" oder „○ Ungelesen") in der Tabelle, um den Status sofort umzuschalten, ohne das Formular öffnen zu müssen.

---

### Bücher löschen

![Lösch-Bestätigung](screenshots/03_loeschen.png)

1. In der Büchertabelle auf das **Mülleimer-Symbol** (🗑) klicken
2. Es erscheint eine Sicherheitsabfrage mit dem Buchtitel
3. Mit **„Löschen"** bestätigen oder mit **„Abbrechen"** abbrechen

---

### Suche und Filter

#### Volltextsuche
- Klicke in die **Suchleiste** (oder drücke `Strg+K`)
- Die Suche durchsucht: Titel, Autor, ISBN, Kategorie und Notizen
- Die Suche ist **fehlertolerant** (Fuzzy-Suche): Auch bei Tippfehlern werden passende Ergebnisse gefunden
- Die Suche startet automatisch nach 300ms Tippause
- Mit dem **✕** rechts im Suchfeld die Suche leeren

#### Kategoriefilter
- Klicke in der **linken Sidebar** auf eine Kategorie, um nur Bücher dieser Kategorie anzuzeigen
- Klicke auf **„Alle Bücher"**, um den Filter aufzuheben

#### Status-Filter (Tabs)
Über der Tabelle befinden sich Filter-Tabs:

| Tab | Zeigt |
|-----|-------|
| **Alle** | Alle Bücher |
| **Gelesen** | Nur gelesene Bücher |
| **Ungelesen** | Nur ungelesene Bücher |
| **Ausgeliehen** | Nur ausgeliehene Bücher |
| **Geliehen** | Nur geliehene Bücher |

#### Aktive Filter
- Wenn Filter aktiv sind, wird eine Filterleiste unter den Tabs angezeigt
- Klicke auf **„Alle Filter entfernen"**, um alle Filter zurückzusetzen

---

### Ausgeliehen / Geliehen

Bücher können als **ausgeliehen** (du hast das Buch jemand anderem gegeben) oder **geliehen** (du hast das Buch von jemandem geliehen) markiert werden.

**Im Formular:**
- Kreuze die Checkbox **„Ausgeliehen"** an → Es erscheint ein Textfeld „An wen ausgeliehen?"
- Kreuze die Checkbox **„Geliehen"** an → Es erscheint ein Textfeld „Von wem geliehen?"
- Die Namenseingabe ist optional, hilft aber den Überblick zu behalten

**In der Tabelle:**
- Ausgeliehene Bücher zeigen ein **oranges Badge** „↗ Ausg." mit dem Namen
- Geliehene Bücher zeigen ein **lila Badge** „↙ Gel." mit dem Namen

**Filtern:**
- Über den Tab **„Ausgeliehen"** alle aktuell verliehenen Bücher anzeigen
- Über den Tab **„Geliehen"** alle aktuell geliehenen Bücher anzeigen

---

### Sternebewertung

- Im Formular auf die Sterne (1–5) klicken, um eine Bewertung zu vergeben
- Nochmal auf den gleichen Stern klicken, um die Bewertung zu entfernen (auf 0 setzen)
- In der Tabelle werden die Sterne gold ausgefüllt dargestellt

---

### Import und Export

#### Export
1. Klicke auf **„↓ Export"** in der Toolbar
2. Eine JSON-Datei wird heruntergeladen (`buecherverwaltung_export_YYYY-MM-DD.json`)
3. Die Datei enthält alle Bücher mit allen Feldern

#### Import
1. Klicke auf **„↑ Import"** in der Toolbar
2. Wähle eine JSON-Datei aus (gleiches Format wie der Export)
3. Die importierten Bücher werden **hinzugefügt** (bestehende bleiben erhalten)
4. Eine Erfolgsmeldung zeigt die Anzahl importierter Bücher

**JSON-Format für Import:**
```json
[
  {
    "titel": "Der Herr der Ringe",
    "autor": "J.R.R. Tolkien",
    "isbn": "978-3608938289",
    "kategorie": "Fantasy",
    "erscheinungsjahr": 1954,
    "bewertung": 5,
    "gelesen": 1,
    "ausgeliehen": 0,
    "ausgeliehen_an": "",
    "geliehen": 0,
    "geliehen_von": "",
    "notizen": "Meisterwerk der Fantasy-Literatur"
  }
]
```

---

### Tastenkürzel

| Tastenkürzel | Aktion |
|---|---|
| `Strg + N` | Neues Buch hinzufügen |
| `Strg + K` | Suchfeld fokussieren |
| `Escape` | Modal / Dialog schließen |

Auf macOS: `Cmd` statt `Strg`

---

### Statistiken

Die Sidebar zeigt ein Statistik-Widget mit sechs Werten:

| Statistik | Beschreibung |
|---|---|
| **Gesamt** | Gesamtanzahl aller Bücher |
| **Gelesen** | Anzahl gelesener Bücher |
| **Ungelesen** | Anzahl ungelesener Bücher |
| **Ø Bewertung** | Durchschnittliche Sternebewertung (nur bewertete Bücher) |
| **Ausgeliehen** | Anzahl aktuell verliehener Bücher |
| **Geliehen** | Anzahl aktuell geliehener Bücher |

Die Statistiken aktualisieren sich automatisch nach jeder Änderung.

---

## Technische Dokumentation

### Architektur

Die Anwendung folgt einer klassischen **3-Schichten-Architektur**:

```
┌──────────────────────────────────────────┐
│          Präsentation (Frontend)         │
│   index.php + style.css + app.js         │
├──────────────────────────────────────────┤
│          API-Schicht (Backend)           │
│              api.php                     │
├──────────────────────────────────────────┤
│          Datenschicht (Datenbank)        │
│        db.php → MySQL/MariaDB           │
└──────────────────────────────────────────┘
```

- **Frontend:** Single-Page-Application (SPA) Verhalten mit Vanilla JavaScript
- **Backend:** Vanilla PHP ohne Framework, REST-API mit JSON
- **Datenbank:** MySQL/MariaDB mit PDO und Prepared Statements

---

### Verzeichnisstruktur

```
/opt/buecherverwaltung/
├── index.php              # Haupt-HTML-Template (SPA)
├── api.php                # REST-API Endpunkte
├── db.php                 # Datenbankverbindung & Auto-Setup
├── config.php             # Konfiguration (DB-Zugangsdaten)
├── menu.php               # Navigationsleiste (PHP Include)
├── seed.php               # Beispieldaten (12 Bücher)
├── DOKUMENTATION.md       # Diese Dokumentation
└── assets/
    ├── style.css          # Dark-Mode CSS (500+ Zeilen)
    ├── app.js             # Frontend-Logik (600+ Zeilen)
    └── favicon.svg        # Buch-Icon als SVG
```

**Symlink für Apache:**
```
/var/www/html/buecherverwaltung → /opt/buecherverwaltung
```

---

### Voraussetzungen

| Komponente | Version |
|---|---|
| Apache | 2.4+ mit `mod_php` oder PHP-FPM |
| PHP | 7.4+ mit PDO und pdo_mysql Extension |
| MySQL / MariaDB | 5.7+ / 10.3+ |

---

### Installation

1. **Dateien kopieren:**
   ```bash
   cp -r buecherverwaltung /opt/buecherverwaltung
   ```

2. **Konfiguration anpassen** (`config.php`):
   ```php
   return [
       'db' => [
           'host' => 'localhost',
           'name' => 'buecherverwaltung',
           'user' => 'DEIN_DB_USER',
           'pass' => 'DEIN_DB_PASSWORT',
       ],
       'app' => [
           'title'    => 'Bücherverwaltung',
           'base_url' => '/buecherverwaltung',
       ],
   ];
   ```

3. **MySQL-Datenbank erstellen:**
   ```sql
   CREATE DATABASE buecherverwaltung
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;

   GRANT ALL PRIVILEGES ON buecherverwaltung.*
     TO 'DEIN_DB_USER'@'localhost';
   ```

4. **Apache Symlink erstellen:**
   ```bash
   ln -s /opt/buecherverwaltung /var/www/html/buecherverwaltung
   ```

5. **Erster Aufruf:** Die Tabellen und Beispieldaten werden beim ersten Zugriff automatisch angelegt.

---

### Datenbank

#### Tabelle: `buecher`

| Spalte | Typ | Beschreibung |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | Eindeutige ID |
| `titel` | VARCHAR(255) NOT NULL | Buchtitel |
| `autor` | VARCHAR(255) DEFAULT '' | Autorname |
| `isbn` | VARCHAR(20) DEFAULT '' | ISBN-Nummer |
| `kategorie` | VARCHAR(100) DEFAULT '' | Buchkategorie |
| `erscheinungsjahr` | INT DEFAULT NULL | Erscheinungsjahr |
| `bewertung` | TINYINT DEFAULT 0 | Bewertung 0–5 |
| `gelesen` | TINYINT(1) DEFAULT 0 | 0 = Ungelesen, 1 = Gelesen |
| `ausgeliehen` | TINYINT(1) DEFAULT 0 | 0 = Nein, 1 = Ausgeliehen |
| `ausgeliehen_an` | VARCHAR(255) DEFAULT '' | Name der Person |
| `geliehen` | TINYINT(1) DEFAULT 0 | 0 = Nein, 1 = Geliehen |
| `geliehen_von` | VARCHAR(255) DEFAULT '' | Name der Person |
| `notizen` | TEXT DEFAULT NULL | Freitext-Notizen |
| `created_at` | DATETIME | Erstellungszeitpunkt |
| `updated_at` | DATETIME | Letzter Änderungszeitpunkt |

**Index:** `FULLTEXT (titel, autor, isbn, kategorie, notizen)` für Volltextsuche

#### Auto-Setup

Die Datei `db.php` führt beim Start automatisch folgende Schritte aus:
1. Datenbank erstellen (falls nicht vorhanden)
2. Tabelle `buecher` erstellen (falls nicht vorhanden)
3. Spalten `ausgeliehen`, `ausgeliehen_an`, `geliehen`, `geliehen_von` nachrüsten (Migration)
4. Beispieldaten laden, wenn die Tabelle leer ist (12 Bücher)

---

### REST API

**Basis-URL:** `/buecherverwaltung/api.php`

Alle Antworten sind JSON mit `Content-Type: application/json; charset=utf-8`.

#### Bücher auflisten

```
GET ?action=list[&kategorie=Fantasy][&gelesen=1][&ausgeliehen=1][&geliehen=1][&search=tolkien]
```

| Parameter | Typ | Beschreibung |
|---|---|---|
| `kategorie` | string | Filtert nach Kategorie (exakter Match) |
| `gelesen` | 0/1 | Filtert nach Gelesen-Status |
| `ausgeliehen` | 0/1 | Filtert nach Ausgeliehen-Status |
| `geliehen` | 0/1 | Filtert nach Geliehen-Status |
| `search` | string | LIKE-Suche in Titel, Autor, ISBN, Kategorie, Notizen |

**Antwort:** Array von Buch-Objekten, sortiert nach Kategorie und Titel.

---

#### Einzelnes Buch laden

```
GET ?action=get&id=1
```

**Antwort:** Einzelnes Buch-Objekt oder `{ "error": "Nicht gefunden" }`

---

#### Buch erstellen

```
POST ?action=create
Content-Type: application/json

{
  "titel": "Buchtitel",
  "autor": "Autorname",
  "isbn": "978-...",
  "kategorie": "Fantasy",
  "erscheinungsjahr": 2024,
  "bewertung": 4,
  "gelesen": 1,
  "ausgeliehen": 0,
  "ausgeliehen_an": "",
  "geliehen": 0,
  "geliehen_von": "",
  "notizen": "Meine Notizen"
}
```

**Antwort:** `{ "success": true, "id": 13 }`

---

#### Buch aktualisieren

```
POST ?action=update
Content-Type: application/json

{ "id": 1, "titel": "Neuer Titel", ... }
```

**Antwort:** `{ "success": true }`

---

#### Buch löschen

```
POST ?action=delete
Content-Type: application/json

{ "id": 1 }
```

**Antwort:** `{ "success": true }`

---

#### Gelesen-Status umschalten

```
POST ?action=toggle_gelesen
Content-Type: application/json

{ "id": 1 }
```

Schaltet `gelesen` zwischen 0 und 1 um.

**Antwort:** `{ "success": true }`

---

#### Kategorien laden

```
GET ?action=categories
```

**Antwort:**
```json
[
  { "kategorie": "Fantasy", "anzahl": 3 },
  { "kategorie": "Science-Fiction", "anzahl": 2 }
]
```

---

#### Statistiken laden

```
GET ?action=stats
```

**Antwort:**
```json
{
  "total": 12,
  "gelesen": 9,
  "ungelesen": 3,
  "ausgeliehen": 1,
  "geliehen": 2,
  "avg_bewertung": 4.6,
  "kategorien": 9
}
```

---

#### Export

```
GET ?action=export
```

Liefert eine JSON-Datei als Download (`buecherverwaltung_export_YYYY-MM-DD.json`).

---

#### Import

```
POST ?action=import
Content-Type: application/json

[ { "titel": "...", "autor": "...", ... }, ... ]
```

Bücher werden zur bestehenden Sammlung **hinzugefügt** (kein Überschreiben).

**Antwort:** `{ "success": true, "imported": 5 }`

---

### Frontend

#### Technologie
- **Vanilla JavaScript** (kein Framework)
- **CSS3** mit CSS Custom Properties (Dark Theme)
- **Responsive Design** mit Breakpoints bei 768px und 480px

#### Fuzzy-Suche
Die Suche verwendet den **Levenshtein-Algorithmus** zur Berechnung der String-Ähnlichkeit:
- Exakte Teilstring-Treffer erhalten 100 Punkte
- Fuzzy-Treffer (≥ 60% Ähnlichkeit) erhalten anteilig Punkte
- Alle Suchwörter müssen matchen (AND-Verknüpfung)
- Ergebnisse werden nach Relevanz sortiert
- Suche wird 300ms nach der letzten Eingabe ausgelöst (Debouncing)

#### CSS Design-System

| Variable | Wert | Verwendung |
|---|---|---|
| `--bg-primary` | `#0f0f1a` | Haupthintergrund |
| `--bg-secondary` | `#161625` | Sidebar, Navigation |
| `--bg-card` | `#1c1c30` | Karten, Hover-Effekte |
| `--accent` | `#00c8ff` | Akzentfarbe (Cyan) |
| `--purple` | `#7c3aed` | Tags, Geliehen-Badge |
| `--gold` | `#f59e0b` | Sterne, Ausgeliehen-Badge |
| `--success` | `#10b981` | Gelesen-Status, Toggle |
| `--danger` | `#ef4444` | Löschen-Buttons |

#### Responsive Breakpoints

| Breakpoint | Anpassungen |
|---|---|
| ≤ 768px | Sidebar wird ausklappbar (Hamburger-Button), Toolbar vertikal, Jahr-Spalte ausgeblendet |
| ≤ 480px | App-Titel ausgeblendet, Bewertungs-Spalte ausgeblendet |

---

### Sicherheit

| Maßnahme | Umsetzung |
|---|---|
| **SQL-Injection** | Alle Queries verwenden PDO Prepared Statements mit gebundenen Parametern |
| **XSS** | `esc()` Funktion escaped HTML-Entities im Frontend, `htmlspecialchars()` im Backend |
| **CSRF** | Kein Session-basierter Schutz (lokale Anwendung) |
| **Authentifizierung** | Keine (gedacht für lokales Netzwerk) |

**Hinweis:** Die Anwendung ist für den Einsatz im **lokalen Netzwerk** konzipiert. Für den Betrieb im Internet sollten zusätzlich Authentifizierung und CSRF-Schutz implementiert werden.
