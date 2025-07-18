class HeaderController {
    constructor() {
        this.isNavOpen = false;
        this.isTransitioning = false;

        // Get DOM elements
        this.navToggle = document.getElementById('nav-toggle');
        this.navPanel = document.getElementById('nav-panel');
        this.navToggleText = document.getElementById('nav-toggle-text');
        this.body = document.body;

        // Store the original SVG content for switching
        this.openIcon = null;
        this.closeIcon = null;

        this.init();
    }

    async init() {
        console.log('HeaderController initializing...');
        console.log('Nav toggle found:', !!this.navToggle);
        console.log('Nav panel found:', !!this.navPanel);
        console.log('Nav toggle text found:', !!this.navToggleText);

        // Load both SVG icons
        await this.loadIcons();

        // Set initial collapsed state
        if (this.navPanel) {
            this.navPanel.classList.add('is-collapsed');
            this.navPanel.setAttribute('aria-hidden', 'true');
        }

        // Add click listener
        if (this.navToggle) {
            this.navToggle.addEventListener('click', (e) => {
                console.log('Nav toggle clicked!');
                e.preventDefault();
                this.toggleNav();
            });
        }

        // Close panel on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isNavOpen) {
                this.closeNav();
            }
        });

        console.log('HeaderController initialized successfully');
    }

    async loadIcons() {
        try {
            console.log('Loading icons...');
            const openResponse = await fetch('/assets/svg/icons/panel-right---to-open.svg');
            this.openIcon = await openResponse.text();

            const closeResponse = await fetch('/assets/svg/icons/panel-right---to-close.svg');
            this.closeIcon = await closeResponse.text();

            console.log('Icons loaded successfully');
        } catch (error) {
            console.warn('Could not load navigation icons:', error);
            // Fallback - keep the existing icon
            const existingSvg = this.navToggle && this.navToggle.querySelector('svg');
            this.openIcon = existingSvg ? existingSvg.outerHTML : '';
            this.closeIcon = this.openIcon; // Use same icon as fallback
        }
    }

    toggleNav() {
        console.log('toggleNav called, current state:', this.isNavOpen);

        if (this.isTransitioning) {
            console.log('Already transitioning, ignoring click');
            return;
        }

        if (this.isNavOpen) {
            this.closeNav();
        } else {
            this.openNav();
        }
    }

    openNav() {
        console.log('Opening nav...');

        if (this.isTransitioning) return;

        this.isNavOpen = true;
        this.isTransitioning = true;

        // Simple body scroll prevention
        this.body.style.overflow = 'hidden';

        // Remove collapsed class first
        if (this.navPanel) {
            this.navPanel.classList.remove('is-collapsed');
            this.navPanel.classList.add('is-opening');
        }

        // Update nav state
        this.updateNavState();

        // Reset transitioning flag after animation
        setTimeout(() => {
            this.isTransitioning = false;
            if (this.navPanel) {
                this.navPanel.classList.remove('is-opening');
                this.navPanel.classList.add('open');
            }
        }, 300); // Adjust timing based on your CSS transition duration
    }

    closeNav() {
        console.log('Closing nav...');

        if (!this.isNavOpen) return;
        if (this.isTransitioning) return;

        this.isNavOpen = false;
        this.isTransitioning = true;

        // Simple body scroll restoration
        this.body.style.overflow = '';

        // Remove opening and open classes, add closing class
        if (this.navPanel) {
            this.navPanel.classList.remove('is-opening', 'open');
            this.navPanel.classList.add('is-closing');
        }

        // Update nav state
        this.updateNavState();

        // Reset transitioning flag after animation
        setTimeout(() => {
            this.isTransitioning = false;
            if (this.navPanel) {
                this.navPanel.classList.remove('is-closing');
                this.navPanel.classList.add('is-collapsed');
            }
        }, 300); // Adjust timing based on your CSS transition duration
    }

    updateNavState() {
        console.log('Updating nav state, isNavOpen:', this.isNavOpen);

        // Update panel attributes
        if (this.navPanel) {
            this.navPanel.setAttribute('aria-hidden', (!this.isNavOpen).toString());
        }

        // Update toggle button
        if (this.navToggle) {
            this.navToggle.setAttribute('aria-expanded', this.isNavOpen.toString());
            this.navToggle.classList.toggle('is-open', this.isNavOpen);
        }

        // Update toggle text
        if (this.navToggleText) {
            this.navToggleText.textContent = this.isNavOpen ? 'Close Navigation' : 'Open Navigation';
        }

        // Update the button icon
        this.updateIcon();
    }

    updateIcon() {
        console.log('Updating icon, isNavOpen:', this.isNavOpen);

        if (!this.openIcon || !this.closeIcon) {
            console.log('Icons not available');
            return;
        }

        // Find the SVG element in the button
        const svgElement = this.navToggle && this.navToggle.querySelector('svg');
        if (!svgElement) {
            console.log('No SVG element found');
            return;
        }

        // Replace the SVG with the appropriate icon
        const newIconHTML = this.isNavOpen ? this.closeIcon : this.openIcon;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newIconHTML;
        const newSvg = tempDiv.querySelector('svg');

        if (newSvg) {
            // Copy classes from old SVG
            if (svgElement.className.baseVal) {
                newSvg.setAttribute('class', svgElement.className.baseVal);
            }

            svgElement.replaceWith(newSvg);
            console.log('Icon updated successfully');
        } else {
            console.log('Failed to create new SVG');
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, initializing HeaderController');
        new HeaderController();
    });
} else {
    console.log('DOM already loaded, initializing HeaderController');
    new HeaderController();
}
