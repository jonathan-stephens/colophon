class HeaderController {
    constructor() {
        this.isNavOpen = false;
        this.isTransitioning = false;
        this.scrollPosition = 0;

        // Get DOM elements
        this.navToggle = document.getElementById('nav-toggle');
        this.navPanel = document.getElementById('nav-panel');
        this.navToggleText = document.getElementById('nav-toggle-text');
        this.body = document.body;
        this.html = document.documentElement;

        // Store the original SVG content for switching
        this.openIcon = null;
        this.closeIcon = null;

        // Mobile detection
        this.isMobile = this.detectMobile();

        // Debounce resize handler
        this.handleResize = this.debounce(this.handleResize.bind(this), 150);

        this.init();
    }

    detectMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
               window.innerWidth <= 768;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async init() {
        // Load both SVG icons
        await this.loadIcons();

        // Replace the existing timeout with this:
        // Set initial collapsed state FIRST, before any transitions
        this.navPanel.classList.add('is-collapsed');
        this.navPanel.setAttribute('aria-hidden', 'true');

        // Enable transitions after DOM is fully ready
        requestAnimationFrame(() => {
          this.navPanel.classList.add('transitions-enabled');
        });
                
        // Check if elements exist before adding listeners
        if (this.navToggle) {
            this.navToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNav();
            });
        }

        // Enhanced transition handling
        if (this.navPanel) {
            this.navPanel.addEventListener('transitionend', (e) => {
                // Only handle the main panel transition, not child elements
                if (e.target === this.navPanel && e.propertyName === 'transform') {
                    this.handleTransitionEnd();
                }
            });
        }

        // Close panel on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isNavOpen) {
                this.closeNav();
            }
        });

        // Enhanced outside click handling
        document.addEventListener('click', (e) => {
            if (this.isNavOpen && !this.isTransitioning) {
                // Check if click is outside nav panel and header
                if (!this.navPanel.contains(e.target) &&
                    !e.target.closest('.site-header')) {
                    this.closeNav();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', this.handleResize);

        // Handle orientation change on mobile
        if (this.isMobile) {
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    this.handleResize();
                }, 100);
            });
        }

        // Handle page visibility change to prevent issues with mobile browsers
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && this.isNavOpen) {
                this.closeNav();
            }
        });

        // Preload animations on mobile
        if (this.isMobile) {
            this.preloadAnimations();
        }
    }

    preloadAnimations() {
        // Force hardware acceleration by triggering a transform
        requestAnimationFrame(() => {
            this.navPanel.style.transform = 'translateX(-100%) translateZ(0)';
        });
    }

    handleResize() {
        // Update mobile detection
        this.isMobile = this.detectMobile();

        // Close nav if open and switching to desktop
        if (this.isNavOpen && !this.isMobile) {
            this.closeNav();
        }
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
            const existingSvg = this.navToggle.querySelector('svg');
            this.openIcon = existingSvg ? existingSvg.outerHTML : '';
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

        // Store current scroll position and prevent body scroll
        this.scrollPosition = window.pageYOffset;
        this.preventBodyScroll();

        // Remove collapsed class first
        this.navPanel.classList.remove('is-collapsed');

        // Force a reflow to ensure the class change is applied
        this.navPanel.offsetHeight;

        // Then add opening class for animation
        this.navPanel.classList.add('is-opening');

        // Update nav state
        this.updateNavState();
    }

    closeNav() {
        if (!this.isNavOpen) return;
        if (this.isTransitioning) return;

        this.isNavOpen = false;
        this.isTransitioning = true;

        // Remove opening and open classes, add closing class
        this.navPanel.classList.remove('is-opening', 'open');
        this.navPanel.classList.add('is-closing');

        // Update nav state
        this.updateNavState();

        // Restore body scroll
        this.restoreBodyScroll();
    }

    preventBodyScroll() {
        // Enhanced body scroll prevention for mobile
        this.body.classList.add('nav-open');

        if (this.isMobile) {
            // Additional mobile-specific scroll prevention
            this.body.style.position = 'fixed';
            this.body.style.top = `-${this.scrollPosition}px`;
            this.body.style.width = '100%';
        }
    }

    restoreBodyScroll() {
        // Restore body scroll
        this.body.classList.remove('nav-open');

        if (this.isMobile) {
            // Restore mobile-specific styles
            this.body.style.position = '';
            this.body.style.top = '';
            this.body.style.width = '';

            // Restore scroll position
            window.scrollTo(0, this.scrollPosition);
        }
    }

    handleTransitionEnd() {
        this.isTransitioning = false;

        if (this.isNavOpen) {
            // Transition to open state complete
            this.navPanel.classList.remove('is-opening');
            this.navPanel.classList.add('open');
        } else {
            // Transition to closed state complete - clean up all classes
            this.navPanel.classList.remove('is-closing', 'open');
            this.navPanel.classList.add('is-collapsed');
        }
    }

    updateNavState() {
        // Update panel classes and attributes
        this.navPanel.setAttribute('aria-hidden', (!this.isNavOpen).toString());

        // Update toggle button
        this.navToggle.setAttribute('aria-expanded', this.isNavOpen.toString());
        this.navToggleText.textContent = this.isNavOpen ? 'Close Navigation' : 'Open Navigation';

        // Update the button icon
        this.updateIcon();

        // Update the button class for styling
        this.navToggle.classList.toggle('is-open', this.isNavOpen);

        // Manage focus
        if (this.isNavOpen) {
            this.manageFocus();
        }
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
            // Copy classes from old SVG
            newSvg.className = svgElement.className;
            svgElement.replaceWith(newSvg);
        }
    }

    manageFocus() {
        if (this.isNavOpen) {
            // Use a small delay to ensure the panel is visible
            setTimeout(() => {
                const firstNavLink = this.navPanel.querySelector('.nav-link');
                if (firstNavLink) {
                    firstNavLink.focus();
                }
            }, 100);
        }
    }
}

// Enhanced initialization with better loading handling
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new HeaderController();
    });
} else {
    // DOM is already loaded
    new HeaderController();
}
