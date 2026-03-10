// =====================================================
// CONFIGURATION & CONSTANTS
// =====================================================

const CONFIG = {
    API: {
        ADD_BOOKMARK:    '/api/bookmarks/add',
        FETCH_METADATA:  '/api/bookmarks/fetch-metadata',
        TAGS:            '/api/bookmarks/tags'
    },
    TAGS: {
        READ_LATER:             'To Read',
        MIN_AUTOCOMPLETE_LEN:   2,
        MAX_SUGGESTIONS:        5,
        CACHE_KEY:              'bm_tags_cache',
        CACHE_TTL_MS:           5 * 60 * 1000  // 5 minutes
    },
    TIMEOUTS: {
        MESSAGE_MS:  5000,
        DEBOUNCE_MS: 300
    },
    DB: {
        NAME:       'BookmarksOfflineDB',
        VERSION:    1,
        STORE:      'pendingBookmarks'
    }
};

// =====================================================
// APPLICATION STATE
// =====================================================

const AppState = {
    credentials:            null,
    tags:                   [],
    selectedSuggestionIdx:  0,
    db:                     null,
    el:                     {}
};

// =====================================================
// UTILITIES
// =====================================================

function debounce(fn, wait) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
}

function showMessage(text, type = 'info') {
    const el = AppState.el.message;
    if (!el) return;
    el.textContent = text;
    el.className = 'message ' + type;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, CONFIG.TIMEOUTS.MESSAGE_MS);
}

function extractDomain(url) {
    try { return new URL(url).hostname.replace(/^www\./, ''); }
    catch { return ''; }
}

function generateSlug(url) {
    try {
        const { pathname, hostname } = new URL(url);
        if (pathname && pathname !== '/') {
            const last = pathname.split('/').filter(Boolean).pop();
            const clean = last?.replace(/\.(html?|php|aspx?)$/i, '');
            if (clean) return clean;
        }
        const parts = hostname.replace(/^www\./, '').split('.');
        parts.pop();
        return parts.join('-');
    } catch { return ''; }
}

// =====================================================
// OFFLINE SUPPORT (IndexedDB) — lazy init
// =====================================================

async function getDB() {
    if (AppState.db) return AppState.db;

    return new Promise((resolve, reject) => {
        const req = indexedDB.open(CONFIG.DB.NAME, CONFIG.DB.VERSION);
        req.onerror = () => reject(req.error);
        req.onsuccess = () => { AppState.db = req.result; resolve(AppState.db); };
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(CONFIG.DB.STORE)) {
                db.createObjectStore(CONFIG.DB.STORE, { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

async function saveToOfflineQueue(data) {
    const db = await getDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction([CONFIG.DB.STORE], 'readwrite');
        const req = tx.objectStore(CONFIG.DB.STORE).add({ ...data, timestamp: Date.now(), synced: false });
        req.onsuccess = () => resolve(req.result);
        req.onerror  = () => reject(req.error);
    });
}

async function getPendingBookmarks() {
    const db = await getDB();
    return new Promise((resolve, reject) => {
        const req = db.transaction([CONFIG.DB.STORE], 'readonly').objectStore(CONFIG.DB.STORE).getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror  = () => reject(req.error);
    });
}

async function removeFromQueue(id) {
    const db = await getDB();
    return new Promise((resolve, reject) => {
        const req = db.transaction([CONFIG.DB.STORE], 'readwrite').objectStore(CONFIG.DB.STORE).delete(id);
        req.onsuccess = () => resolve();
        req.onerror  = () => reject(req.error);
    });
}

async function syncOfflineBookmarks() {
    if (!navigator.onLine) return;

    const pending = await getPendingBookmarks();
    if (!pending.length) return;

    const auth = await getAuthCredentials();
    if (!auth) return;

    // Compute auth header once, outside the loop
    const authHeader = 'Basic ' + btoa(auth.email + ':' + auth.password);

    let synced = 0;
    for (const bookmark of pending) {
        try {
            const res  = await fetch(CONFIG.API.ADD_BOOKMARK, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': authHeader },
                body:    JSON.stringify(bookmark)
            });
            const json = await res.json();
            if (json.status === 'success') { await removeFromQueue(bookmark.id); synced++; }
        } catch { /* individual failure — continue syncing others */ }
    }

    if (synced) showMessage(`✅ ${synced} offline bookmark(s) synced!`, 'success');
}

// =====================================================
// TAG MANAGEMENT
// =====================================================

function normalizeTags(input) {
    if (!input) return '';
    return input
        .split(',')
        .map(t => t.trim())
        .filter(Boolean)
        .map(tag => AppState.tags.find(t => t.toLowerCase() === tag.toLowerCase()) || tag)
        .join(', ');
}

async function loadExistingTags() {
    // Return cached tags if still fresh
    try {
        const raw = sessionStorage.getItem(CONFIG.TAGS.CACHE_KEY);
        if (raw) {
            const { tags, ts } = JSON.parse(raw);
            if (Date.now() - ts < CONFIG.TAGS.CACHE_TTL_MS) {
                AppState.tags = tags;
                return;
            }
        }
    } catch { /* corrupted cache — fetch fresh */ }

    try {
        const res  = await fetch(CONFIG.API.TAGS);
        const json = await res.json();
        if (json.status === 'success' && json.data) {
            AppState.tags = json.data;
            sessionStorage.setItem(CONFIG.TAGS.CACHE_KEY, JSON.stringify({ tags: json.data, ts: Date.now() }));
        }
    } catch { /* non-fatal — autocomplete just won't work */ }
}

function getTagSuggestions(input) {
    if (input.length < CONFIG.TAGS.MIN_AUTOCOMPLETE_LEN) return [];
    const lower = input.toLowerCase();
    return AppState.tags.filter(t => t.toLowerCase().includes(lower)).slice(0, CONFIG.TAGS.MAX_SUGGESTIONS);
}

// Diff-based suggestion renderer — reuses existing DOM nodes where possible
function renderTagSuggestions(input, suggestions) {
    const container = AppState.el.tagSuggestions;
    if (!suggestions.length) { container.classList.remove('active'); return; }

    const existing = container.querySelectorAll('.tag-suggestion');
    const escaped  = input.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex    = new RegExp(`(${escaped})`, 'gi');

    // Reuse or create nodes
    suggestions.forEach((tag, i) => {
        let div = existing[i];
        if (!div) {
            div = document.createElement('div');
            div.className = 'tag-suggestion';
            div.addEventListener('click', () => { insertTag(tag); container.classList.remove('active'); });
            container.appendChild(div);
        }
        div.classList.toggle('selected', i === 0);
        div.innerHTML = tag.replace(regex, '<mark>$1</mark>');
        // Update click handler to current tag value
        div.onclick = () => { insertTag(tag); container.classList.remove('active'); };
    });

    // Remove leftover nodes
    for (let i = suggestions.length; i < existing.length; i++) existing[i].remove();

    container.classList.add('active');
    AppState.selectedSuggestionIdx = 0;
}

function insertTag(tag) {
    const el   = AppState.el.tags;
    const tags = el.value.trim() ? el.value.split(',').map(t => t.trim()) : [];
    tags.pop();
    tags.push(tag);
    el.value = tags.join(', ') + ', ';
    el.focus();
}

function updateSuggestionSelection(suggestions, idx) {
    suggestions.forEach((s, i) => s.classList.toggle('selected', i === idx));
}

const handleTagInput = debounce((e) => {
    const tags       = e.target.value.split(',');
    const current    = tags[tags.length - 1].trim();
    const container  = AppState.el.tagSuggestions;

    if (current.length >= CONFIG.TAGS.MIN_AUTOCOMPLETE_LEN) {
        renderTagSuggestions(current, getTagSuggestions(current));
    } else {
        container.classList.remove('active');
    }
}, CONFIG.TIMEOUTS.DEBOUNCE_MS);

// =====================================================
// METADATA FETCHING
// =====================================================

async function fetchMetadata(url) {
    if (!url) return;
    showMessage('Fetching metadata...', 'info');

    try {
        const res  = await fetch(CONFIG.API.FETCH_METADATA, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ url })
        });
        const { status, data } = await res.json();

        if (status !== 'success' || !data) { showMessage('Could not fetch metadata', 'info'); return; }

        const { el } = AppState;
        const updated = [];

        if (data.author && el.author && !el.author.value) { el.author.value = data.author; updated.push('author'); }
        if (data.tags   && el.tags   && !el.tags.value)   { el.tags.value   = data.tags;   updated.push('tags');   }
        if (data.title  && el.title  && !el.title.value)  { el.title.value  = data.title;  updated.push('title');  }

        showMessage(
            updated.length ? `Metadata fetched! Updated: ${updated.join(', ')}` : 'Metadata fetched (no empty fields to fill)',
            'success'
        );
    } catch (err) {
        showMessage('Error fetching metadata: ' + err.message, 'error');
    }
}

// =====================================================
// AUTHENTICATION
// =====================================================

async function getAuthCredentials() {
    const userEmail = document.body.dataset.userEmail;

    if (userEmail) {
        if (AppState.credentials?.email === userEmail) return AppState.credentials;
        const password = prompt(`Enter your Kirby password for: ${userEmail}`);
        if (!password) return null;
        AppState.credentials = { email: userEmail, password };
        return AppState.credentials;
    }

    if (AppState.credentials) return AppState.credentials;

    const email    = prompt('Enter your Kirby email:');
    if (!email) return null;
    const password = prompt('Enter your Kirby password:');
    if (!password) return null;

    AppState.credentials = { email, password };
    return AppState.credentials;
}

// =====================================================
// ONLINE/OFFLINE
// =====================================================

function updateOnlineStatus() {
    const offline = AppState.el.offlineIndicator;
    if (navigator.onLine) {
        offline?.classList.remove('show');
        syncOfflineBookmarks();
    } else {
        offline?.classList.add('show');
    }
}

// =====================================================
// FORM SUBMISSION
// =====================================================

async function handleFormSubmit(e) {
    e.preventDefault();

    const { el } = AppState;
    const bookmarkData = {
        website: el.website?.value  || '',
        title:   el.title?.value    || '',
        tld:     el.tld?.value      || '',
        slug:    el.slug?.value     || '',
        author:  el.author?.value   || '',
        tags:    normalizeTags(el.tags?.value || ''),
        text:    el.text?.value     || ''
    };

    if (!navigator.onLine) {
        try {
            await saveToOfflineQueue(bookmarkData);
            showMessage('📡 Saved offline! Will sync when back online.', 'success');
            el.form?.reset();
        } catch (err) {
            showMessage('Error saving offline: ' + err.message, 'error');
        }
        return;
    }

    try {
        // Attempt session auth first
        if (document.body.dataset.userEmail) {
            const res  = await fetch(CONFIG.API.ADD_BOOKMARK, {
                method:      'POST',
                headers:     { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body:        JSON.stringify(bookmarkData)
            });
            const json = await res.json();
            if (json.status === 'success') {
                showMessage('Bookmark saved!', 'success');
                setTimeout(() => { window.location.href = '/links'; }, 1500);
                return;
            }
        }

        // Fall back to Basic auth
        const auth = await getAuthCredentials();
        if (!auth) { showMessage('Authentication required', 'error'); return; }

        const res  = await fetch(CONFIG.API.ADD_BOOKMARK, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Authorization': 'Basic ' + btoa(auth.email + ':' + auth.password)
            },
            body: JSON.stringify(bookmarkData)
        });
        const json = await res.json();

        if (json.status === 'success') {
            showMessage('Bookmark saved!', 'success');
            setTimeout(() => { window.location.href = '/links'; }, 1500);
        } else if (json.message?.includes('authentication')) {
            AppState.credentials = null;
            showMessage('Authentication failed. Please try again.', 'error');
        } else {
            showMessage(json.message || 'Error saving bookmark', 'error');
        }
    } catch (err) {
        showMessage('Network error: ' + err.message, 'error');
    }
}

// =====================================================
// QUICK SAVE
// =====================================================

async function handleQuickSave() {
    const { el } = AppState;
    const url = el.website?.value.trim();
    if (!url) { showMessage('URL is required', 'error'); return; }

    if (el.title && !el.title.value)  el.title.value = 'Read Later';
    if (el.tld   && !el.tld.value)    el.tld.value   = extractDomain(url);
    if (el.slug  && !el.slug.value)   el.slug.value  = generateSlug(url);

    if (el.tags) {
        const current = el.tags.value.trim();
        if (current) {
            const hasTag = current.split(',').some(t => t.trim().toLowerCase() === CONFIG.TAGS.READ_LATER.toLowerCase());
            if (!hasTag) el.tags.value = CONFIG.TAGS.READ_LATER + ', ' + current;
        } else {
            el.tags.value = CONFIG.TAGS.READ_LATER;
        }
    }

    el.form?.dispatchEvent(new Event('submit'));
}

// =====================================================
// EVENT LISTENERS
// =====================================================

function setupEventListeners() {
    const { el } = AppState;

    el.website?.addEventListener('blur', () => {
        const url = el.website.value.trim();
        if (!url) return;
        if (el.tld  && !el.tld.value)  el.tld.value  = extractDomain(url);
        if (el.slug && !el.slug.value) el.slug.value  = generateSlug(url);
    });

    if (el.tags && el.tagSuggestions) {
        el.tags.addEventListener('input', handleTagInput);

        el.tags.addEventListener('keydown', (e) => {
            const container   = el.tagSuggestions;
            const suggestions = container.querySelectorAll('.tag-suggestion');
            if (!container.classList.contains('active')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                AppState.selectedSuggestionIdx = (AppState.selectedSuggestionIdx + 1) % suggestions.length;
                updateSuggestionSelection(suggestions, AppState.selectedSuggestionIdx);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                AppState.selectedSuggestionIdx = (AppState.selectedSuggestionIdx - 1 + suggestions.length) % suggestions.length;
                updateSuggestionSelection(suggestions, AppState.selectedSuggestionIdx);
            } else if (e.key === 'Enter' && suggestions.length) {
                e.preventDefault();
                suggestions[AppState.selectedSuggestionIdx].click();
            } else if (e.key === 'Escape') {
                container.classList.remove('active');
            }
        });

        document.addEventListener('click', (e) => {
            if (!el.tags.contains(e.target) && !el.tagSuggestions.contains(e.target)) {
                el.tagSuggestions.classList.remove('active');
            }
        });
    }

    el.fetchMetadataBtn?.addEventListener('click', () => {
        const url = el.website?.value.trim();
        url ? fetchMetadata(url) : showMessage('Please enter a URL first', 'error');
    });

    el.quickSaveBtn?.addEventListener('click', handleQuickSave);
    el.form?.addEventListener('submit', handleFormSubmit);

    window.addEventListener('online',  updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        const mod = e.ctrlKey || e.metaKey;
        if (!mod) return;

        if (e.key === 's' && !e.shiftKey) {
            e.preventDefault();
            AppState.el.form?.dispatchEvent(new Event('submit'));
        } else if (e.key === 'S' && e.shiftKey) {
            e.preventDefault();
            handleQuickSave();
        } else if (e.key === 'm') {
            e.preventDefault();
            const url = AppState.el.website?.value.trim();
            url ? fetchMetadata(url) : showMessage('Please enter a URL first', 'error');
        } else if (e.key === 'Escape') {
            AppState.el.tagSuggestions?.classList.remove('active');
        }
    });
}

// =====================================================
// INITIALIZATION
// =====================================================

window.addEventListener('DOMContentLoaded', async () => {
    // Cache DOM references
    AppState.el = {
        website:         document.getElementById('website'),
        tld:             document.getElementById('tld'),
        slug:            document.getElementById('slug'),
        author:          document.getElementById('author'),
        tags:            document.getElementById('tags'),
        title:           document.getElementById('page-title'),
        text:            document.getElementById('text'),
        form:            document.getElementById('bookmark-form'),
        fetchMetadataBtn:document.getElementById('fetch-metadata-btn'),
        quickSaveBtn:    document.getElementById('quick-save-btn'),
        message:         document.getElementById('message'),
        offlineIndicator:document.getElementById('offline-indicator'),
        tagSuggestions:  document.getElementById('tag-suggestions')
    };

    // Parallelize independent async init tasks
    await Promise.all([
        loadExistingTags()
        // IndexedDB is now lazy — no longer initialized here
    ]);

    setupEventListeners();
    setupKeyboardShortcuts();
    updateOnlineStatus();

    // Auto-fill from URL params — no arbitrary timeout needed,
    // the service worker handles the POST→GET redirect before this runs
    const prefilled = AppState.el.website?.value;
    if (prefilled) {
        AppState.el.website.dispatchEvent(new Event('blur'));
        fetchMetadata(prefilled);
    }
});