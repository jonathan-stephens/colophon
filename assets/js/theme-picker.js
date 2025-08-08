(() => {
    // Wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        // Theme functions
        const getSystemMode = () => window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const getUserTheme = () => {
            try {
                return localStorage.getItem('theme-preference') || 'system';
            } catch {
                return 'system'; // Fallback if localStorage unavailable
            }
        };
        const saveUserTheme = (theme) => {
            try {
                localStorage.setItem('theme-preference', theme);
            } catch {
                // Silently fail if localStorage unavailable
            }
        };

        const getAppliedTheme = (userPref) => {
            if (userPref === 'light') return 'light';
            if (userPref === 'dark') return 'dark';
            return getSystemMode(); // system or fallback
        };

        const setTheme = (mode) => {
            document.documentElement.dataset.theme = mode;
            // Update icon state via CSS
            document.documentElement.dataset.themeIcon = mode;
        };

        const toggleTheme = () => {
            const userPref = getUserTheme();
            const currentApplied = getAppliedTheme(userPref);
            const newPref = currentApplied === 'dark' ? 'light' : 'dark';

            saveUserTheme(newPref);
            setTheme(newPref);
        };

        // Get elements
        const themeToggle = document.getElementById('theme-toggle-footer');
        const scrollFloating = document.getElementById('scroll-to-top-floating');

        if (!scrollFloating) return;

        // State
        let isScrollVisible = false;
        let ticking = false;

        // Scroll handling
        const update = () => {
            const shouldShowScroll = window.scrollY > 300;

            // Toggle scroll-to-top visibility
            if (shouldShowScroll !== isScrollVisible) {
                isScrollVisible = shouldShowScroll;
                const method = shouldShowScroll ? 'add' : 'remove';
                scrollFloating.classList[method]('show');
                scrollFloating.setAttribute('aria-hidden', !shouldShowScroll);
            }

            ticking = false;
        };

        const handleScroll = () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        };

        // Initialize theme
        const userPref = getUserTheme();
        const appliedTheme = getAppliedTheme(userPref);
        setTheme(appliedTheme);

        // Set accessibility attributes
        scrollFloating.setAttribute('aria-hidden', 'true');

        // Event listeners
        window.addEventListener('scroll', handleScroll, { passive: true });
        themeToggle?.addEventListener('click', toggleTheme);
        scrollFloating.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // System theme changes (only if user hasn't set a preference)
        window.matchMedia?.('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const currentUserPref = getUserTheme();
            if (currentUserPref === 'system') {
                const newSystemMode = getSystemMode();
                setTheme(newSystemMode);
            }
        });

        update();
    }
})();
