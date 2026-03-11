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
            this._updateNavFocusability(false);
        }
    }

    bindEvents() {
        this.navToggle?.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleNav();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isNavOpen) {
                this.closeNav();
                this.navToggle?.focus(); // return focus to the toggle on Escape
                return;
            }
            if (e.key === 'Tab' && this.isNavOpen) this._trapFocus(e);
        });

        this.navPanel?.addEventListener('transitionend', (e) => {
            if (e.target !== this.navPanel || e.propertyName !== 'transform') return;
            this._onTransitionEnd();
        });
    }
    _getFocusableElements() {
        return Array.from(this.navPanel.querySelectorAll(
            'a:not([tabindex="-1"]), button:not([tabindex="-1"]), input:not([tabindex="-1"]), select:not([tabindex="-1"]), textarea:not([tabindex="-1"]), [tabindex]:not([tabindex="-1"])'
        )).filter(el => !el.disabled);
    }

    _trapFocus(e) {
        // Include the toggle button itself as the first element in the trap
        const focusable  = [this.navToggle, ...this._getFocusableElements()];
        const firstEl    = focusable[0];
        const lastEl     = focusable[focusable.length - 1];

        if (e.shiftKey) {
            // Shift+Tab: if on the first element, wrap to the last
            if (document.activeElement === firstEl) {
                e.preventDefault();
                lastEl.focus();
            }
        } else {
            // Tab: if on the last element, wrap to the first (the toggle/close button)
            if (document.activeElement === lastEl) {
                e.preventDefault();
                firstEl.focus();
            }
        }
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

    _updateNavFocusability(isOpen) {
        if (!this.navPanel) return;

        const focusable = this.navPanel.querySelectorAll(
            'a, button, input, select, textarea, [tabindex]'
        );

        focusable.forEach(el => {
            if (isOpen) {
                // Restore natural tab order (remove the attr entirely, or set to "0")
                el.removeAttribute('tabindex');
            } else {
                // Pull out of tab order while panel is hidden
                el.setAttribute('tabindex', '-1');
            }
        });
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
            this._updateNavFocusability(true);
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
            this._updateNavFocusability(false);
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