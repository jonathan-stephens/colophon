(() => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        // =====================================================
        // THEME
        // =====================================================

        const getSystemMode  = () => window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const getUserTheme   = () => { try { return localStorage.getItem('theme-preference') || 'system'; } catch { return 'system'; } };
        const saveUserTheme  = (t) => { try { localStorage.setItem('theme-preference', t); } catch (_) { /* localStorage unavailable — fail silently */ } };
        const getAppliedTheme = (pref) => pref === 'light' || pref === 'dark' ? pref : getSystemMode();
        const setTheme       = (mode) => { document.documentElement.dataset.theme = mode; };

        const toggleTheme = () => {
            const newPref = getAppliedTheme(getUserTheme()) === 'dark' ? 'light' : 'dark';
            saveUserTheme(newPref);
            setTheme(newPref);
        };

        // Apply theme immediately
        setTheme(getAppliedTheme(getUserTheme()));

        // Attach theme toggle — must happen before any early returns
        document.getElementById('theme-toggle-footer')?.addEventListener('click', toggleTheme);

        // React to OS-level changes if user hasn't pinned a preference
        window.matchMedia?.('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (getUserTheme() === 'system') setTheme(getSystemMode());
        });

        // =====================================================
        // SCROLL TO TOP
        // =====================================================

        const scrollFloating = document.getElementById('scroll-to-top-floating');
        if (!scrollFloating) return; // scroll button is optional — theme is already wired up above

        let isScrollVisible = false;
        let ticking         = false;

        const update = () => {
            const shouldShow = window.scrollY > 300;
            if (shouldShow !== isScrollVisible) {
                isScrollVisible = shouldShow;
                scrollFloating.classList.toggle('show', shouldShow);
                scrollFloating.setAttribute('aria-hidden', String(!shouldShow));
            }
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });

        scrollFloating.setAttribute('aria-hidden', 'true');
        scrollFloating.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        update();
    }
})();