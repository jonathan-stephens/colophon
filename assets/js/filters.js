(function () {
    'use strict';

    const supportsPopover = 'popover' in HTMLElement.prototype;

    document.querySelectorAll('.filter-popover').forEach(function (popover) {
        var triggerId = popover.getAttribute('aria-labelledby');
        var trigger = triggerId ? document.getElementById(triggerId) : null;

        // ── 1. Sync aria-expanded (both native and fallback) ──────────────
        if (supportsPopover && trigger) {
            popover.addEventListener('toggle', function (e) {
                trigger.setAttribute('aria-expanded', e.newState === 'open' ? 'true' : 'false');
            });
        }

        // ── 2. Close on item select (native) ──────────────────────────────
        if (supportsPopover) {
            popover.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    popover.hidePopover();
                });
            });
            return; // native handles everything else
        }

        // ── 3. Fallback for browsers without Popover API ──────────────────
        if (!trigger) return;

        function allPopovers() {
            return document.querySelectorAll('.filter-popover');
        }

        function closeAll() {
            allPopovers().forEach(function (p) {
                p.removeAttribute('data-open');
                var t = document.getElementById(p.getAttribute('aria-labelledby'));
                if (t) t.setAttribute('aria-expanded', 'false');
            });
        }

        function positionUnder(popover, trigger) {
            var rect = trigger.getBoundingClientRect();
            popover.style.top  = (rect.bottom + window.scrollY + 4) + 'px';
            popover.style.left = rect.left + 'px';
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = popover.hasAttribute('data-open');
            closeAll();
            if (!isOpen) {
                positionUnder(popover, trigger);
                popover.setAttribute('data-open', '');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });

        popover.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                closeAll();
            });
        });

        // Light-dismiss
        document.addEventListener('click', function (e) {
            if (!popover.contains(e.target) && e.target !== trigger) {
                closeAll();
            }
        });

        // Esc key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });
    });
})();