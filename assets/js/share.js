// =====================================================
// CONFIGURATION & CONSTANTS
// =====================================================

const CONFIG = {
    API_ENDPOINTS: {
        ADD_BOOKMARK: '/api/bookmarks/add',
        FETCH_METADATA: '/api/bookmarks/fetch-metadata',
        TAGS: '/api/bookmarks/tags'
    },
    TAGS: {
        READ_LATER: 'To Read',
        MIN_AUTOCOMPLETE_LENGTH: 2,
        MAX_SUGGESTIONS: 5
    },
    TIMEOUTS: {
        MESSAGE_DISPLAY: 5000,
        AUTO_FETCH_DELAY: 300,
        DEBOUNCE_DELAY: 300
    },
    DB: {
        NAME: 'BookmarksOfflineDB',
        VERSION: 1,
        STORE_NAME: 'pendingBookmarks'
    }
};

// =====================================================
// APPLICATION STATE
// =====================================================

const AppState = {
    credentials: null,
    tags: [],
    selectedSuggestionIndex: 0,
    isOnline: navigator.onLine,
    db: null,
    elements: {} // Will store cached DOM references
};

// =====================================================
// UTILITY FUNCTIONS
// =====================================================

// Debounce function to limit execution frequency
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

// Show message to user
function showMessage(text, type = "info") {
    if (!AppState.elements.messageDiv) {
        console.error("Message element not found");
        return;
    }
    AppState.elements.messageDiv.textContent = text;
    AppState.elements.messageDiv.className = "message " + type;
    AppState.elements.messageDiv.style.display = "block";

    setTimeout(() => {
        AppState.elements.messageDiv.style.display = "none";
    }, CONFIG.TIMEOUTS.MESSAGE_DISPLAY);
}

// Extract domain from URL
function extractDomain(url) {
    try {
        const hostname = new URL(url).hostname;
        return hostname.replace(/^www\./, "");
    } catch (err) {
        console.error("Error extracting domain:", err);
        return "";
    }
}

// Generate slug from URL
function generateSlug(url) {
    try {
        const urlObj = new URL(url);
        const path = urlObj.pathname;

        if (path && path !== '/') {
            const pathSegments = path.split('/').filter(s => s);
            let lastSegment = pathSegments[pathSegments.length - 1];
            lastSegment = lastSegment.replace(/\.(html|htm|php|asp|aspx)$/i, '');

            if (lastSegment) {
                return lastSegment;
            }
        }

        const hostname = urlObj.hostname.replace(/^www\./, '');
        const parts = hostname.split('.');
        parts.pop();
        return parts.join('-');

    } catch (err) {
        console.error("Error generating slug:", err);
        return "";
    }
}

// =====================================================
// OFFLINE SUPPORT (IndexedDB)
// =====================================================

async function initDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(CONFIG.DB.NAME, CONFIG.DB.VERSION);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            AppState.db = request.result;
            resolve(AppState.db);
        };

        request.onupgradeneeded = (e) => {
            const database = e.target.result;
            if (!database.objectStoreNames.contains(CONFIG.DB.STORE_NAME)) {
                database.createObjectStore(CONFIG.DB.STORE_NAME, {
                    keyPath: 'id',
                    autoIncrement: true
                });
            }
        };
    });
}

async function saveToOfflineQueue(bookmarkData) {
    if (!AppState.db) await initDB();

    return new Promise((resolve, reject) => {
        const transaction = AppState.db.transaction([CONFIG.DB.STORE_NAME], 'readwrite');
        const store = transaction.objectStore(CONFIG.DB.STORE_NAME);

        const data = {
            ...bookmarkData,
            timestamp: Date.now(),
            synced: false
        };

        const request = store.add(data);

        request.onsuccess = () => {
            console.log('📦 Saved to offline queue:', data);
            resolve(request.result);
        };
        request.onerror = () => reject(request.error);
    });
}

async function getPendingBookmarks() {
    if (!AppState.db) await initDB();

    return new Promise((resolve, reject) => {
        const transaction = AppState.db.transaction([CONFIG.DB.STORE_NAME], 'readonly');
        const store = transaction.objectStore(CONFIG.DB.STORE_NAME);
        const request = store.getAll();

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function removeFromQueue(id) {
    if (!AppState.db) await initDB();

    return new Promise((resolve, reject) => {
        const transaction = AppState.db.transaction([CONFIG.DB.STORE_NAME], 'readwrite');
        const store = transaction.objectStore(CONFIG.DB.STORE_NAME);
        const request = store.delete(id);

        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

async function syncOfflineBookmarks() {
    if (!navigator.onLine) {
        console.log('📡 Still offline, sync postponed');
        return;
    }

    const pending = await getPendingBookmarks();

    if (pending.length === 0) {
        console.log('✅ No pending bookmarks to sync');
        return;
    }

    console.log(`📤 Syncing ${pending.length} offline bookmarks...`);

    for (const bookmark of pending) {
        try {
            const auth = await getAuthCredentials();
            if (!auth) continue;

            const response = await fetch(CONFIG.API_ENDPOINTS.ADD_BOOKMARK, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Basic ' + btoa(auth.email + ':' + auth.password)
                },
                body: JSON.stringify(bookmark)
            });

            const result = await response.json();

            if (result.status === 'success') {
                await removeFromQueue(bookmark.id);
                console.log('✅ Synced:', bookmark.title);
            } else {
                console.error('❌ Sync failed:', result.message);
            }
        } catch (err) {
            console.error('❌ Sync error:', err);
        }
    }

    showMessage('✅ Offline bookmarks synced!', 'success');
}

// =====================================================
// TAG MANAGEMENT & NORMALIZATION
// =====================================================

// Normalize tags against existing tags (case-insensitive)
function normalizeTags(inputTags) {
    if (!inputTags) return '';

    const tags = inputTags.split(',').map(t => t.trim()).filter(t => t);
    const normalized = tags.map(tag => {
        // Find matching tag in AppState.tags (case-insensitive)
        const existing = AppState.tags.find(t => t.toLowerCase() === tag.toLowerCase());
        return existing || tag; // Use existing capitalization or keep original
    });

    return normalized.join(', ');
}

// Load existing tags from the site
async function loadExistingTags() {
    try {
        const response = await fetch(CONFIG.API_ENDPOINTS.TAGS);
        const result = await response.json();

        if (result.status === 'success' && result.data) {
            AppState.tags = result.data;
            console.log('📋 Loaded', AppState.tags.length, 'existing tags');
        }
    } catch (err) {
        console.error('Failed to load tags:', err);
    }
}

// Get tag suggestions based on input
function getTagSuggestions(input) {
    if (!input || input.length < CONFIG.TAGS.MIN_AUTOCOMPLETE_LENGTH) {
        return [];
    }

    const lowerInput = input.toLowerCase();

    return AppState.tags
        .filter(tag => tag.toLowerCase().includes(lowerInput))
        .slice(0, CONFIG.TAGS.MAX_SUGGESTIONS);
}

// Show tag suggestions
function showTagSuggestions(input, suggestions) {
    const container = AppState.elements.tagSuggestionsContainer;

    if (suggestions.length === 0) {
        container.classList.remove('active');
        return;
    }

    container.innerHTML = '';
    container.classList.add('active');

    suggestions.forEach((tag, index) => {
        const div = document.createElement('div');
        div.className = 'tag-suggestion';
        if (index === 0) div.classList.add('selected');

        // Highlight matching text (safely)
        const regex = new RegExp(`(${input.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        div.innerHTML = tag.replace(regex, '<mark>$1</mark>');

        div.addEventListener('click', () => {
            insertTag(tag);
            container.classList.remove('active');
        });

        container.appendChild(div);
    });
}

// Insert tag into input
function insertTag(tag) {
    const tagsInput = AppState.elements.tagsInput;
    const currentValue = tagsInput.value.trim();

    let tags = currentValue ? currentValue.split(',').map(t => t.trim()) : [];

    // Remove the incomplete tag (last one)
    tags.pop();

    // Add the selected tag (using exact capitalization from AppState.tags)
    tags.push(tag);

    // Update input
    tagsInput.value = tags.join(', ') + ', ';
    tagsInput.focus();
}

// Update selection in suggestions
function updateSelection(suggestions, index) {
    suggestions.forEach((s, i) => {
        s.classList.toggle('selected', i === index);
    });
}

// Debounced tag input handler
const handleTagInput = debounce((e) => {
    const value = e.target.value;

    // Get the last tag being typed
    const tags = value.split(',');
    const currentTag = tags[tags.length - 1].trim();

    if (currentTag.length >= CONFIG.TAGS.MIN_AUTOCOMPLETE_LENGTH) {
        const suggestions = getTagSuggestions(currentTag);
        showTagSuggestions(currentTag, suggestions);
        AppState.selectedSuggestionIndex = 0;
    } else {
        AppState.elements.tagSuggestionsContainer.classList.remove('active');
    }
}, CONFIG.TIMEOUTS.DEBOUNCE_DELAY);

// =====================================================
// METADATA FETCHING
// =====================================================

async function fetchMetadata(url) {
    if (!url) return;

    showMessage("Fetching metadata...", "info");
    console.log("📡 Fetching metadata for:", url);

    try {
        const response = await fetch(CONFIG.API_ENDPOINTS.FETCH_METADATA, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ url: url })
        });

        const result = await response.json();
        console.log("Metadata response:", result);

        if (result.status === "success" && result.data) {
            const data = result.data;
            let updated = [];

            // Update author (only if empty)
            if (data.author && AppState.elements.authorInput && !AppState.elements.authorInput.value) {
                AppState.elements.authorInput.value = data.author;
                updated.push("author");
                console.log("✅ Author set:", data.author);
            }

            // Update tags (only if empty)
            if (data.tags && AppState.elements.tagsInput && !AppState.elements.tagsInput.value) {
                AppState.elements.tagsInput.value = data.tags;
                updated.push("tags");
                console.log("✅ Tags set:", data.tags);
            }

            // Update title (only if empty)
            if (data.title && AppState.elements.titleInput && !AppState.elements.titleInput.value) {
                AppState.elements.titleInput.value = data.title;
                updated.push("title");
                console.log("✅ Title set:", data.title);
            }

            if (updated.length > 0) {
                showMessage(
                    `Metadata fetched! Updated: ${updated.join(", ")}`,
                    "success"
                );
            } else {
                showMessage("Metadata fetched (no empty fields to fill)", "info");
            }
        } else {
            showMessage("Could not fetch metadata", "info");
            console.log("Metadata fetch returned no data");
        }
    } catch (err) {
        console.error("Metadata fetch error:", err);
        showMessage("Error fetching metadata: " + err.message, "error");
    }
}

// =====================================================
// AUTHENTICATION
// =====================================================

async function getAuthCredentials() {
    const userEmail = document.body.dataset.userEmail;

    if (userEmail) {
        console.log("✅ User logged in via session:", userEmail);

        if (AppState.credentials && AppState.credentials.email === userEmail) {
            console.log("Using cached credentials");
            return AppState.credentials;
        }

        const password = prompt(
            `Enter your Kirby password for: ${userEmail}\n\n(This is your panel login password)`
        );

        if (!password) {
            return null;
        }

        AppState.credentials = { email: userEmail, password: password };
        return AppState.credentials;
    }

    console.log("⚠️ No session found - full login required");

    if (AppState.credentials) {
        console.log("Using cached credentials from this session");
        return AppState.credentials;
    }

    const email = prompt("Enter your Kirby email:\n(Your panel login email)");
    if (!email) return null;

    const password = prompt("Enter your Kirby password:\n(Your panel login password)");
    if (!password) return null;

    AppState.credentials = { email: email, password: password };
    console.log("Stored credentials for this session:", email);

    return AppState.credentials;
}

// =====================================================
// ONLINE/OFFLINE STATUS
// =====================================================

function updateOnlineStatus() {
    if (navigator.onLine) {
        AppState.elements.offlineIndicator.classList.remove('show');
        syncOfflineBookmarks();
    } else {
        AppState.elements.offlineIndicator.classList.add('show');
    }
    AppState.isOnline = navigator.onLine;
}

// =====================================================
// FORM SUBMISSION
// =====================================================

async function handleFormSubmit(e) {
    e.preventDefault();
    console.log("Form submitted");

    const userEmail = document.body.dataset.userEmail;

    // Normalize tags before submission
    const normalizedTags = normalizeTags(
        AppState.elements.tagsInput ? AppState.elements.tagsInput.value : ""
    );

    const bookmarkData = {
        website: AppState.elements.websiteInput ? AppState.elements.websiteInput.value : "",
        title: AppState.elements.titleInput ? AppState.elements.titleInput.value : "",
        tld: AppState.elements.tldInput ? AppState.elements.tldInput.value : "",
        slug: AppState.elements.slugInput ? AppState.elements.slugInput.value : "",
        author: AppState.elements.authorInput ? AppState.elements.authorInput.value : "",
        tags: normalizedTags,
        text: AppState.elements.textInput ? AppState.elements.textInput.value : ""
    };

    console.log("Submitting bookmark:", bookmarkData);

    // Handle offline submission
    if (!navigator.onLine) {
        console.log("📡 Offline - saving to queue");
        try {
            await saveToOfflineQueue(bookmarkData);
            showMessage("📡 Saved offline! Will sync when you're back online.", "success");
            AppState.elements.form.reset();
            return;
        } catch (err) {
            showMessage("Error saving offline: " + err.message, "error");
            return;
        }
    }

    // Handle online submission
    try {
        let response;

        // Try session auth first if logged in
        if (userEmail) {
            console.log("Attempting save with session auth...");
            response = await fetch(CONFIG.API_ENDPOINTS.ADD_BOOKMARK, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "same-origin",
                body: JSON.stringify(bookmarkData)
            });

            const result = await response.json();

            if (result.status === "success") {
                console.log("✅ Saved with session auth");
                showMessage("Bookmark saved successfully!", "success");
                setTimeout(() => {
                    window.location.href = "/links";
                }, 1500);
                return;
            }

            console.log("Session auth failed, trying Basic auth...");
        }

        // Get credentials for Basic auth
        const auth = await getAuthCredentials();
        if (!auth || !auth.password) {
            showMessage("Authentication required", "error");
            return;
        }

        response = await fetch(CONFIG.API_ENDPOINTS.ADD_BOOKMARK, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Basic " + btoa(auth.email + ":" + auth.password)
            },
            body: JSON.stringify(bookmarkData)
        });

        const result = await response.json();
        console.log("Save response:", result);

        if (result.status === "success") {
            showMessage("Bookmark saved successfully!", "success");
            setTimeout(() => {
                window.location.href = "/links";
            }, 1500);
        } else if (result.message && result.message.includes("authentication")) {
            AppState.credentials = null;
            showMessage("Authentication failed. Please try again.", "error");
        } else {
            showMessage(result.message || "Error saving bookmark", "error");
        }
    } catch (err) {
        console.error("Network error:", err);
        showMessage("Network error: " + err.message, "error");
    }
}

// =====================================================
// QUICK SAVE
// =====================================================

async function handleQuickSave() {
    console.log("Quick save button clicked");

    const url = AppState.elements.websiteInput ? AppState.elements.websiteInput.value.trim() : "";

    if (!url) {
        showMessage("URL is required", "error");
        return;
    }

    // Auto-fill title if empty
    if (AppState.elements.titleInput && !AppState.elements.titleInput.value) {
        AppState.elements.titleInput.value = "Read Later";
    }

    // Auto-fill domain if empty
    if (AppState.elements.tldInput && !AppState.elements.tldInput.value) {
        const domain = extractDomain(url);
        if (domain) {
            AppState.elements.tldInput.value = domain;
        }
    }

    // Auto-generate slug if empty
    if (AppState.elements.slugInput && !AppState.elements.slugInput.value) {
        const slug = generateSlug(url);
        if (slug) {
            AppState.elements.slugInput.value = slug;
        }
    }

    // Add or prepend "Read-Later" tag
    if (AppState.elements.tagsInput) {
        const currentTags = AppState.elements.tagsInput.value.trim();
        if (currentTags) {
            // Prepend Read-Later to existing tags if not already present
            const tags = currentTags.split(',').map(t => t.trim());
            const hasReadLater = tags.some(t => t.toLowerCase() === CONFIG.TAGS.READ_LATER.toLowerCase());

            if (!hasReadLater) {
                AppState.elements.tagsInput.value = CONFIG.TAGS.READ_LATER + ', ' + currentTags;
            }
        } else {
            AppState.elements.tagsInput.value = CONFIG.TAGS.READ_LATER;
        }
    }

    console.log("Quick saving - submitting form");
    AppState.elements.form.dispatchEvent(new Event('submit'));
}

// =====================================================
// KEYBOARD SHORTCUTS
// =====================================================

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + S: Save bookmark
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            console.log('⌨️ Keyboard shortcut: Save (Ctrl+S)');
            if (AppState.elements.form) {
                AppState.elements.form.dispatchEvent(new Event('submit'));
            }
        }

        // Ctrl/Cmd + Shift + S: Quick Save
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'S') {
            e.preventDefault();
            console.log('⌨️ Keyboard shortcut: Quick Save (Ctrl+Shift+S)');
            if (AppState.elements.quickSaveBtn) {
                handleQuickSave();
            }
        }

        // Ctrl/Cmd + M: Fetch Metadata
        if ((e.ctrlKey || e.metaKey) && e.key === 'm') {
            e.preventDefault();
            console.log('⌨️ Keyboard shortcut: Fetch Metadata (Ctrl+M)');
            const url = AppState.elements.websiteInput ? AppState.elements.websiteInput.value.trim() : "";
            if (url) {
                fetchMetadata(url);
            } else {
                showMessage("Please enter a URL first", "error");
            }
        }

        // Escape: Clear tag suggestions
        if (e.key === 'Escape' && AppState.elements.tagSuggestionsContainer) {
            AppState.elements.tagSuggestionsContainer.classList.remove('active');
        }
    });

    console.log('⌨️ Keyboard shortcuts enabled:');
    console.log('  • Ctrl/Cmd + S: Save bookmark');
    console.log('  • Ctrl/Cmd + Shift + S: Quick Save');
    console.log('  • Ctrl/Cmd + M: Fetch Metadata');
    console.log('  • Escape: Close tag suggestions');
}

// =====================================================
// EVENT LISTENERS SETUP
// =====================================================

function setupEventListeners() {
    const {
        websiteInput,
        tagsInput,
        fetchMetadataBtn,
        quickSaveBtn,
        form,
        tagSuggestionsContainer
    } = AppState.elements;

    // URL blur: auto-extract domain and slug
    if (websiteInput) {
        websiteInput.addEventListener("blur", () => {
            const url = websiteInput.value.trim();
            if (!url) return;

            const domain = extractDomain(url);
            if (domain && AppState.elements.tldInput && !AppState.elements.tldInput.value) {
                AppState.elements.tldInput.value = domain;
                console.log("Domain extracted:", domain);
            }

            const slug = generateSlug(url);
            if (slug && AppState.elements.slugInput && !AppState.elements.slugInput.value) {
                AppState.elements.slugInput.value = slug;
                console.log("Slug generated:", slug);
            }
        });
    }

    // Tag autocomplete with debouncing
    if (tagsInput && tagSuggestionsContainer) {
        tagsInput.addEventListener('input', handleTagInput);

        tagsInput.addEventListener('keydown', (e) => {
            const suggestions = tagSuggestionsContainer.querySelectorAll('.tag-suggestion');

            if (!tagSuggestionsContainer.classList.contains('active')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                AppState.selectedSuggestionIndex = (AppState.selectedSuggestionIndex + 1) % suggestions.length;
                updateSelection(suggestions, AppState.selectedSuggestionIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                AppState.selectedSuggestionIndex = (AppState.selectedSuggestionIndex - 1 + suggestions.length) % suggestions.length;
                updateSelection(suggestions, AppState.selectedSuggestionIndex);
            } else if (e.key === 'Enter' && suggestions.length > 0) {
                e.preventDefault();
                suggestions[AppState.selectedSuggestionIndex].click();
            } else if (e.key === 'Escape') {
                tagSuggestionsContainer.classList.remove('active');
            }
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!tagsInput.contains(e.target) && !tagSuggestionsContainer.contains(e.target)) {
                tagSuggestionsContainer.classList.remove('active');
            }
        });
    }

    // Fetch metadata button
    if (fetchMetadataBtn) {
        fetchMetadataBtn.addEventListener("click", () => {
            console.log("Fetch metadata button clicked");
            const url = websiteInput ? websiteInput.value.trim() : "";
            if (url) {
                fetchMetadata(url);
            } else {
                showMessage("Please enter a URL first", "error");
            }
        });
    }

    // Quick save button
    if (quickSaveBtn) {
        quickSaveBtn.addEventListener("click", handleQuickSave);
    }

    // Form submission
    if (form) {
        form.addEventListener("submit", handleFormSubmit);
    }

    // Online/offline status
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    console.log("✅ All event listeners attached successfully");
}

// =====================================================
// INITIALIZATION
// =====================================================

window.addEventListener("DOMContentLoaded", async () => {
    console.log("✅ share.js loaded");

    // Cache DOM element references
    AppState.elements = {
        websiteInput: document.getElementById("website"),
        tldInput: document.getElementById("tld"),
        slugInput: document.getElementById("slug"),
        authorInput: document.getElementById("author"),
        tagsInput: document.getElementById("tags"),
        titleInput: document.getElementById("page-title"),
        textInput: document.getElementById("text"),
        form: document.getElementById("bookmark-form"),
        fetchMetadataBtn: document.getElementById("fetch-metadata-btn"),
        quickSaveBtn: document.getElementById("quick-save-btn"),
        messageDiv: document.getElementById("message"),
        offlineIndicator: document.getElementById("offline-indicator"),
        tagSuggestionsContainer: document.getElementById("tag-suggestions")
    };

    console.log("Element check:", {
        websiteInput: !!AppState.elements.websiteInput,
        tldInput: !!AppState.elements.tldInput,
        slugInput: !!AppState.elements.slugInput,
        authorInput: !!AppState.elements.authorInput,
        tagsInput: !!AppState.elements.tagsInput,
        titleInput: !!AppState.elements.titleInput,
        textInput: !!AppState.elements.textInput,
        form: !!AppState.elements.form,
        fetchBtn: !!AppState.elements.fetchMetadataBtn,
        quickSaveBtn: !!AppState.elements.quickSaveBtn,
        messageDiv: !!AppState.elements.messageDiv,
        tagSuggestions: !!AppState.elements.tagSuggestionsContainer
    });

    // Test indicator (debug mode)
    const jsTest = document.getElementById("js-test");
    if (jsTest) {
        jsTest.textContent = "✅ JavaScript loaded and working!";
        jsTest.style.background = "#d4edda";
        jsTest.style.color = "#155724";
    }

    // Initialize IndexedDB
    await initDB();

    // Load existing tags for autocomplete
    await loadExistingTags();

    // Setup all event listeners
    setupEventListeners();

    // Setup keyboard shortcuts
    setupKeyboardShortcuts();

    // Check initial online status
    updateOnlineStatus();

    // Auto-fetch metadata if URL is prefilled
    setTimeout(() => {
        if (AppState.elements.websiteInput && AppState.elements.websiteInput.value) {
            console.log("🌐 Prefilled URL detected:", AppState.elements.websiteInput.value);

            // Trigger blur to extract domain and slug
            AppState.elements.websiteInput.dispatchEvent(new Event("blur"));

            // Auto-fetch metadata
            console.log("🔄 Auto-fetching metadata for shared URL...");
            fetchMetadata(AppState.elements.websiteInput.value);
        } else {
            console.log("ℹ️ No prefilled URL found.");
        }
    }, CONFIG.TIMEOUTS.AUTO_FETCH_DELAY);

    console.log("🎉 Application initialized successfully");
});
