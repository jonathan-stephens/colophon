// FOUC prevention - inline this in your <head>
       (function() {
           const saved = localStorage.getItem('theme');
           const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
           const isDark = saved === 'dark' || (!saved && systemDark);
           if (isDark) {
               document.documentElement.setAttribute('data-theme', 'dark');
           }
       })();

       // Lightweight Theme Manager
       class ThemeManager {
           constructor() {
               this.toggle = document.querySelector('.js-mode-toggle');
               this.statusElement = document.querySelector('.js-mode-status');
               this.toggleContainer = document.querySelector('.color-mode-toggle');
               this.htmlElement = document.documentElement;

               this.init();
           }

           init() {
               if (!this.toggle) {
                   console.warn('Theme toggle not found');
                   return;
               }

               // Set initial state based on current theme
               this.updateToggleState();

               // Single event listener for all interactions
               this.toggle.addEventListener('change', () => this.handleToggle());

               // Listen for system theme changes (only if no saved preference)
               this.setupSystemThemeListener();

               // Show toggle after setup
               if (this.toggleContainer) {
                   this.toggleContainer.style.visibility = 'visible';
               }
           }

           getCurrentTheme() {
               return this.htmlElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
           }

           handleToggle() {
               const newTheme = this.toggle.checked ? 'dark' : 'light';
               this.setTheme(newTheme);
           }

           setTheme(theme) {
               const isDark = theme === 'dark';

               // Update DOM
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
               const isDark = this.getCurrentTheme() === 'dark';

               // Update checkbox
               this.toggle.checked = isDark;
               this.toggle.setAttribute('aria-checked', isDark.toString());

               // Update status for screen readers
               if (this.statusElement) {
                   this.statusElement.textContent = `Color mode is currently ${isDark ? 'dark' : 'light'}`;
               }
           }

           setupSystemThemeListener() {
               const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

               const handleSystemChange = (e) => {
                   // Only apply system theme if user hasn't set a preference
                   if (!localStorage.getItem('theme')) {
                       this.setTheme(e.matches ? 'dark' : 'light');
                   }
               };

               // Use modern addEventListener with fallback
               if (mediaQuery.addEventListener) {
                   mediaQuery.addEventListener('change', handleSystemChange);
               } else {
                   mediaQuery.addListener(handleSystemChange);
               }
           }
       }

       // Initialize when DOM is ready
       document.addEventListener('DOMContentLoaded', () => {
           new ThemeManager();
       });
