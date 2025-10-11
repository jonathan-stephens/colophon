<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

/**
 * Share Target Page Template - Enhanced with Debug
 */

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

snippet('site-header') ?>

<div class="share-page">
  <div class="container">
      <h1>Save Bookmark</h1>

      <!-- ALWAYS VISIBLE DEBUG PANEL -->
      <div class="debug-panel">
          <strong>🔍 Debug Info:</strong><br>
          <strong>Request Method:</strong> <?= $kirby->request()->method() ?><br>
          <strong>Data Source:</strong> <?= $dataSource ?><br>
          <strong>Shared URL:</strong> <?= $sharedUrl ?: '❌ EMPTY' ?><br>
          <strong>Shared Title:</strong> <?= $sharedTitle ?: '❌ EMPTY' ?><br>
          <strong>Shared Text:</strong> <?= $sharedText ?: '❌ EMPTY' ?><br>
          <strong>GET params:</strong> <?= !empty($_GET) ? json_encode($_GET) : '[]' ?><br>
          <strong>POST params:</strong> <?= !empty($_POST) ? json_encode($_POST) : '[]' ?><br>
          <strong>Referer:</strong> <?= $_SERVER['HTTP_REFERER'] ?? 'none' ?><br>
          <button type="button" onclick="testServiceWorker()" class="btn-link">
              Test Service Worker Status
          </button>
          <div id="sw-status" style="margin-top: 0.5rem; padding: 0.5rem; background: #f0f0f0; border-radius: 4px; font-size: 0.85rem;"></div>
      </div>

      <!-- JavaScript Loading Test -->
      <div id="js-test" style="background: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
          ⚠️ JavaScript not loaded yet
      </div>

      <form id="bookmark-form" class="bookmark-form">
          <div class="form-group">
              <label for="website">URL *</label>
              <input
                  type="url"
                  id="website"
                  name="website"
                  required
                  value="<?= esc($sharedUrl) ?>"
                  placeholder="https://example.com"
              >
              <?php if ($sharedUrl): ?>
              <button type="button" id="fetch-metadata-btn" class="btn-link">
                  Fetch metadata from URL
              </button>
              <?php endif; ?>
          </div>

          <div class="form-group">
              <label for="page-title">Page Title *</label>
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
                  <label for="tld">Domain *</label>
                  <input
                      type="text"
                      id="tld"
                      name="tld"
                      required
                      placeholder="e.g., example.com"
                  >
              </div>

              <div class="form-group half">
                  <label for="author">Author</label>
                  <input
                      type="text"
                      id="author"
                      name="author"
                      placeholder="Optional"
                  >
              </div>
          </div>

          <div class="form-group">
              <label for="tags">Tags</label>
              <input
                  type="text"
                  id="tags"
                  name="tags"
                  placeholder="Comma-separated tags"
              >
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
              <button type="button" id="quick-save-btn" class="btn btn-secondary">
                  Quick Save (Read Later)
              </button>
              <button type="submit" class="btn btn-primary">
                  Save Bookmark
              </button>
          </div>

          <div id="message" class="message" style="display: none;"></div>
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

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--text-color, #333);
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

.btn-link {
    background: none;
    border: none;
    color: var(--link-color, #0066cc);
    cursor: pointer;
    text-decoration: underline;
    padding: 0.5rem 0;
    font-size: 0.9rem;
}

.btn-link:hover {
    opacity: 0.8;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--button-radius, 4px);
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: opacity 0.2s;
}

.btn-primary {
    background: var(--button-bg, #0066cc);
    color: var(--button-color, #ffffff);
}

.btn-secondary {
    background: var(--button-hover-bg, #6c757d);
    color: var(--button-hover-color, #ffffff);
}

.btn:hover {
    opacity: 0.9;
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

<!-- Service Worker Status Test -->
<script>
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
</script>

<!-- Load JavaScript with error handling -->
<script>
console.log('🔍 Inline script loaded - checking for share.js...');

window.addEventListener('DOMContentLoaded', () => {
    const testDiv = document.getElementById('js-test');
    if (testDiv) {
        testDiv.textContent = '⏳ DOMContentLoaded fired, waiting for share.js...';
        testDiv.style.background = '#fff3cd';
        testDiv.style.color = '#856404';
    }

    console.log('Form element:', document.getElementById('bookmark-form'));
    console.log('Website input:', document.getElementById('website'));
    console.log('Website input VALUE:', document.getElementById('website')?.value);

    setTimeout(() => {
        if (testDiv && testDiv.textContent.includes('waiting')) {
            testDiv.textContent = '❌ share.js failed to load or execute';
            testDiv.style.background = '#f8d7da';
            testDiv.style.color = '#721c24';
            console.error('share.js did not execute within 2 seconds');
        }
    }, 2000);
});
</script>

<?= js('assets/js/share.js') ?>

<script src="/assets/js/share.js" onerror="console.error('Failed to load /assets/js/share.js')"></script>

<?php snippet('site-footer') ?>
