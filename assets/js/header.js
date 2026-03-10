class HeaderController {
    constructor() {
        this.isNavOpen      = false;
        this.isTransitioning = false;

        this.navToggle  = document.getElementById('nav-toggle');
        this.navPanel   = document.getElementById('nav-panel');
        this.toggleText = document.getElementById('nav-toggle-text');
        this.body       = document.body;

        this.init();
    }

    init() {
        this.setupInitialState();
        this.bindEvents();
    }

    setupInitialState() {
        // Panel starts off-screen; transitions are enabled via CSS once is-animating is added
        if (this.navPanel) {
            this.navPanel.setAttribute('aria-hidden', 'true');
        }
    }

    bindEvents() {
        this.navToggle?.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleNav();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isNavOpen) this.closeNav();
        });

        // transitionend for accuracy; fallback guards against cases where
        // the transition never fires (prefers-reduced-motion, hidden element, etc.)
        this.navPanel?.addEventListener('transitionend', (e) => {
            if (e.target !== this.navPanel || e.propertyName !== 'transform') return;
            this._onTransitionEnd();
        });
    }

    _onTransitionEnd() {
        clearTimeout(this._transitionFallback);
        this.navPanel.classList.remove('is-animating');
        this.isTransitioning = false;
    }

    // Safety valve: if transitionend never fires, unlock after CSS duration + buffer
    _scheduleTransitionFallback(durationMs) {
        clearTimeout(this._transitionFallback);
        this._transitionFallback = setTimeout(() => {
            if (this.isTransitioning) this._onTransitionEnd();
        }, durationMs + 50);
    }

    toggleNav() {
        if (this.isTransitioning) return;
        this.isNavOpen ? this.closeNav() : this.openNav();
    }

    openNav() {
        this.isNavOpen       = true;
        this.isTransitioning = true;

        this.body.classList.add('nav-open');

        if (this.navPanel) {
            this.navPanel.classList.add('is-animating');
            this.navPanel.getBoundingClientRect(); // force reflow so browser sees transition start
            this.navPanel.classList.add('is-open');
            this.navPanel.setAttribute('aria-hidden', 'false');
        }

        this.updateToggleState();
        this._scheduleTransitionFallback(500);
    }

    closeNav() {
        this.isNavOpen       = false;
        this.isTransitioning = true;

        this.body.classList.remove('nav-open');

        if (this.navPanel) {
            this.navPanel.classList.add('is-animating');
            this.navPanel.getBoundingClientRect(); // same reflow requirement as openNav
            this.navPanel.classList.remove('is-open');
            this.navPanel.setAttribute('aria-hidden', 'true');
        }

        this.updateToggleState();
        this._scheduleTransitionFallback(400);
    }

    updateToggleState() {
        if (!this.navToggle) return;

        this.navToggle.setAttribute('aria-expanded', this.isNavOpen.toString());
        this.navToggle.classList.toggle('is-open', this.isNavOpen);

        // Icon swap — both SVGs are in the DOM, CSS handles visibility
        // No DOM creation, no innerHTML parsing
        if (this.toggleText) {
            this.toggleText.textContent = this.isNavOpen ? 'Close Menu' : 'Open Menu';
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new HeaderController());
} else {
    new HeaderController();
}