/**
 * Bücherverwaltung - Frontend JavaScript
 */
(() => {
    'use strict';

    const API = 'api.php';
    let allBooks = [];
    let allBooksCache = [];
    let categories = [];
    let activeKategorie = '';
    let activeFilter = 'alle'; // alle, gelesen, ungelesen, ausgeliehen, geliehen
    let deleteId = null;

    // -- DOM Elements --
    const $ = id => document.getElementById(id);
    const tableContainer = $('tableContainer');
    const categoryList = $('categoryList');
    const searchInput = $('searchInput');
    const searchClear = $('searchClear');
    const emptyState = $('emptyState');
    const activeFilters = $('activeFilters');
    const filterTagsEl = $('filterTagsEl');
    const modalOverlay = $('modalOverlay');
    const deleteOverlay = $('deleteOverlay');
    const toast = $('toast');

    // -- Category Icons --
    const catIcons = {
        'Belletristik': '\u{1F4D6}',
        'Biografie': '\u{1F464}',
        'Fantasy': '\u{1F9D9}',
        'Geschichte': '\u{1F3DB}',
        'Humor': '\u{1F602}',
        'Kinderbuch': '\u{1F476}',
        'Kochen': '\u{1F373}',
        'Krimi & Thriller': '\u{1F575}',
        'Kunst & Musik': '\u{1F3A8}',
        'Lyrik': '\u{1F338}',
        'Philosophie': '\u{1F4AD}',
        'Psychologie': '\u{1F9E0}',
        'Ratgeber': '\u{1F4A1}',
        'Reise': '\u{2708}',
        'Religion & Spiritualität': '\u{1F54E}',
        'Roman': '\u{1F4D5}',
        'Sachbuch': '\u{1F4DA}',
        'Science-Fiction': '\u{1F680}',
        'Technik & IT': '\u{1F4BB}',
        'Wirtschaft': '\u{1F4C8}',
        'Wissenschaft': '\u{1F52C}',
    };

    // -- Levenshtein Distance --
    function levenshtein(a, b) {
        const an = a.length, bn = b.length;
        if (an === 0) return bn;
        if (bn === 0) return an;
        const matrix = [];
        for (let i = 0; i <= bn; i++) matrix[i] = [i];
        for (let j = 0; j <= an; j++) matrix[0][j] = j;
        for (let i = 1; i <= bn; i++) {
            for (let j = 1; j <= an; j++) {
                if (b[i - 1] === a[j - 1]) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j - 1] + 1,
                        matrix[i][j - 1] + 1,
                        matrix[i - 1][j] + 1
                    );
                }
            }
        }
        return matrix[bn][an];
    }

    // -- Fuzzy Match --
    function fuzzyMatch(query, books) {
        const words = query.toLowerCase().split(/\s+/).filter(Boolean);
        if (words.length === 0) return books;

        const scored = [];
        for (const book of books) {
            const searchable = [book.titel, book.autor, book.isbn, book.kategorie, book.notizen].join(' ').toLowerCase();
            let totalScore = 0;
            let allMatch = true;

            for (const word of words) {
                if (searchable.includes(word)) {
                    totalScore += 100;
                    continue;
                }
                const textWords = searchable.split(/[\s\-_,./]+/).filter(w => w.length >= 2);
                let bestSim = 0;
                for (const tw of textWords) {
                    const dist = levenshtein(word, tw);
                    const maxLen = Math.max(word.length, tw.length);
                    const sim = 1 - dist / maxLen;
                    if (sim > bestSim) bestSim = sim;
                }
                if (bestSim >= 0.6) {
                    totalScore += Math.round(bestSim * 80);
                } else {
                    allMatch = false;
                    break;
                }
            }

            if (allMatch) {
                scored.push({ book, score: totalScore });
            }
        }

        scored.sort((a, b) => b.score - a.score);
        return scored.map(s => s.book);
    }

    // -- API Calls --
    async function api(action, params = {}, body = null) {
        const url = new URL(API, window.location.href);
        url.searchParams.set('action', action);
        for (const [k, v] of Object.entries(params)) {
            if (v !== undefined && v !== null && v !== '') url.searchParams.set(k, v);
        }
        const opts = {};
        if (body) {
            opts.method = 'POST';
            opts.headers = { 'Content-Type': 'application/json' };
            opts.body = JSON.stringify(body);
        }
        const res = await fetch(url, opts);
        return res.json();
    }

    // -- Load Data --
    async function loadAllBooksCache() {
        allBooksCache = await api('list');
    }

    async function loadBooks() {
        const search = searchInput.value.trim();

        if (search) {
            if (allBooksCache.length === 0) {
                await loadAllBooksCache();
            }
            let filtered = allBooksCache;
            if (activeKategorie) {
                filtered = filtered.filter(b => b.kategorie === activeKategorie);
            }
            if (activeFilter === 'gelesen') filtered = filtered.filter(b => b.gelesen == 1);
            if (activeFilter === 'ungelesen') filtered = filtered.filter(b => b.gelesen == 0);
            if (activeFilter === 'ausgeliehen') filtered = filtered.filter(b => b.ausgeliehen == 1);
            if (activeFilter === 'geliehen') filtered = filtered.filter(b => b.geliehen == 1);
            allBooks = fuzzyMatch(search, filtered);
        } else {
            const params = {};
            if (activeKategorie) params.kategorie = activeKategorie;
            if (activeFilter === 'gelesen') params.gelesen = '1';
            if (activeFilter === 'ungelesen') params.gelesen = '0';
            if (activeFilter === 'ausgeliehen') params.ausgeliehen = '1';
            if (activeFilter === 'geliehen') params.geliehen = '1';
            allBooks = await api('list', params);
        }

        renderTable();
        updateFilterDisplay();
    }

    async function loadCategories() {
        categories = await api('categories');
        renderSidebar();
    }

    async function loadStats() {
        const stats = await api('stats');
        $('statTotal').textContent = stats.total;
        $('statGelesen').textContent = stats.gelesen;
        $('statUngelesen').textContent = stats.ungelesen;
        $('statBewertung').textContent = stats.avg_bewertung > 0 ? stats.avg_bewertung : '-';
        $('statAusgeliehen').textContent = stats.ausgeliehen;
        $('statGeliehen').textContent = stats.geliehen;
    }

    // -- Render Sidebar --
    function renderSidebar() {
        const totalCount = allBooks.length;

        let html = `
            <li class="category-item ${!activeKategorie ? 'active' : ''}" data-kategorie="">
                <span class="cat-icon">&#128218;</span>
                <span class="cat-name">Alle Bücher</span>
                <span class="cat-count" id="countAll">${totalCount}</span>
            </li>`;

        for (const cat of categories) {
            const isActive = activeKategorie === cat.kategorie;
            const icon = catIcons[cat.kategorie] || '\u{1F4D3}';

            html += `
                <li class="category-item ${isActive ? 'active' : ''}" data-kategorie="${esc(cat.kategorie)}">
                    <span class="cat-icon">${icon}</span>
                    <span class="cat-name">${esc(cat.kategorie)}</span>
                    <span class="cat-count">${cat.anzahl}</span>
                </li>`;
        }

        categoryList.innerHTML = html;

        // Click handlers
        categoryList.querySelectorAll('.category-item').forEach(el => {
            el.addEventListener('click', () => {
                activeKategorie = el.dataset.kategorie;
                loadBooks();
                loadCategories();
            });
        });
    }

    // -- Render Stars --
    function renderStars(rating) {
        let html = '<span class="stars">';
        for (let i = 1; i <= 5; i++) {
            html += `<span class="${i <= rating ? 'filled' : ''}">&#9733;</span>`;
        }
        html += '</span>';
        return html;
    }

    // -- Render Table --
    function renderTable() {
        if (allBooks.length === 0) {
            tableContainer.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }
        emptyState.classList.add('hidden');

        // Group by category
        const grouped = {};
        for (const book of allBooks) {
            const key = book.kategorie || 'Ohne Kategorie';
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(book);
        }

        let html = '<table class="book-table"><thead><tr>';
        html += '<th style="width:30%">Titel</th><th style="width:22%">Autor</th><th style="width:8%">Jahr</th><th style="width:14%">Bewertung</th><th style="width:10%">Status</th><th style="width:16%; text-align:right">Aktionen</th>';
        html += '</tr></thead><tbody>';

        for (const [kategorie, books] of Object.entries(grouped)) {
            const icon = catIcons[kategorie] || '\u{1F4D3}';
            html += `<tr class="group-row"><td colspan="6">
                <span class="group-icon">${icon}</span>
                ${esc(kategorie)}
                <span class="group-count">${books.length} ${books.length === 1 ? 'Buch' : 'Bücher'}</span>
            </td></tr>`;

            for (const book of books) {
                html += renderRow(book);
            }
        }

        html += '</tbody></table>';
        tableContainer.innerHTML = html;

        // Event: Edit buttons
        tableContainer.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => openEditModal(parseInt(btn.dataset.id)));
        });

        // Event: Delete buttons
        tableContainer.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => openDeleteConfirm(parseInt(btn.dataset.id), btn.dataset.title));
        });

        // Event: Status badge click (toggle gelesen)
        tableContainer.querySelectorAll('.status-badge').forEach(el => {
            el.addEventListener('click', () => toggleGelesen(parseInt(el.dataset.id)));
        });

        const countAll = $('countAll');
        if (countAll) countAll.textContent = allBooks.length;
    }

    function renderRow(book) {
        const statusClass = book.gelesen == 1 ? 'gelesen' : 'ungelesen';
        const statusText = book.gelesen == 1 ? 'Gelesen' : 'Ungelesen';
        const statusIcon = book.gelesen == 1 ? '&#10003;' : '&#9711;';

        let badges = `<span class="status-badge ${statusClass}" data-id="${book.id}" title="Klicken zum Umschalten">${statusIcon} ${statusText}</span>`;
        if (book.ausgeliehen == 1) {
            const an = book.ausgeliehen_an ? ` an ${esc(book.ausgeliehen_an)}` : '';
            badges += `<span class="status-badge ausgeliehen" title="Ausgeliehen${escAttr(an)}">&#8599; Ausg.${an}</span>`;
        }
        if (book.geliehen == 1) {
            const von = book.geliehen_von ? ` von ${esc(book.geliehen_von)}` : '';
            badges += `<span class="status-badge geliehen" title="Geliehen${escAttr(von)}">&#8601; Gel.${von}</span>`;
        }

        return `<tr class="book-row" data-id="${book.id}">
            <td class="title-cell" title="${escAttr(book.titel)}">${esc(book.titel)}</td>
            <td class="autor-cell" title="${escAttr(book.autor)}">${esc(book.autor)}</td>
            <td class="year-cell">${book.erscheinungsjahr || '—'}</td>
            <td class="rating-cell">${book.bewertung > 0 ? renderStars(book.bewertung) : '<span class="stars"><span>—</span></span>'}</td>
            <td class="status-cell">${badges}</td>
            <td class="actions-cell">
                <button class="btn-icon edit-btn" data-id="${book.id}" title="Bearbeiten">&#9998;</button>
                <button class="btn-icon delete-btn" data-id="${book.id}" data-title="${escAttr(book.titel)}" title="L\u00f6schen">&#128465;</button>
            </td>
        </tr>`;
    }

    // -- Toggle Gelesen --
    async function toggleGelesen(id) {
        const result = await api('toggle_gelesen', {}, { id });
        if (result.success) {
            allBooksCache = [];
            loadBooks();
            loadCategories();
            loadStats();
        }
    }

    // -- Filter Display --
    function updateFilterDisplay() {
        const filters = [];
        if (activeKategorie) filters.push(activeKategorie);
        if (activeFilter !== 'alle') {
            const filterLabels = { gelesen: 'Gelesen', ungelesen: 'Ungelesen', ausgeliehen: 'Ausgeliehen', geliehen: 'Geliehen' };
            filters.push(filterLabels[activeFilter] || activeFilter);
        }
        if (searchInput.value.trim()) filters.push(`Suche: "${searchInput.value.trim()}"`);

        if (filters.length > 0) {
            activeFilters.classList.remove('hidden');
            filterTagsEl.innerHTML = filters.map(f => `<span class="filter-tag">${esc(f)}</span>`).join('');
        } else {
            activeFilters.classList.add('hidden');
        }

        searchClear.classList.toggle('hidden', !searchInput.value);
    }

    // -- Modal --
    function openModal(title = 'Neues Buch', data = null) {
        $('modalTitle').textContent = title;
        $('formId').value = data?.id || '';
        $('formTitel').value = data?.titel || '';
        $('formAutor').value = data?.autor || '';
        $('formISBN').value = data?.isbn || '';
        $('formKategorie').value = data?.kategorie || '';
        $('formJahr').value = data?.erscheinungsjahr || '';
        $('formNotizen').value = data?.notizen || '';

        // Set rating
        const rating = data?.bewertung || 0;
        $('formBewertung').value = rating;
        updateStarDisplay(rating);

        // Set gelesen toggle
        const gelesen = data?.gelesen == 1;
        $('formGelesen').checked = gelesen;
        $('toggleLabel').textContent = gelesen ? 'Gelesen' : 'Ungelesen';

        // Set ausgeliehen/geliehen checkboxes
        const ausgeliehen = data?.ausgeliehen == 1;
        $('formAusgeliehen').checked = ausgeliehen;
        $('formAusgeliehenAn').value = data?.ausgeliehen_an || '';
        toggleConditionalInput($('formAusgeliehen'), $('formAusgeliehenAn'));

        const geliehen2 = data?.geliehen == 1;
        $('formGeliehen').checked = geliehen2;
        $('formGeliehenVon').value = data?.geliehen_von || '';
        toggleConditionalInput($('formGeliehen'), $('formGeliehenVon'));

        modalOverlay.classList.remove('hidden');
        $('formTitel').focus();
    }

    function closeModal() {
        modalOverlay.classList.add('hidden');
        $('bookForm').reset();
        $('formBewertung').value = 0;
        updateStarDisplay(0);
        $('toggleLabel').textContent = 'Ungelesen';
        $('formAusgeliehenAn').classList.add('hidden');
        $('formGeliehenVon').classList.add('hidden');
    }

    // -- Conditional Input Toggle --
    function toggleConditionalInput(checkbox, input) {
        if (checkbox.checked) {
            input.classList.remove('hidden');
        } else {
            input.classList.add('hidden');
            input.value = '';
        }
    }

    async function openEditModal(id) {
        const book = await api('get', { id });
        if (book.error) return showToast(book.error, 'error');
        openModal('Buch bearbeiten', book);
    }

    // -- Star Rating --
    function updateStarDisplay(rating) {
        $('starRating').querySelectorAll('.star').forEach(star => {
            star.classList.toggle('active', parseInt(star.dataset.value) <= rating);
        });
    }

    $('starRating').addEventListener('click', (e) => {
        const star = e.target.closest('.star');
        if (!star) return;
        const val = parseInt(star.dataset.value);
        const current = parseInt($('formBewertung').value);
        const newVal = val === current ? 0 : val;
        $('formBewertung').value = newVal;
        updateStarDisplay(newVal);
    });

    // -- Toggle Label --
    $('formGelesen').addEventListener('change', () => {
        $('toggleLabel').textContent = $('formGelesen').checked ? 'Gelesen' : 'Ungelesen';
    });

    // -- Ausgeliehen/Geliehen Checkbox Toggle --
    $('formAusgeliehen').addEventListener('change', () => {
        toggleConditionalInput($('formAusgeliehen'), $('formAusgeliehenAn'));
    });
    $('formGeliehen').addEventListener('change', () => {
        toggleConditionalInput($('formGeliehen'), $('formGeliehenVon'));
    });

    // -- Delete --
    function openDeleteConfirm(id, title) {
        deleteId = id;
        $('deleteName').textContent = title;
        deleteOverlay.classList.remove('hidden');
    }

    function closeDelete() {
        deleteOverlay.classList.add('hidden');
        deleteId = null;
    }

    // -- Toast --
    let toastTimer = null;
    function showToast(msg, type = 'info') {
        toast.textContent = msg;
        toast.className = `toast ${type}`;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    // -- Export --
    function exportData() {
        window.location.href = `${API}?action=export`;
    }

    // -- Import --
    async function importData(file) {
        try {
            const text = await file.text();
            const data = JSON.parse(text);
            if (!Array.isArray(data)) throw new Error('JSON muss ein Array sein');

            const result = await api('import', {}, data);
            if (result.error) throw new Error(result.error);

            showToast(`${result.imported} Bücher importiert`, 'success');
            allBooksCache = [];
            loadBooks();
            loadCategories();
            loadStats();
        } catch (e) {
            showToast('Import fehlgeschlagen: ' + e.message, 'error');
        }
    }

    // -- Helpers --
    function esc(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function escAttr(str) {
        return (str || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // -- Event Listeners --
    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadBooks, 300);
    });

    searchClear.addEventListener('click', () => {
        searchInput.value = '';
        loadBooks();
    });

    $('btnNew').addEventListener('click', () => openModal());
    $('btnExport').addEventListener('click', exportData);
    $('fileImport').addEventListener('change', (e) => {
        if (e.target.files[0]) importData(e.target.files[0]);
        e.target.value = '';
    });

    $('modalClose').addEventListener('click', closeModal);
    $('btnCancel').addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });

    $('deleteClose').addEventListener('click', closeDelete);
    $('deleteCancel').addEventListener('click', closeDelete);
    deleteOverlay.addEventListener('click', (e) => { if (e.target === deleteOverlay) closeDelete(); });

    $('deleteConfirm').addEventListener('click', async () => {
        if (!deleteId) return;
        const result = await api('delete', {}, { id: deleteId });
        if (result.success) {
            showToast('Buch gelöscht', 'success');
            closeDelete();
            allBooksCache = [];
            loadBooks();
            loadCategories();
            loadStats();
        } else {
            showToast('Fehler beim Löschen', 'error');
        }
    });

    $('clearFilters').addEventListener('click', () => {
        activeKategorie = '';
        activeFilter = 'alle';
        searchInput.value = '';
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.toggle('active', t.dataset.filter === 'alle'));
        loadBooks();
        loadCategories();
    });

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            activeFilter = tab.dataset.filter;
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.toggle('active', t === tab));
            loadBooks();
        });
    });

    // Form submit
    $('bookForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            titel: $('formTitel').value.trim(),
            autor: $('formAutor').value.trim(),
            isbn: $('formISBN').value.trim(),
            kategorie: $('formKategorie').value,
            erscheinungsjahr: $('formJahr').value ? parseInt($('formJahr').value) : null,
            bewertung: parseInt($('formBewertung').value) || 0,
            gelesen: $('formGelesen').checked ? 1 : 0,
            ausgeliehen: $('formAusgeliehen').checked ? 1 : 0,
            ausgeliehen_an: $('formAusgeliehenAn').value.trim(),
            geliehen: $('formGeliehen').checked ? 1 : 0,
            geliehen_von: $('formGeliehenVon').value.trim(),
            notizen: $('formNotizen').value.trim(),
        };

        const id = $('formId').value;
        let result;

        if (id) {
            data.id = parseInt(id);
            result = await api('update', {}, data);
        } else {
            result = await api('create', {}, data);
        }

        if (result.success) {
            showToast(id ? 'Buch aktualisiert' : 'Buch hinzugefügt', 'success');
            closeModal();
            allBooksCache = [];
            loadBooks();
            loadCategories();
            loadStats();
        } else {
            showToast(result.error || 'Fehler beim Speichern', 'error');
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!modalOverlay.classList.contains('hidden')) closeModal();
            if (!deleteOverlay.classList.contains('hidden')) closeDelete();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            openModal();
        }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.createElement('button');
    sidebarToggle.className = 'sidebar-toggle';
    sidebarToggle.innerHTML = '&#9776;';
    sidebarToggle.addEventListener('click', () => {
        $('sidebar').classList.toggle('open');
    });
    document.body.appendChild(sidebarToggle);

    document.addEventListener('click', (e) => {
        const sidebar = $('sidebar');
        if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== sidebarToggle) {
            sidebar.classList.remove('open');
        }
    });

    // -- Init --
    async function init() {
        await loadAllBooksCache();
        await loadBooks();
        await loadCategories();
        await loadStats();
    }

    init();
})();
