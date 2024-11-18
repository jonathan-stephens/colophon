const modeToggle = document.querySelector('.js-mode-toggle');
const modeStatus = document.querySelector('.js-mode-status');
const modeToggleText = document.querySelector('.js-mode-toggle-text');
const htmlElement = document.documentElement;

// Determine initial theme
function setInitialTheme() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) {
        applyTheme(savedTheme === 'dark');
    } else if (systemPrefersDark) {
        applyTheme(true);
    }
}

// Apply theme and update accessibility attributes
function applyTheme(isDark) {
    if (isDark) {
        htmlElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        modeStatus.textContent = 'Color mode is now dark';
        modeToggleText.textContent = 'Enable light mode';
        modeToggle.checked = true;
        modeToggle.setAttribute('aria-checked', 'true');
    } else {
        htmlElement.removeAttribute('data-theme');
        localStorage.removeItem('theme');
        modeStatus.textContent = 'Color mode is now light';
        modeToggleText.textContent = 'Enable dark mode';
        modeToggle.checked = false;
        modeToggle.setAttribute('aria-checked', 'false');
    }
}

// Initial theme setup
setInitialTheme();

// Theme toggle event listener
modeToggle.addEventListener('change', () => {
    const isDark = htmlElement.getAttribute('data-theme') === 'dark';
    applyTheme(!isDark);
});

// Keyboard support
modeToggle.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        modeToggle.checked = !modeToggle.checked;
        const isDark = htmlElement.getAttribute('data-theme') === 'dark';
        applyTheme(!isDark);
    }
});

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addListener(e => {
    // Only change if no saved preference exists
    if (!localStorage.getItem('theme')) {
        applyTheme(e.matches);
    }
});
