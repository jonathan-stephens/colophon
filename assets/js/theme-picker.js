// Create a style element to prevent FOUC (Flash of Unstyled Content)
const themeInitializer = `
  (function() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  })();
`;

// Create and insert the script element
const script = document.createElement('script');
script.textContent = themeInitializer;
document.head.insertBefore(script, document.head.firstChild);

// Main theme functionality
class ThemeManager {
  constructor() {
    this.modeToggle = document.querySelector('.js-mode-toggle');
    this.modeStatus = document.querySelector('.js-mode-status');
    this.modeToggleText = document.querySelector('.js-mode-toggle-text');
    this.htmlElement = document.documentElement;

    this.init();
  }

  init() {
    // Set initial state
    this.setInitialTheme();

    // Event listeners
    this.modeToggle.addEventListener('change', () => this.handleThemeToggle());
    this.setupKeyboardSupport();
    this.setupSystemThemeListener();
  }

  setInitialTheme() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) {
      this.applyTheme(savedTheme === 'dark');
    } else if (systemPrefersDark) {
      this.applyTheme(true);
    }

    // Make toggle visible after initial setup
    document.querySelector('.color-mode-toggle').style.visibility = 'visible';
  }

  applyTheme(isDark) {
    if (isDark) {
      this.htmlElement.setAttribute('data-theme', 'dark');
      localStorage.setItem('theme', 'dark');
      this.modeStatus.textContent = 'Color mode is now dark';
      this.modeToggleText.textContent = 'Enable light mode';
      this.modeToggle.checked = true;
      this.modeToggle.setAttribute('aria-checked', 'true');
    } else {
      this.htmlElement.removeAttribute('data-theme');
      localStorage.setItem('theme', 'light'); // Store light theme explicitly
      this.modeStatus.textContent = 'Color mode is now light';
      this.modeToggleText.textContent = 'Enable dark mode';
      this.modeToggle.checked = false;
      this.modeToggle.setAttribute('aria-checked', 'false');
    }
  }

  handleThemeToggle() {
    const isDark = this.htmlElement.getAttribute('data-theme') === 'dark';
    this.applyTheme(!isDark);
  }

  setupKeyboardSupport() {
    this.modeToggle.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.modeToggle.checked = !this.modeToggle.checked;
        this.handleThemeToggle();
      }
    });
  }

  setupSystemThemeListener() {
    window.matchMedia('(prefers-color-scheme: dark)').addListener(e => {
      // Only change if no saved preference exists
      if (!localStorage.getItem('theme')) {
        this.applyTheme(e.matches);
      }
    });
  }
}

// Initialize theme manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new ThemeManager();
});
