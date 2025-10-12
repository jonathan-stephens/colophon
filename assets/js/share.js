// =====================================================
// OFFLINE SUPPORT SETUP
// =====================================================

const DB_NAME = 'BookmarksOfflineDB';
const DB_VERSION = 1;
const STORE_NAME = 'pendingBookmarks';

let db;

// Initialize IndexedDB
async function initDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            db = request.result;
            resolve(db);
        };

        request.onupgradeneeded = (e) => {
            const database = e.target.result;
            if (!database.objectStoreNames.contains(STORE_NAME)) {
                database.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

// Save bookmark to offline queue
async function saveToOfflineQueue(bookmarkData) {
    if (!db) await initDB();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction([STORE_NAME], 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        
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

// Get all pending bookmarks
async function getPendingBookmarks() {
    if (!db) await initDB();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction([STORE_NAME], 'readonly');
        const store = transaction.objectStore(STORE_NAME);
        const request = store.getAll();

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

// Remove bookmark from queue
async function removeFromQueue(id) {
    if (!db) await initDB();

    return new Promise((resolve, reject) => {
        const transaction = db.transaction([STORE_NAME], 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        const request = store.delete(id);

        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

// Sync offline bookmarks when back online
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

            const response = await fetch('/api/bookmarks/add', {
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
// TAG AUTOCOMPLETE
// =====================================================

let allTags = [];

// Fetch all existing tags from the site
async function loadExistingTags() {
    try {
        const response = await fetch('/api/bookmarks/tags');
        const result = await response.json();
        
        if (result.status === 'success' && result.data) {
            allTags = result.data;
            console.log('📋 Loaded', allTags.length, 'existing tags');
        }
    } catch (err) {
        console.error('Failed to load tags:', err);
        // Continue without autocomplete
    }
}

// Show tag suggestions
function showTagSuggestions(input, suggestions) {
    const container = document.getElementById('tag-suggestions');
    
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
        
        // Highlight matching text
        const regex = new RegExp(`(${input})`, 'gi');
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
    const tagsInput = document.getElementById('tags');
    const currentValue = tagsInput.value.trim();
    
    // Get the current tags
    let tags = currentValue ? currentValue.split(',').map(t => t.trim()) : [];
    
    // Remove the incomplete tag (last one)
    tags.pop();
    
    // Add the selected tag
    tags.push(tag);
    
    // Update input
    tagsInput.value = tags.join(', ') + ', ';
    tagsInput.focus();
}

// Get tag suggestions based on input
function getTagSuggestions(input) {
    if (!input || input.length < 2) return [];
    
    const lowerInput = input.toLowerCase();
    
    return allTags
        .filter(tag => tag.toLowerCase().includes(lowerInput))
        .slice(0, 5); // Limit to 5 suggestions
}

// =====================================================
// MAIN SCRIPT
// =====================================================

window.addEventListener("DOMContentLoaded", async () => {
    console.log("✅ share.js loaded");

    // Initialize IndexedDB
    await initDB();

    // Load existing tags for autocomplete
    await loadExistingTags();

    // Get all form elements
    const websiteInput = document.getElementById("website");
    const tldInput = document.getElementById("tld");
    const slugInput = document.getElementById("slug");
    const authorInput = document.getElementById("author");
    const tagsInput = document.getElementById("tags");
    const titleInput = document.getElementById("page-title");
    const textInput = document.getElementById("text");
    const form = document.getElementById("bookmark-form");
    const fetchMetadataBtn = document.getElementById("fetch-metadata-btn");
    const quickSaveBtn = document.getElementById("quick-save-btn");
    const messageDiv = document.getElementById("message");
    const offlineIndicator = document.getElementById("offline-indicator");

    console.log("Element check:", {
        websiteInput: !!websiteInput,
        tldInput: !!tldInput,
        slugInput: !!slugInput,
        authorInput: !!authorInput,
        tagsInput: !!tagsInput,
        titleInput: !!titleInput,
        textInput: !!textInput,
        form: !!form,
        fetchBtn: !!fetchMetadataBtn,
        quickSaveBtn: !!quickSaveBtn,
        messageDiv: !!messageDiv
    });

    // Test indicator (only in debug mode)
    const jsTest = document.getElementById("js-test");
    if (jsTest) {
        jsTest.textContent = "✅ JavaScript loaded and working!";
        jsTest.style.background = "#d4edda";
        jsTest.style.color = "#155724";
    }

    // In-memory storage for credentials (session-only)
    let cachedCredentials = null;

    // =====================================================
    // OFFLINE DETECTION
    // =====================================================

    function updateOnlineStatus() {
        if (navigator.onLine) {
            offlineIndicator.classList.remove('show');
            // Try to sync when back online
            syncOfflineBookmarks();
        } else {
            offlineIndicator.classList.add('show');
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    // Check initial status
    updateOnlineStatus();

    // =====================================================
    // TAG AUTOCOMPLETE HANDLERS
    // =====================================================

    if (tagsInput) {
        let selectedIndex = 0;
        const suggestionsContainer = document.getElementById('tag-suggestions');

        tagsInput.addEventListener('input', (e) => {
            const value = e.target.value;
            
            // Get the last tag being typed
            const tags = value.split(',');
            const currentTag = tags[tags.length - 1].trim();
            
            if (currentTag.length >= 2) {
                const suggestions = getTagSuggestions(currentTag);
                showTagSuggestions(currentTag, suggestions);
                selectedIndex = 0;
            } else {
                suggestionsContainer.classList.remove('active');
            }
        });

        tagsInput.addEventListener('keydown', (e) => {
            const suggestions = suggestionsContainer.querySelectorAll('.tag-suggestion');
            
            if (!suggestionsContainer.classList.contains('active')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % suggestions.length;
                updateSelection(suggestions, selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + suggestions.length) % suggestions.length;
                updateSelection(suggestions, selectedIndex);
            } else if (e.key === 'Enter' && suggestions.length > 0) {
                e.preventDefault();
                suggestions[selectedIndex].click();
            } else if (e.key === 'Escape') {
                suggestionsContainer.classList.remove('active');
            }
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!tagsInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.classList.remove('active');
            }
        });
    }

    function updateSelection(suggestions, index) {
        suggestions.forEach((s, i) => {
            s.classList.toggle('selected', i === index);
        });
    }

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================

    // Show message to user
    function showMessage(text, type = "info") {
        if (!messageDiv) {
            console.error("Message element not found");
            return;
        }
        messageDiv.textContent = text;
        messageDiv.className = "message " + type;
        messageDiv.style.display = "block";

        setTimeout(() => {
            messageDiv.style.display = "none";
        }, 5000);
    }

    // Make showMessage global for offline sync
    window.showMessage = showMessage;

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

            // If path exists and is not just root
            if (path && path !== '/') {
                // Get the last segment of the path
                const pathSegments = path.split('/').filter(s => s);
                let lastSegment = pathSegments[pathSegments.length - 1];

                // Remove file extensions
                lastSegment = lastSegment.replace(/\.(html|htm|php|asp|aspx)$/i, '');

                if (lastSegment) {
                    return lastSegment;
                }
            }

            // Fallback: use domain name (without TLD)
            const hostname = urlObj.hostname.replace(/^www\./, '');
            const parts = hostname.split('.');
            parts.pop(); // Remove TLD
            return parts.join('-');

        } catch (err) {
            console.error("Error generating slug:", err);
            return "";
        }
    }

    // Fetch metadata from URL
    async function fetchMetadata(url) {
        if (!url) return;

        showMessage("Fetching metadata...", "info");
        console.log("📡 Fetching metadata for:", url);

        try {
            const response = await fetch("/api/bookmarks/fetch-metadata", {
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
                if (data.author && authorInput && !authorInput.value) {
                    authorInput.value = data.author;
                    updated.push("author");
                    console.log("✅ Author set:", data.author);
                }

                // Update tags (only if empty)
                if (data.tags && tagsInput && !tagsInput.value) {
                    tagsInput.value = data.tags;
                    updated.push("tags");
                    console.log("✅ Tags set:", data.tags);
                }

                // Update title (only if empty)
                if (data.title && titleInput && !titleInput.value) {
                    titleInput.value = data.title;
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

    // Auto-extract domain and slug when URL changes
    if (websiteInput) {
        websiteInput.addEventListener("blur", () => {
            const url = websiteInput.value.trim();
            if (!url) return;

            // Auto-fill domain
            const domain = extractDomain(url);
            if (domain && tldInput && !tldInput.value) {
                tldInput.value = domain;
                console.log("Domain extracted:", domain);
            }

            // Auto-generate slug
            const slug = generateSlug(url);
            if (slug && slugInput && !slugInput.value) {
                slugInput.value = slug;
                console.log("Slug generated:", slug);
            }
        });
    }

    // Manual fetch metadata button
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

    // Auto-fetch metadata on page load if URL is prefilled
    setTimeout(() => {
        if (websiteInput && websiteInput.value) {
            console.log("🌐 Prefilled URL detected:", websiteInput.value);

            // Trigger blur to extract domain and slug
            websiteInput.dispatchEvent(new Event("blur"));

            // Auto-fetch metadata
            console.log("🔄 Auto-fetching metadata for shared URL...");
            fetchMetadata(websiteInput.value);
        } else {
            console.log("ℹ️ No prefilled URL found.");
        }
    }, 300);

    // Get authentication credentials
    async function getAuthCredentials() {
        // Check if user is already logged in via session
        const userEmail = document.body.dataset.userEmail;

        if (userEmail) {
            console.log("✅ User logged in via session:", userEmail);
            
            // If we have cached password from this session, use it
            if (cachedCredentials && cachedCredentials.email === userEmail) {
                console.log("Using cached credentials");
                return cachedCredentials;
            }

            // Otherwise prompt for password (one-time per session)
            const password = prompt(
                `Enter your Kirby password for: ${userEmail}\n\n(This is your panel login password)`
            );
            
            if (!password) {
                return null;
            }

            cachedCredentials = { email: userEmail, password: password };
            return cachedCredentials;
        }

        // No session - need full login
        console.log("⚠️ No session found - full login required");

        // If we have cached credentials from this page session, use them
        if (cachedCredentials) {
            console.log("Using cached credentials from this session");
            return cachedCredentials;
        }

        const email = prompt("Enter your Kirby email:\n(Your panel login email)");
        if (!email) return null;

        const password = prompt("Enter your Kirby password:\n(Your panel login password)");
        if (!password) return null;

        cachedCredentials = { email: email, password: password };
        console.log("Stored credentials for this session:", email);
        
        return cachedCredentials;
    }

    // Make getAuthCredentials global for offline sync
    window.getAuthCredentials = getAuthCredentials;

    // Form submission handler
    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            console.log("Form submitted");

            // Check if user is logged in
            const userEmail = document.body.dataset.userEmail;

            // Prepare bookmark data
            const bookmarkData = {
                website: websiteInput ? websiteInput.value : "",
                title: titleInput ? titleInput.value : "",
                tld: tldInput ? tldInput.value : "",
                slug: slugInput ? slugInput.value : "",
                author: authorInput ? authorInput.value : "",
                tags: tagsInput ? tagsInput.value : "",
                text: textInput ? textInput.value : ""
            };

            console.log("Submitting bookmark:", bookmarkData);

            // =====================================================
            // OFFLINE HANDLING
            // =====================================================
            if (!navigator.onLine) {
                console.log("📡 Offline - saving to queue");
                try {
                    await saveToOfflineQueue(bookmarkData);
                    showMessage("📡 Saved offline! Will sync when you're back online.", "success");
                    form.reset();
                    return;
                } catch (err) {
                    showMessage("Error saving offline: " + err.message, "error");
                    return;
                }
            }

            // =====================================================
            // ONLINE SUBMISSION
            // =====================================================
            try {
                let response;

                // If logged in via session, try session auth first
                if (userEmail) {
                    console.log("Attempting save with session auth...");
                    response = await fetch("/api/bookmarks/add", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        credentials: "same-origin", // Include session cookie
                        body: JSON.stringify(bookmarkData)
                    });

                    const result = await response.json();

                    // If session auth worked, we're done
                    if (result.status === "success") {
                        console.log("✅ Saved with session auth");
                        showMessage("Bookmark saved successfully!", "success");
                        setTimeout(() => {
                            window.location.href = "/links";
                        }, 1500);
                        return;
                    }

                    // Session auth failed, fall through to Basic auth
                    console.log("Session auth failed, trying Basic auth...");
                }

                // Get credentials (either for no-session or session-auth-failed)
                const auth = await getAuthCredentials();
                if (!auth || !auth.password) {
                    showMessage("Authentication required", "error");
                    return;
                }

                // Try with Basic auth
                response = await fetch("/api/bookmarks/add", {
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
                    // Clear cached credentials if auth failed
                    cachedCredentials = null;
                    showMessage("Authentication failed. Please try again.", "error");
                } else {
                    showMessage(result.message || "Error saving bookmark", "error");
                }
            } catch (err) {
                console.error("Network error:", err);
                showMessage("Network error: " + err.message, "error");
            }
        });
    }

    // Quick save button handler
    if (quickSaveBtn) {
        quickSaveBtn.addEventListener("click", async () => {
            console.log("Quick save button clicked");

            const url = websiteInput ? websiteInput.value.trim() : "";

            if (!url) {
                showMessage("URL is required", "error");
                return;
            }

            // Auto-fill title if empty
            if (titleInput && !titleInput.value) {
                titleInput.value = "Read Later";
            }

            // Auto-fill domain if empty
            if (tldInput && !tldInput.value) {
                const domain = extractDomain(url);
                if (domain) {
                    tldInput.value = domain;
                }
            }

            // Auto-generate slug if empty
            if (slugInput && !slugInput.value) {
                const slug = generateSlug(url);
                if (slug) {
                    slugInput.value = slug;
                }
            }

            // Add "read-later" tag if tags are empty
            if (tagsInput && !tagsInput.value) {
                tagsInput.value = "Read-Later";
            }

            console.log("Quick saving - submitting form");
            
            // Submit the main form
            form.dispatchEvent(new Event('submit'));
        });
    }

    console.log("🎉 All event listeners attached successfully");
});