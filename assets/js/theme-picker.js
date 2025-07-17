// Prevent FOUC (Flash of Unstyled Content) - inline script in <head>
const themeInitializer = `
  (function() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
    if (isDark) {
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
    this.themeToggle = document.querySelector('.js-mode-toggle');
    this.modeStatus = document.querySelector('.js-mode-status');
    this.modeToggleText = document.querySelector('.js-mode-toggle-text');
    this.toggleContainer = document.querySelector('.color-mode-toggle');
    this.htmlElement = document.documentElement;

    this.init();
  }

  init() {
    // Check if toggle exists
    if (!this.themeToggle) {
      console.warn('Theme toggle not found');
      return;
    }

    // Set initial state
    this.updateToggleState();

    // Event listener for toggle change
    this.themeToggle.addEventListener('change', () => this.handleThemeToggle());

    // Keyboard support
    this.setupKeyboardSupport();

    // Listen for system theme changes
    this.setupSystemThemeListener();

    // Make toggle visible after setup
    if (this.toggleContainer) {
      this.toggleContainer.style.visibility = 'visible';
    }
  }

  getCurrentTheme() {
    return this.htmlElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
  }

  isDarkMode() {
    return this.getCurrentTheme() === 'dark';
  }

  handleThemeToggle() {
    const isDark = this.themeToggle.checked;
    this.setTheme(isDark ? 'dark' : 'light');
  }

  setTheme(theme) {
    const isDark = theme === 'dark';

    if (isDark) {
      this.htmlElement.setAttribute('data-theme', 'dark');
    } else {
      this.htmlElement.removeAttribute('data-theme');
    }

    // Save preference
    localStorage.setItem('theme', theme);

    // Update toggle state
    this.updateToggleState();
  }

  updateToggleState() {
    const isDark = this.isDarkMode();

    // Update checkbox state
    this.themeToggle.checked = isDark;
    this.themeToggle.setAttribute('aria-checked', isDark.toString());

    // Update status text
    if (this.modeStatus) {
      this.modeStatus.textContent = `Color mode is currently ${isDark ? 'dark' : 'light'}`;
    }

    // Update toggle text
    if (this.modeToggleText) {
      this.modeToggleText.textContent = isDark ? 'Enable light mode' : 'Enable dark mode';
    }
  }

  setupKeyboardSupport() {
    this.themeToggle.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.themeToggle.checked = !this.themeToggle.checked;
        this.handleThemeToggle();
      }
    });
  }

  setupSystemThemeListener() {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

    // Use the newer addEventListener method if available
    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener('change', (e) => this.handleSystemThemeChange(e));
    } else {
      // Fallback for older browsers
      mediaQuery.addListener((e) => this.handleSystemThemeChange(e));
    }
  }

  handleSystemThemeChange(e) {
    // Only apply system theme if user hasn't set a preference
    const savedTheme = localStorage.getItem('theme');
    if (!savedTheme) {
      this.setTheme(e.matches ? 'dark' : 'light');
    }
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new ThemeManager();
});
