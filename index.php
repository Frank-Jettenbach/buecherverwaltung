<?php
$config = require __DIR__ . '/config.php';
$baseUrl = $config['app']['base_url'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['app']['title']) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= $baseUrl ?>/assets/favicon.svg">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/menu.php'; ?>

    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Kategorien</h3>
            </div>
            <ul class="category-list" id="categoryList">
                <li class="category-item active" data-kategorie="">
                    <span class="cat-icon">&#128218;</span>
                    <span class="cat-name">Alle Bücher</span>
                    <span class="cat-count" id="countAll">0</span>
                </li>
            </ul>

            <!-- Statistiken -->
            <div class="sidebar-stats" id="sidebarStats">
                <div class="sidebar-header" style="margin-top: 8px;">
                    <h3>Statistiken</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-value" id="statTotal">0</span>
                        <span class="stat-label">Gesamt</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="statGelesen">0</span>
                        <span class="stat-label">Gelesen</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="statUngelesen">0</span>
                        <span class="stat-label">Ungelesen</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="statBewertung">-</span>
                        <span class="stat-label">&#216; Bewertung</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="statAusgeliehen">0</span>
                        <span class="stat-label">Ausgeliehen</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="statGeliehen">0</span>
                        <span class="stat-label">Geliehen</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <main class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="search-wrapper">
                    <span class="search-icon">&#128269;</span>
                    <input type="text" id="searchInput" class="search-input" placeholder="Bücher durchsuchen..." autocomplete="off">
                    <button class="search-clear hidden" id="searchClear" title="Suche leeren">&#10005;</button>
                </div>
                <div class="toolbar-actions">
                    <button class="btn btn-primary" id="btnNew" title="Neues Buch">&#43; Neues Buch</button>
                    <button class="btn btn-secondary" id="btnExport" title="Export als JSON">&#8681; Export</button>
                    <label class="btn btn-secondary" title="JSON importieren">
                        &#8679; Import
                        <input type="file" id="fileImport" accept=".json" hidden>
                    </label>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs" id="filterTabs">
                <button class="filter-tab active" data-filter="alle">Alle</button>
                <button class="filter-tab" data-filter="gelesen">Gelesen</button>
                <button class="filter-tab" data-filter="ungelesen">Ungelesen</button>
                <button class="filter-tab" data-filter="ausgeliehen">Ausgeliehen</button>
                <button class="filter-tab" data-filter="geliehen">Geliehen</button>
            </div>

            <!-- Active Filters -->
            <div class="active-filters hidden" id="activeFilters">
                <span class="filter-label">Filter:</span>
                <div class="filter-tags" id="filterTagsEl"></div>
                <button class="btn-link" id="clearFilters">Alle Filter entfernen</button>
            </div>

            <!-- Book Table -->
            <div class="table-container" id="tableContainer">
                <div class="loading">Bücher werden geladen...</div>
            </div>

            <!-- Empty State -->
            <div class="empty-state hidden" id="emptyState">
                <div class="empty-icon">&#128214;</div>
                <h3>Keine Bücher gefunden</h3>
                <p>Versuche andere Suchbegriffe oder füge ein neues Buch hinzu.</p>
            </div>
        </main>
    </div>

    <!-- Modal: Buch erstellen/bearbeiten -->
    <div class="modal-overlay hidden" id="modalOverlay">
        <div class="modal" id="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Neues Buch</h2>
                <button class="modal-close" id="modalClose">&#10005;</button>
            </div>
            <form id="bookForm" class="modal-body">
                <input type="hidden" id="formId">
                <div class="form-group">
                    <label for="formTitel">Titel *</label>
                    <input type="text" id="formTitel" required placeholder="Buchtitel eingeben">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="formAutor">Autor *</label>
                        <input type="text" id="formAutor" required placeholder="Autor eingeben">
                    </div>
                    <div class="form-group">
                        <label for="formISBN">ISBN</label>
                        <input type="text" id="formISBN" placeholder="z.B. 978-3608938289">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="formKategorie">Kategorie *</label>
                        <select id="formKategorie" required>
                            <option value="">— Kategorie wählen —</option>
                            <option value="Belletristik">Belletristik</option>
                            <option value="Biografie">Biografie</option>
                            <option value="Fantasy">Fantasy</option>
                            <option value="Geschichte">Geschichte</option>
                            <option value="Humor">Humor</option>
                            <option value="Kinderbuch">Kinderbuch</option>
                            <option value="Kochen">Kochen</option>
                            <option value="Krimi & Thriller">Krimi & Thriller</option>
                            <option value="Kunst & Musik">Kunst & Musik</option>
                            <option value="Lyrik">Lyrik</option>
                            <option value="Philosophie">Philosophie</option>
                            <option value="Psychologie">Psychologie</option>
                            <option value="Ratgeber">Ratgeber</option>
                            <option value="Reise">Reise</option>
                            <option value="Religion & Spiritualität">Religion & Spiritualität</option>
                            <option value="Roman">Roman</option>
                            <option value="Sachbuch">Sachbuch</option>
                            <option value="Science-Fiction">Science-Fiction</option>
                            <option value="Technik & IT">Technik & IT</option>
                            <option value="Wirtschaft">Wirtschaft</option>
                            <option value="Wissenschaft">Wissenschaft</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="formJahr">Erscheinungsjahr</label>
                        <input type="number" id="formJahr" min="1000" max="2099" placeholder="z.B. 2024">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Bewertung</label>
                        <div class="star-rating" id="starRating">
                            <span class="star" data-value="1">&#9733;</span>
                            <span class="star" data-value="2">&#9733;</span>
                            <span class="star" data-value="3">&#9733;</span>
                            <span class="star" data-value="4">&#9733;</span>
                            <span class="star" data-value="5">&#9733;</span>
                        </div>
                        <input type="hidden" id="formBewertung" value="0">
                    </div>
                    <div class="form-group">
                        <label for="formGelesen">Status</label>
                        <div class="toggle-wrapper">
                            <label class="toggle">
                                <input type="checkbox" id="formGelesen">
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label" id="toggleLabel">Ungelesen</span>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="formAusgeliehen" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            Ausgeliehen
                        </label>
                        <input type="text" id="formAusgeliehenAn" class="conditional-input hidden" placeholder="An wen ausgeliehen?">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="formGeliehen" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            Geliehen
                        </label>
                        <input type="text" id="formGeliehenVon" class="conditional-input hidden" placeholder="Von wem geliehen?">
                    </div>
                </div>
                <div class="form-group">
                    <label for="formNotizen">Notizen</label>
                    <textarea id="formNotizen" rows="3" placeholder="Eigene Notizen zum Buch..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancel">Abbrechen</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirm -->
    <div class="modal-overlay hidden" id="deleteOverlay">
        <div class="modal modal-sm">
            <div class="modal-header">
                <h2>Buch l&ouml;schen</h2>
                <button class="modal-close" id="deleteClose">&#10005;</button>
            </div>
            <div class="modal-body">
                <p>Soll das Buch <strong id="deleteName"></strong> wirklich gel&ouml;scht werden?</p>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="deleteCancel">Abbrechen</button>
                    <button class="btn btn-danger" id="deleteConfirm">L&ouml;schen</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast hidden" id="toast"></div>

    <script src="<?= $baseUrl ?>/assets/app.js"></script>
</body>
</html>
