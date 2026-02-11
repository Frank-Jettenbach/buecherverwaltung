<?php
/**
 * Bücherverwaltung - Beispieldaten
 */
function seedBuecher(PDO $pdo) {
    $buecher = [
        ['Der Herr der Ringe', 'J.R.R. Tolkien', '978-3608938289', 'Fantasy', 1954, 5, 1, 'Meisterwerk der Fantasy-Literatur'],
        ['1984', 'George Orwell', '978-3548234106', 'Science-Fiction', 1949, 5, 1, 'Dystopischer Klassiker'],
        ['Der kleine Prinz', 'Antoine de Saint-Exupéry', '978-3792000281', 'Belletristik', 1943, 4, 1, ''],
        ['Eine kurze Geschichte der Zeit', 'Stephen Hawking', '978-3499626005', 'Wissenschaft', 1988, 4, 1, 'Einführung in die Kosmologie'],
        ['Clean Code', 'Robert C. Martin', '978-0132350884', 'Technik & IT', 2008, 5, 1, 'Pflichtlektüre für Entwickler'],
        ['Der Prozess', 'Franz Kafka', '978-3596200191', 'Belletristik', 1925, 4, 1, ''],
        ['Sapiens', 'Yuval Noah Harari', '978-3421047434', 'Sachbuch', 2011, 5, 0, 'Geschichte der Menschheit'],
        ['Dune', 'Frank Herbert', '978-3453186835', 'Science-Fiction', 1965, 5, 1, 'Sci-Fi Epos'],
        ['Steve Jobs', 'Walter Isaacson', '978-3570101568', 'Biografie', 2011, 4, 0, ''],
        ['Der Name der Rose', 'Umberto Eco', '978-3423105514', 'Krimi & Thriller', 1980, 5, 1, 'Historischer Krimi im Kloster'],
        ['Thinking, Fast and Slow', 'Daniel Kahneman', '978-3328100348', 'Psychologie', 2011, 4, 0, 'Über Entscheidungsfindung'],
        ['The Pragmatic Programmer', 'David Thomas & Andrew Hunt', '978-0135957059', 'Technik & IT', 2019, 5, 1, 'Klassiker für Software-Entwickler'],
    ];

    $stmt = $pdo->prepare('INSERT INTO buecher (titel, autor, isbn, kategorie, erscheinungsjahr, bewertung, gelesen, notizen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($buecher as $b) {
        $stmt->execute($b);
    }
}
