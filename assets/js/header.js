class HeaderController {
    constructor() {
        this.isNavOpen = false;
        this.isTransitioning = false;
        this.transitionDuration = 300;

        // Get DOM elements
        this.navToggle = document.getElementById('nav-toggle');
        this.navPanel = document.getElementById('nav-panel');
        this.navToggleText = document.getElementById('nav-toggle-text');
        this.body = document.body;

        // Store SVG icons
        this.openIcon = null;
        this.closeIcon = null;

        this.init();
    }

    init() {
        this.loadIcons().then(() => {
            this.setupInitialState();
            this.bindEvents();
        });
    }

    loadIcons() {
        return Promise.all([
            fetch('/assets/svg/icons/panel-right---to-open.svg'),
            fetch('/assets/svg/icons/panel-right---to-close.svg')
        ])
        .then(([openResponse, closeResponse]) => {
            return Promise.all([
                openResponse.text(),
                closeResponse.text()
            ]);
        })
        .then(([openIcon, closeIcon]) => {
            this.openIcon = openIcon;
            this.closeIcon = closeIcon;
        })
        .catch(() => {
            // Fallback - use existing icon
            const existingSvg = this.navToggle && this.navToggle.querySelector('svg');
            this.openIcon = (existingSvg && existingSvg.outerHTML) || '';
            this.closeIcon = this.openIcon;
        });
    }

    setupInitialState() {
        if (this.navPanel) {
            this.navPanel.classList.add('is-collapsed', 'transitions-enabled');
            this.navPanel.setAttribute('aria-hidden', 'true');
        }
    }

    bindEvents() {
        this.navToggle && this.navToggle.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleNav();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isNavOpen) {
                this.closeNav();
            }
        });
    }

    toggleNav() {
        if (this.isTransitioning) return;

        this.isNavOpen ? this.closeNav() : this.openNav();
    }

    openNav() {
        if (this.isTransitioning) return;

        this.isNavOpen = true;
        this.isTransitioning = true;
        this.body.classList.add('nav-open');

        if (this.navPanel) {
            this.navPanel.classList.remove('is-collapsed');
            this.navPanel.classList.add('is-opening');
        }

        this.updateNavState();
        this.scheduleTransitionEnd(() => {
            if (this.navPanel) {
                this.navPanel.classList.remove('is-opening');
                this.navPanel.classList.add('open');
            }
        });
    }

    closeNav() {
        if (!this.isNavOpen || this.isTransitioning) return;

        this.isNavOpen = false;
        this.isTransitioning = true;
        this.body.classList.remove('nav-open');

        if (this.navPanel) {
            this.navPanel.classList.remove('is-opening', 'open');
            this.navPanel.classList.add('is-closing');
        }

        this.updateNavState();
        this.scheduleTransitionEnd(() => {
            if (this.navPanel) {
                this.navPanel.classList.remove('is-closing');
                this.navPanel.classList.add('is-collapsed');
            }
        });
    }

    scheduleTransitionEnd(callback) {
        setTimeout(() => {
            this.isTransitioning = false;
            callback();
        }, this.transitionDuration);
    }

    updateNavState() {
        if (this.navPanel) {
            this.navPanel.setAttribute('aria-hidden', (!this.isNavOpen).toString());
        }

        if (this.navToggle) {
            this.navToggle.setAttribute('aria-expanded', this.isNavOpen.toString());
            this.navToggle.classList.toggle('is-open', this.isNavOpen);
        }

        if (this.navToggleText) {
            this.navToggleText.textContent = this.isNavOpen ? 'Close Navigation' : 'Open Navigation';
        }

        this.updateIcon();
    }

    updateIcon() {
        if (!this.openIcon || !this.closeIcon) return;

        const svgElement = this.navToggle && this.navToggle.querySelector('svg');
        if (!svgElement) return;

        const newIconHTML = this.isNavOpen ? this.closeIcon : this.openIcon;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newIconHTML;
        const newSvg = tempDiv.querySelector('svg');

        if (newSvg) {
            if (svgElement.className.baseVal) {
                newSvg.setAttribute('class', svgElement.className.baseVal);
            }
            svgElement.replaceWith(newSvg);
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new HeaderController());
} else {
    new HeaderController();
}
