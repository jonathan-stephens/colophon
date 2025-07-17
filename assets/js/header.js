class HeaderController {
    constructor() {
        this.isNavOpen = false;

        // Get DOM elements
        this.navToggle = document.getElementById('nav-toggle');
        this.navClose = document.getElementById('nav-close');
        this.navPanel = document.getElementById('nav-panel');
        this.navToggleText = document.getElementById('nav-toggle-text');
        this.overlay = document.getElementById('overlay');

        this.init();
    }

    init() {
        // Check if elements exist before adding listeners
        if (this.navToggle) {
            this.navToggle.addEventListener('click', () => this.toggleNav());
        }

        if (this.navClose) {
            this.navClose.addEventListener('click', () => this.closeNav());
        }

        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.closeNav());
        }

        // Close panel on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeNav();
            }
        });
    }

    toggleNav() {
        if (this.isNavOpen) {
            this.closeNav();
        } else {
            this.openNav();
        }
    }

    openNav() {
        this.isNavOpen = true;
        this.updateNavState();
    }

    closeNav() {
        this.isNavOpen = false;
        this.updateNavState();
    }

    updateNavState() {
        // Update panel classes and attributes
        this.navPanel.classList.toggle('open', this.isNavOpen);
        this.navPanel.setAttribute('aria-hidden', (!this.isNavOpen).toString());

        // Update toggle button
        this.navToggle.setAttribute('aria-expanded', this.isNavOpen.toString());
        this.navToggleText.textContent = this.isNavOpen ? 'Close Navigation' : 'Open Navigation';

        // Update close button
        this.navClose.setAttribute('aria-expanded', this.isNavOpen.toString());

        // Update overlay
        this.overlay.classList.toggle('active', this.isNavOpen);
        this.overlay.setAttribute('aria-hidden', (!this.isNavOpen).toString());

        this.manageFocus();
    }

    manageFocus() {
        if (this.isNavOpen) {
            // Focus first navigation link
            const firstNavLink = this.navPanel.querySelector('.nav-link');
            if (firstNavLink) {
                firstNavLink.focus();
            }
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new HeaderController();
});
