<?php
/**
 * Bücherverwaltung - REST API
 */
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // -- Alle Bücher laden (mit optionalen Filtern) --
    case 'list':
        $where = [];
        $params = [];

        if (!empty($_GET['kategorie'])) {
            $where[] = 'kategorie = ?';
            $params[] = $_GET['kategorie'];
        }
        if (isset($_GET['gelesen']) && $_GET['gelesen'] !== '') {
            $where[] = 'gelesen = ?';
            $params[] = (int)$_GET['gelesen'];
        }
        if (isset($_GET['ausgeliehen']) && $_GET['ausgeliehen'] !== '') {
            $where[] = 'ausgeliehen = ?';
            $params[] = (int)$_GET['ausgeliehen'];
        }
        if (isset($_GET['geliehen']) && $_GET['geliehen'] !== '') {
            $where[] = 'geliehen = ?';
            $params[] = (int)$_GET['geliehen'];
        }
        if (!empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $where[] = '(titel LIKE ? OR autor LIKE ? OR isbn LIKE ? OR kategorie LIKE ? OR notizen LIKE ?)';
            $params = array_merge($params, [$search, $search, $search, $search, $search]);
        }

        $sql = 'SELECT * FROM buecher';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY kategorie, titel';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll());
        break;

    // -- Einzelnes Buch laden --
    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM buecher WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        echo json_encode($row ?: ['error' => 'Nicht gefunden']);
        break;

    // -- Buch erstellen --
    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['error' => 'Keine Daten empfangen']);
            break;
        }
        $stmt = $pdo->prepare('INSERT INTO buecher (titel, autor, isbn, kategorie, erscheinungsjahr, bewertung, gelesen, ausgeliehen, ausgeliehen_an, geliehen, geliehen_von, notizen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['titel'] ?? '',
            $data['autor'] ?? '',
            $data['isbn'] ?? '',
            $data['kategorie'] ?? '',
            !empty($data['erscheinungsjahr']) ? (int)$data['erscheinungsjahr'] : null,
            (int)($data['bewertung'] ?? 0),
            (int)($data['gelesen'] ?? 0),
            (int)($data['ausgeliehen'] ?? 0),
            $data['ausgeliehen_an'] ?? '',
            (int)($data['geliehen'] ?? 0),
            $data['geliehen_von'] ?? '',
            $data['notizen'] ?? '',
        ]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    // -- Buch aktualisieren --
    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['id'])) {
            echo json_encode(['error' => 'Keine Daten oder ID']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE buecher SET titel=?, autor=?, isbn=?, kategorie=?, erscheinungsjahr=?, bewertung=?, gelesen=?, ausgeliehen=?, ausgeliehen_an=?, geliehen=?, geliehen_von=?, notizen=? WHERE id=?');
        $stmt->execute([
            $data['titel'] ?? '',
            $data['autor'] ?? '',
            $data['isbn'] ?? '',
            $data['kategorie'] ?? '',
            !empty($data['erscheinungsjahr']) ? (int)$data['erscheinungsjahr'] : null,
            (int)($data['bewertung'] ?? 0),
            (int)($data['gelesen'] ?? 0),
            (int)($data['ausgeliehen'] ?? 0),
            $data['ausgeliehen_an'] ?? '',
            (int)($data['geliehen'] ?? 0),
            $data['geliehen_von'] ?? '',
            $data['notizen'] ?? '',
            (int)$data['id'],
        ]);
        echo json_encode(['success' => true]);
        break;

    // -- Buch löschen --
    case 'delete':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);
        if (!$id) {
            echo json_encode(['error' => 'Keine ID']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM buecher WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    // -- Gelesen-Status umschalten --
    case 'toggle_gelesen':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);
        if (!$id) {
            echo json_encode(['error' => 'Keine ID']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE buecher SET gelesen = NOT gelesen WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    // -- Kategorien mit Anzahl laden --
    case 'categories':
        $stmt = $pdo->query("
            SELECT kategorie, COUNT(*) as anzahl
            FROM buecher
            WHERE kategorie != ''
            GROUP BY kategorie
            ORDER BY kategorie
        ");
        echo json_encode($stmt->fetchAll());
        break;

    // -- Statistiken --
    case 'stats':
        $total = $pdo->query("SELECT COUNT(*) FROM buecher")->fetchColumn();
        $gelesen = $pdo->query("SELECT COUNT(*) FROM buecher WHERE gelesen = 1")->fetchColumn();
        $ungelesen = $total - $gelesen;
        $avgBewertung = $pdo->query("SELECT ROUND(AVG(bewertung), 1) FROM buecher WHERE bewertung > 0")->fetchColumn();
        $kategorien = $pdo->query("SELECT COUNT(DISTINCT kategorie) FROM buecher WHERE kategorie != ''")->fetchColumn();
        $ausgeliehen = $pdo->query("SELECT COUNT(*) FROM buecher WHERE ausgeliehen = 1")->fetchColumn();
        $geliehen = $pdo->query("SELECT COUNT(*) FROM buecher WHERE geliehen = 1")->fetchColumn();
        echo json_encode([
            'total' => (int)$total,
            'gelesen' => (int)$gelesen,
            'ungelesen' => (int)$ungelesen,
            'ausgeliehen' => (int)$ausgeliehen,
            'geliehen' => (int)$geliehen,
            'avg_bewertung' => $avgBewertung ?: '0',
            'kategorien' => (int)$kategorien,
        ]);
        break;

    // -- Export als JSON --
    case 'export':
        $stmt = $pdo->query('SELECT titel, autor, isbn, kategorie, erscheinungsjahr, bewertung, gelesen, ausgeliehen, ausgeliehen_an, geliehen, geliehen_von, notizen FROM buecher ORDER BY kategorie, titel');
        $data = $stmt->fetchAll();
        header('Content-Disposition: attachment; filename="buecherverwaltung_export_' . date('Y-m-d') . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    // -- Import von JSON --
    case 'import':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !is_array($data)) {
            echo json_encode(['error' => 'Ungültige JSON-Daten']);
            break;
        }

        $stmt = $pdo->prepare('INSERT INTO buecher (titel, autor, isbn, kategorie, erscheinungsjahr, bewertung, gelesen, ausgeliehen, ausgeliehen_an, geliehen, geliehen_von, notizen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $imported = 0;
        foreach ($data as $b) {
            if (empty($b['titel'])) continue;
            $stmt->execute([
                $b['titel'] ?? '',
                $b['autor'] ?? '',
                $b['isbn'] ?? '',
                $b['kategorie'] ?? '',
                !empty($b['erscheinungsjahr']) ? (int)$b['erscheinungsjahr'] : null,
                (int)($b['bewertung'] ?? 0),
                (int)($b['gelesen'] ?? 0),
                (int)($b['ausgeliehen'] ?? 0),
                $b['ausgeliehen_an'] ?? '',
                (int)($b['geliehen'] ?? 0),
                $b['geliehen_von'] ?? '',
                $b['notizen'] ?? '',
            ]);
            $imported++;
        }
        echo json_encode(['success' => true, 'imported' => $imported]);
        break;

    default:
        echo json_encode(['error' => 'Unbekannte Aktion']);
}
