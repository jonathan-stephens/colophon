<?php
/**
 * Share Target Page Template
 * Place in site/templates/share.php
 * Create a page in Kirby Panel at /share using this template
 */

snippet('site-header') ?>

<section class="share-page">
  <div class="container">
      <h1>Save Bookmark</h1>

      <form id="bookmark-form" class="bookmark-form">
          <div class="form-group">
              <label for="website">URL *</label>
              <input
                  type="url"
                  id="website"
                  name="website"
                  required
                  value="<?= esc(get('url', '')) ?>"
              >
          </div>

          <div class="form-group">
              <label for="page-title">Page Title *</label>
              <input
                  type="text"
                  id="page-title"
                  name="page-title"
                  required
                  value="<?= esc(get('title', '')) ?>"
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
</section>

<style>
.share-page {
    padding: 2rem 1rem;
    max-width: 800px;
    margin: 0 auto;
}

.bookmark-form {
    background: var(--background-secondary);
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

<script>
// Auto-extract domain when URL changes
document.getElementById('website').addEventListener('blur', function() {
    const url = this.value;
    if (url) {
        try {
            const urlObj = new URL(url);
            const host = urlObj.hostname;
            // Remove www. if present
            const domain = host.replace(/^www\./, '');
            document.getElementById('tld').value = domain;
        } catch (e) {
            console.error('Invalid URL');
        }
    }
});

// Check if user is logged in via Kirby session
async function checkAuth() {
    try {
        const response = await fetch('/api/users/<?= $kirby->user() ? $kirby->user()->email() : "" ?>', {
            credentials: 'include'
        });
        return response.ok;
    } catch (e) {
        return false;
    }
}

// Get API credentials
async function getApiAuth() {
    <?php if ($kirby->user()): ?>
    // User is logged into Kirby Panel
    // We still need the password for Basic Auth to the API
    let password = localStorage.getItem('kirby_api_password');

    if (!password) {
        password = prompt('Please enter your Kirby password for API access:');
        if (password) {
            localStorage.setItem('kirby_api_password', password);
        } else {
            return null;
        }
    }

    return {
        email: '<?= $kirby->user()->email() ?>',
        password: password
    };
    <?php else: ?>
    // User is not logged in, need both email and password
    const email = localStorage.getItem('kirby_api_email');
    const password = localStorage.getItem('kirby_api_password');

    if (!email || !password) {
        const newEmail = prompt('Please enter your Kirby email:');
        const newPassword = prompt('Please enter your Kirby password:');

        if (newEmail && newPassword) {
            localStorage.setItem('kirby_api_email', newEmail);
            localStorage.setItem('kirby_api_password', newPassword);
            return { email: newEmail, password: newPassword };
        }
        return null;
    }

    return { email, password };
    <?php endif; ?>
}

function showMessage(text, type) {
    const messageEl = document.getElementById('message');
    messageEl.textContent = text;
    messageEl.className = 'message ' + type;
    messageEl.style.display = 'block';

    setTimeout(() => {
        messageEl.style.display = 'none';
    }, 5000);
}

// Regular save
document.getElementById('bookmark-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const auth = await getApiAuth();
    if (!auth || !auth.password) {
        showMessage('Authentication required', 'error');
        return;
    }

    const formData = {
        website: document.getElementById('website').value,
        title: document.getElementById('page-title').value,
        tld: document.getElementById('tld').value,
        author: document.getElementById('author').value,
        tags: document.getElementById('tags').value,
        text: document.getElementById('text').value
    };

    try {
        const response = await fetch('/api/bookmarks/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(auth.email + ':' + auth.password)
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.status === 'success') {
            showMessage('Bookmark saved successfully!', 'success');
            // Clear the form
            document.getElementById('bookmark-form').reset();
        } else {
            // If authentication failed, clear stored password
            if (result.message && result.message.includes('authentication')) {
                localStorage.removeItem('kirby_api_password');
                showMessage('Authentication failed. Please try again.', 'error');
            } else {
                showMessage(result.message || 'Error saving bookmark', 'error');
            }
        }
    } catch (error) {
        showMessage('Network error: ' + error.message, 'error');
    }
});

// Quick save
document.getElementById('quick-save-btn').addEventListener('click', async function() {
    const auth = await getApiAuth();
    if (!auth || !auth.password) {
        showMessage('Authentication required', 'error');
        return;
    }

    const url = document.getElementById('website').value;
    const title = document.getElementById('page-title').value;

    if (!url) {
        showMessage('URL is required', 'error');
        return;
    }

    const data = {
        url: url,
        title: title,
        text: ''
    };

    try {
        const response = await fetch('/api/bookmarks/quick-add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa(auth.email + ':' + auth.password)
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.status === 'success') {
            showMessage('Quickly saved!', 'success');
            // Clear the form
            document.getElementById('bookmark-form').reset();
        } else {
            // If authentication failed, clear stored password
            if (result.message && result.message.includes('authentication')) {
                localStorage.removeItem('kirby_api_password');
                showMessage('Authentication failed. Please try again.', 'error');
            } else {
                showMessage(result.message || 'Error saving bookmark', 'error');
            }
        }
    } catch (error) {
        showMessage('Network error: ' + error.message, 'error');
    }
});

// Auto-fill domain on page load if URL is present
window.addEventListener('DOMContentLoaded', function() {
    const websiteInput = document.getElementById('website');
    if (websiteInput.value) {
        websiteInput.dispatchEvent(new Event('blur'));
    }
});
</script>
<?php snippet('site-footer') ?>
