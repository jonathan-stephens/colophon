class HeaderController {
    constructor() {
        this.isNavOpen = false;
        this.isTransitioning = false;
        // Get DOM elements
        this.navToggle = document.getElementById('nav-toggle');
        this.navPanel = document.getElementById('nav-panel');
        this.navToggleText = document.getElementById('nav-toggle-text');

        // Store the original SVG content for switching
        this.openIcon = null;
        this.closeIcon = null;

        this.init();
    }

    async init() {
        // Load both SVG icons
        await this.loadIcons();

        // Check if elements exist before adding listeners
        if (this.navToggle) {
            this.navToggle.addEventListener('click', () => this.toggleNav());
        }

        // Listen for transition end to update classes
        if (this.navPanel) {
            this.navPanel.addEventListener('transitionend', (e) => {
                // Only handle the main panel transition, not child elements
                if (e.target === this.navPanel) {
                    this.handleTransitionEnd();
                }
            });
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

    async loadIcons() {
        try {
            // Load the open icon (current one)
            const openResponse = await fetch('/assets/svg/icons/panel-left---to-open.svg');
            this.openIcon = await openResponse.text();

            // Load the close icon
            const closeResponse = await fetch('/assets/svg/icons/panel-left---to-close.svg');
            this.closeIcon = await closeResponse.text();
        } catch (error) {
            console.warn('Could not load navigation icons:', error);
            // Fallback - keep the existing icon
            this.openIcon = this.navToggle.querySelector('svg')?.outerHTML || '';
            this.closeIcon = this.openIcon; // Use same icon as fallback
        }
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

        // Force a reflow to ensure the class change is applied
        this.navPanel.offsetHeight;

        this.updateNavState();
    }

    closeNav() {
        if (this.isTransitioning || !this.isNavOpen) return;

        this.isNavOpen = false;
        this.isTransitioning = true;

        // Remove opening class and add closing class
        this.navPanel.classList.remove('is-opening');
        this.navPanel.classList.add('is-closing');

        // Force a reflow to ensure the class change is applied
        this.navPanel.offsetHeight;

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

        // Update the button icon
        this.updateIcon();

        // Update the button class for styling
        this.navToggle.classList.toggle('is-open', this.isNavOpen);

        // Add state classes to body for global styling control
        document.body.classList.toggle('nav-open', this.isNavOpen);

        this.manageFocus();
    }

    updateIcon() {
        if (!this.openIcon || !this.closeIcon) return;

        // Find the SVG element in the button
        const svgElement = this.navToggle.querySelector('svg');
        if (!svgElement) return;

        // Replace the SVG with the appropriate icon
        const newIconHTML = this.isNavOpen ? this.closeIcon : this.openIcon;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newIconHTML;
        const newSvg = tempDiv.querySelector('svg');

        if (newSvg) {
            svgElement.replaceWith(newSvg);
        }
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
