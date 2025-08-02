(() => {
    const button = document.querySelector('.scroll-to-top');
    if (!button) return;

    let isVisible = false;
    let ticking = false;
    const threshold = 300;

    const updateVisibility = () => {
        const shouldShow = window.scrollY > threshold;
        if (shouldShow !== isVisible) {
            isVisible = shouldShow;
            button.classList.toggle('show', shouldShow);
            button.setAttribute('aria-hidden', (!shouldShow).toString());
        }
        ticking = false;
    };

    const handleScroll = () => {
        if (!ticking) {
            requestAnimationFrame(updateVisibility);
            ticking = true;
        }
    };

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Set initial accessibility attributes
    button.setAttribute('aria-label', 'Scroll to top of page');
    button.setAttribute('aria-hidden', 'true');

    window.addEventListener('scroll', handleScroll, { passive: true });
    button.addEventListener('click', scrollToTop);
})();
