<?php
/**
 * Share Target Page Template
 */

// Handle both GET and POST for share target
// Try multiple ways to get the data
$sharedUrl = '';
$sharedTitle = '';
$sharedText = '';

// Method 1: GET parameters
if (get('url')) {
    $sharedUrl = get('url');
    $sharedTitle = get('title', '');
    $sharedText = get('text', '');
}
// Method 2: POST parameters
elseif (isset($_POST['url'])) {
    $sharedUrl = $_POST['url'];
    $sharedTitle = $_POST['title'] ?? '';
    $sharedText = $_POST['text'] ?? '';
}
// Method 3: Check request body
elseif ($kirby->request()->method() === 'POST') {
    $body = $kirby->request()->body();
    if (isset($body['url'])) {
        $sharedUrl = $body['url'];
        $sharedTitle = $body['title'] ?? '';
        $sharedText = $body['text'] ?? '';
    }
}

snippet('site-header') ?>

<div class="share-page">
  <div class="container">
      <h1>Save Bookmark</h1>

      <?php if (option('debug', false)): ?>
      <!-- Debug info - only shows if debug mode is on -->
      <div style="background: #fff3cd; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; font-size: 0.85rem;">
          <strong>Debug Info:</strong><br>
          Method: <?= $kirby->request()->method() ?><br>
          GET url: <?= get('url') ? 'YES' : 'NO' ?><br>
          POST url: <?= isset($_POST['url']) ? 'YES' : 'NO' ?><br>
          Shared URL: <?= $sharedUrl ?: 'EMPTY' ?><br>
          Shared Title: <?= $sharedTitle ?: 'EMPTY' ?><br>
      </div>
      <?php endif; ?>

      <form id="bookmark-form" class="bookmark-form">
          <div class="form-group">
              <label for="website">URL *</label>
              <input
                  type="url"
                  id="website"
                  name="website"
                  required
                  value="<?= esc($sharedUrl) ?>"
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
              ></textarea>
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
}

.bookmark-form {
    background: var(--background-primary);
    padding: 2rem;
    border-radius: var(--radii-square);
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
}

input[type="url"],
input[type="text"],
textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color-distinct);
    border-radius: var(--radii-square);
    font-size: 1rem;
    font-family: inherit;
    background:var(--background-secondary);
}

textarea {
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--button-radius);
    cursor: pointer;
}

.btn-primary {
    background: var(--button-bg);
    color: var(--button-color);
}

.btn-secondary {
    background: var(--button-hover-bg);
    color: var(--button-hover-color);
}

.btn:hover {
    opacity: 0.9;
}

.message {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: var(--radii-square)
}

.message.success {
    background: var(--success-background);
    color: var(--success-color);
}

.message.error {
    background: var(--error-background);
    color: var(--error-color);
}

@media (max-width: 600px) {
    .form-row {
        flex-direction: column;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<?= js('assets/js/share-min.js', ['defer' => true]) ?>

<?php snippet('site-footer') ?>
