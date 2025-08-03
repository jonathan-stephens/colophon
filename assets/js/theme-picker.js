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
        const themeFloating = document.getElementById('theme-toggle-floating');
        const themeFooter = document.getElementById('theme-toggle-footer');
        const scrollFloating = document.getElementById('scroll-to-top-floating');
        const footer = document.querySelector('.sticky-bottom');

        if (!themeFloating || !scrollFloating) return;

        // State
        let isVisible = false;
        let isMorphed = false;
        let ticking = false;

        // Scroll handling
        const update = () => {
            const { scrollY, innerHeight } = window;
            const { scrollHeight } = document.documentElement;

            const shouldShow = scrollY > 300;
            const shouldMorph = shouldShow && (scrollHeight - scrollY - innerHeight) <= 100;

            // Toggle visibility
            if (shouldShow !== isVisible) {
                isVisible = shouldShow;
                const method = shouldShow ? 'add' : 'remove';
                themeFloating.classList[method]('show');
                scrollFloating.classList[method]('show');
                themeFloating.setAttribute('aria-hidden', !shouldShow);
                scrollFloating.setAttribute('aria-hidden', !shouldShow);
            }

            // Toggle morphing
            if (shouldMorph !== isMorphed) {
                isMorphed = shouldMorph;
                const method = shouldMorph ? 'add' : 'remove';
                themeFloating.classList[method]('morphed');
                scrollFloating.classList[method]('morphed');
                footer?.classList[method]('show-controls');
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
        [themeFloating, scrollFloating].forEach(btn => {
            btn.setAttribute('aria-hidden', 'true');
        });

        // Event listeners
        window.addEventListener('scroll', handleScroll, { passive: true });
        themeFloating.addEventListener('click', toggleTheme);
        themeFooter?.addEventListener('click', toggleTheme);
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
