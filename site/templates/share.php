<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

/**
 * Share Target Page Template - With User Detection
 */

// Get current user if logged in
$currentUser = $kirby->user();

// Capture ALL possible data sources
$sharedUrl = '';
$sharedTitle = '';
$sharedText = '';
$dataSource = 'none';

// Method 1: GET parameters (from service worker redirect)
if (get('url')) {
    $sharedUrl = get('url');
    $sharedTitle = get('title', '');
    $sharedText = get('text', '');
    $dataSource = 'GET';
}
// Method 2: POST parameters (direct POST, SW not active)
elseif (isset($_POST['url'])) {
    $sharedUrl = $_POST['url'];
    $sharedTitle = $_POST['title'] ?? '';
    $sharedText = $_POST['text'] ?? '';
    $dataSource = 'POST';
}

// FIX: Some browsers send URL in the 'text' field instead of 'url' field
if (empty($sharedUrl) && !empty($sharedText)) {
    // Check if text looks like a URL
    if (preg_match('/^https?:\/\//', $sharedText)) {
        $sharedUrl = $sharedText;
        $sharedText = ''; // Clear text since it was the URL
        $dataSource .= ' (URL from text field)';
    }
}

snippet('site-header') ?>

<div class="share-page">
  <div class="container">
      <h1>Save Bookmark</h1>

      <?php if ($kirby->option('debug', false)): ?>
      <!-- DEBUG PANEL - Only visible when debug mode is enabled -->
      <div class="debug-panel">
          <strong>🔍 Debug Info:</strong><br>
          <strong>Logged in:</strong> <?= $currentUser ? '✅ ' . $currentUser->email() : '❌ Not logged in' ?><br>
          <strong>Request Method:</strong> <?= $kirby->request()->method() ?><br>
          <strong>Data Source:</strong> <?= $dataSource ?><br>
          <strong>Shared URL:</strong> <?= $sharedUrl ?: '❌ EMPTY' ?><br>
          <strong>Shared Title:</strong> <?= $sharedTitle ?: '❌ EMPTY' ?><br>
          <strong>Shared Text:</strong> <?= $sharedText ?: '❌ EMPTY' ?><br>
          <button type="button" onclick="testServiceWorker()" class="btn-link">
              Test Service Worker Status
          </button>
          <div id="sw-status" style="margin-top: 0.5rem; padding: 0.5rem; background: #f0f0f0; border-radius: 4px; font-size: 0.85rem;"></div>
      </div>

      <!-- JavaScript Loading Test -->
      <div id="js-test" style="background: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
          ⚠️ JavaScript not loaded yet
      </div>
      <?php endif; ?>

      <form id="bookmark-form" class="bookmark-form">
          <div class="form-group">
              <label for="website">URL<sup>*</sup></label>
              <input
                  type="url"
                  id="website"
                  name="website"
                  required
                  value="<?= esc($sharedUrl) ?>"
                  placeholder="https://example.com"
              >
              <button type="button" id="fetch-metadata-btn" class="button">
                  Fetch metadata from URL
              </button>
          </div>

          <div class="form-group">
              <label for="page-title">Page Title<sup>*</sup></label>
              <input
                  type="text"
                  id="page-title"
                  name="page-title"
                  required
                  value="<?= esc($sharedTitle) ?>"
                  placeholder="Title of the page you're bookmarking"
              >
          </div>

          <div class="form-row">
              <div class="form-group half">
                  <label for="tld">Domain<sup>*</sup></label>
                  <input
                      type="text"
                      id="tld"
                      name="tld"
                      required
                      placeholder="e.g., example.com"
                  >
              </div>

              <div class="form-group half">
                  <label for="slug">Slug<sup>*</sup></label>
                  <input
                      type="text"
                      id="slug"
                      name="slug"
                      required
                      placeholder="e.g., article-slug"
                  >
                  <small class="field-help">Auto-generated from URL, but you can edit it</small>
              </div>
          </div>

          <div class="form-group">
              <label for="author">Author</label>
              <input
                  type="text"
                  id="author"
                  name="author"
                  placeholder="Optional"
              >
          </div>

          <div class="form-group">
              <label for="tags">Tags</label>
              <input
                  type="text"
                  id="tags"
                  name="tags"
                  placeholder="Start typing for suggestions..."
              >
              <div id="tag-suggestions" class="tag-suggestions"></div>
          </div>

          <div class="form-group">
              <label for="text">Description</label>
              <textarea
                  id="text"
                  name="text"
                  rows="6"
                  placeholder="Add notes, quotes, or description..."
              ><?= esc($sharedText) ?></textarea>
          </div>

          <div class="form-actions">
              <button type="button" id="quick-save-btn" class="button">
                  Quick Save (Read Later)
              </button>
              <button type="submit" class="button" data-button-variant="primary">
                  Save Bookmark
              </button>
          </div>

          <textarea id="message" class="message" style="display: none;"></textarea>
      </form>
  </div>
</div>

<style>
.share-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.container {
    width: 100%;
}

.debug-panel {
    background: #fff3cd;
    color: #856404;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    font-size: 0.85rem;
    border: 2px solid #ffc107;
}

.debug-panel strong {
    display: inline-block;
    min-width: 120px;
}

.bookmark-form {
    background: var(--background-primary, #ffffff);
    padding: 2rem;
    border-radius: var(--radii-square, 8px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: flex;
    gap: 1rem;
}

.form-group.half {
    flex: 1;
}


.field-help {
    display: block;
    margin-top: 0.25rem;
    color: var(--color-subtle);
    font-style: italic;
}

input[type="url"],
input[type="text"],
textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color-distinct, #ddd);
    border-radius: var(--radii-square, 4px);
    font-size: 1rem;
    font-family: inherit;
    background: var(--background-secondary, #f9f9f9);
    box-sizing: border-box;
}

textarea {
    resize: vertical;
}

.message {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: var(--radii-square, 4px);
    font-weight: 500;
}

.message.success {
    background: var(--success-background, #d4edda);
    color: var(--success-color, #155724);
    border: 1px solid #c3e6cb;
}

.message.error {
    background: var(--error-background, #f8d7da);
    color: var(--error-color, #721c24);
    border: 1px solid #f5c6cb;
}

.message.info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.message.warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* Tag Autocomplete Styles */
.tag-suggestions {
    position: relative;
    margin-top: 0.5rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    display: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.tag-suggestions.active {
    display: block;
}

.tag-suggestion {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background 0.2s;
}

.tag-suggestion:hover,
.tag-suggestion.selected {
    background: #f0f0f0;
}

.tag-suggestion mark {
    background: #ffd700;
    font-weight: 600;
}

/* Offline Indicator */
.offline-indicator {
    position: fixed;
    top: 1rem;
    right: 1rem;
    background: #ff9800;
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 1000;
    display: none;
}

.offline-indicator.show {
    display: block;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 600px) {
    .form-row {
        flex-direction: column;
    }

    .form-actions {
        flex-direction: column;
    }

    .share-page {
        padding: 1rem 0.5rem;
    }

    .bookmark-form {
        padding: 1rem;
    }
}
</style>

<!-- Offline Indicator -->
<div id="offline-indicator" class="offline-indicator">
    📡 You're offline. Bookmarks will be saved locally and synced when you're back online.
</div>

<!-- Pass user email to JavaScript if logged in -->
<script>
<?php if ($currentUser): ?>
document.body.dataset.userEmail = '<?= esc($currentUser->email()) ?>';
console.log('✅ User logged in:', '<?= esc($currentUser->email()) ?>');
<?php else: ?>
console.log('ℹ️ No user logged in - will prompt for credentials');
<?php endif; ?>

<?php if ($kirby->option('debug', false)): ?>
// Debug mode enabled - load service worker test
async function testServiceWorker() {
    const statusDiv = document.getElementById('sw-status');
    statusDiv.innerHTML = '⏳ Checking...';

    if (!('serviceWorker' in navigator)) {
        statusDiv.innerHTML = '❌ Service Worker not supported in this browser';
        return;
    }

    try {
        const registration = await navigator.serviceWorker.getRegistration();

        if (!registration) {
            statusDiv.innerHTML = '❌ No Service Worker registered<br><em>The PWA share target will not work!</em>';
            return;
        }

        const status = {
            scope: registration.scope,
            state: registration.active ? registration.active.state : 'none',
            installing: !!registration.installing,
            waiting: !!registration.waiting,
            active: !!registration.active,
            controlling: !!navigator.serviceWorker.controller
        };

        let html = '✅ Service Worker found:<br>';
        html += `<strong>Scope:</strong> ${status.scope}<br>`;
        html += `<strong>State:</strong> ${status.state}<br>`;
        html += `<strong>Controlling page:</strong> ${status.controlling ? '✅ YES' : '❌ NO (refresh needed)'}<br>`;
        html += `<strong>Active:</strong> ${status.active ? '✅' : '❌'}<br>`;

        if (status.waiting) {
            html += '<br>⚠️ Update waiting - refresh to activate';
        }

        statusDiv.innerHTML = html;

    } catch (err) {
        statusDiv.innerHTML = '❌ Error checking Service Worker: ' + err.message;
    }
}

// Auto-run on page load
window.addEventListener('load', () => {
    setTimeout(testServiceWorker, 500);
});

// Test indicator
window.addEventListener('DOMContentLoaded', () => {
    const testDiv = document.getElementById('js-test');
    if (testDiv) {
        testDiv.textContent = '⏳ DOMContentLoaded fired, waiting for share.js...';
        testDiv.style.background = '#fff3cd';
        testDiv.style.color = '#856404';
    }

    setTimeout(() => {
        if (testDiv && testDiv.textContent.includes('waiting')) {
            testDiv.textContent = '❌ share.js failed to load or execute';
            testDiv.style.background = '#f8d7da';
            testDiv.style.color = '#721c24';
            console.error('share.js did not execute within 2 seconds');
        }
    }, 2000);
});
<?php endif; ?>
</script>

<?= js('assets/js/share.js') ?>

<?php snippet('site-footer') ?>
