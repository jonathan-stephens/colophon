class HeaderController {
    constructor() {
        this.isNavOpen = false;
        this.isTransitioning = false;
        // Get DOM elements
        this.navToggle = document.getElementById('nav-toggle');
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

        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.closeNav());
        }

        // Listen for transition end to update classes
        if (this.navPanel) {
            this.navPanel.addEventListener('transitionend', () => this.handleTransitionEnd());
        }

        // Close panel on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeNav();
            }
        });

        // Close panel on outside click (anywhere except nav panel and header)
        document.addEventListener('click', (e) => {
            if (this.isNavOpen && !this.isTransitioning) {
                // Check if click is outside nav panel and header
                if (!this.navPanel.contains(e.target) &&
                    !e.target.closest('.site-header')) {
                    this.closeNav();
                }
            }
        });

        // Set initial collapsed state
        this.navPanel.classList.add('is-collapsed');
    }

    toggleNav() {
        if (this.isTransitioning) return; // Prevent multiple clicks during transition

        if (this.isNavOpen) {
            this.closeNav();
        } else {
            this.openNav();
        }
    }

    openNav() {
        if (this.isTransitioning) return;

        this.isNavOpen = true;
        this.isTransitioning = true;

        // Remove collapsed class and add opening class
        this.navPanel.classList.remove('is-collapsed');
        this.navPanel.classList.add('is-opening');

        this.updateNavState();
    }

    closeNav() {
        if (this.isTransitioning) return;

        this.isNavOpen = false;
        this.isTransitioning = true;

        // Add closing class
        this.navPanel.classList.add('is-closing');

        this.updateNavState();
    }

    handleTransitionEnd() {
        this.isTransitioning = false;

        if (this.isNavOpen) {
            // Transition to open state complete
            this.navPanel.classList.remove('is-opening');
        } else {
            // Transition to closed state complete - clean up all classes
            this.navPanel.classList.remove('is-closing');
            this.navPanel.classList.add('is-collapsed');
        }
    }

    updateNavState() {
        // Update panel classes and attributes
        this.navPanel.classList.toggle('open', this.isNavOpen);
        this.navPanel.setAttribute('aria-hidden', (!this.isNavOpen).toString());

        // Update toggle button
        this.navToggle.setAttribute('aria-expanded', this.isNavOpen.toString());
        this.navToggleText.textContent = this.isNavOpen ? 'Close Navigation' : 'Open Navigation';

        // Update the button icon (you'll need to handle this in CSS or swap the SVG)
        // You could add a class to the button to change the icon via CSS
        this.navToggle.classList.toggle('is-open', this.isNavOpen);

        // Update overlay for smooth left-slide transition
        // The overlay should fade in/out in sync with the panel slide
        this.overlay.classList.toggle('active', this.isNavOpen);
        this.overlay.setAttribute('aria-hidden', (!this.isNavOpen).toString());

        // Add state classes to body for global styling control
        document.body.classList.toggle('nav-open', this.isNavOpen);

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
