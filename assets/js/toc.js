(function() {
    'use strict';

    function buildHierarchy(headings) {
        const result = [];
        const stack = [];

        for (const heading of headings) {
            const level = +heading.tagName[1];
            const item = {
                element: heading,
                level,
                text: heading.textContent.trim(),
                id: heading.id,
                children: []
            };

            while (stack.length && stack[stack.length - 1].level >= level) {
                stack.pop();
            }

            (stack.length ? stack[stack.length - 1].children : result).push(item);
            stack.push(item);
        }

        return result;
    }

    function renderToc(items, nestLevel = 0) {
        if (!items.length) return '';

        let html = nestLevel === 0 ? '' : `<ol class="toc-list toc-nestlvl-${nestLevel}">`;

        if (nestLevel === 0) {
            for (const item of items) {
                html += `<li class="toc-item toc-level-${item.level}">`;
                html += `<a href="#${item.id}" class="toc-link" data-level="${item.level}">${item.text.replace(/[<>&"']/g, c => ({
                    '<': '&lt;', '>': '&gt;', '&': '&amp;', '"': '&quot;', "'": '&#39;'
                })[c])}</a>`;

                if (item.children.length) {
                    html += renderToc(item.children, nestLevel + 1);
                }

                html += '</li>';
            }
        } else {
            for (const item of items) {
                html += `<li class="toc-item toc-level-${item.level}">`;
                html += `<a href="#${item.id}" class="toc-link" data-level="${item.level}">${item.text.replace(/[<>&"']/g, c => ({
                    '<': '&lt;', '>': '&gt;', '&': '&amp;', '"': '&quot;', "'": '&#39;'
                })[c])}</a>`;

                if (item.children.length) {
                    html += renderToc(item.children, nestLevel + 1);
                }

                html += '</li>';
            }
            html += '</ol>';
        }

        return html;
    }

    function setupInteractions(tocNav, headings) {
        tocNav.addEventListener('click', e => {
            if (!e.target.matches('.toc-link')) return;
            e.preventDefault();

            const target = document.getElementById(e.target.getAttribute('href').slice(1));
            if (!target) return;

            window.scrollTo({
                top: target.getBoundingClientRect().top + window.pageYOffset - 20,
                behavior: 'smooth'
            });

            history.replaceState?.(null, null, e.target.getAttribute('href'));
        });

        if (!window.IntersectionObserver) return;

        const observer = new IntersectionObserver(entries => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;

                tocNav.querySelectorAll('.toc-link').forEach(link => link.classList.remove('active'));

                const link = tocNav.querySelector(`.toc-link[href="#${entry.target.id}"]`);
                link?.classList.add('active');
            }
        }, { rootMargin: '-10% 0px -80% 0px' });

        headings.forEach(heading => observer.observe(heading));
    }

    function processToc(tocNav) {
        const config = JSON.parse(tocNav.dataset.tocConfig);
        const tocContainer = document.getElementById(tocNav.id + '-content');
        const mainContent = document.querySelector(config.mainSelector);

        if (!tocContainer || !mainContent) {
            console.warn('TOC: Missing elements for', tocNav.id);
            return;
        }

        const selector = Array.from({length: config.maxLevel - config.minLevel + 1}, (_, i) =>
            `h${config.minLevel + i}`).join(',');

        let headings = [...mainContent.querySelectorAll(selector)];

        if (config.exclude?.length) {
            headings = headings.filter(heading =>
                !config.exclude.some(sel => {
                    try {
                        return heading.matches(sel) || heading.closest(sel) || heading.querySelector(sel);
                    } catch {
                        console.warn('TOC: Invalid selector:', sel);
                        return false;
                    }
                })
            );
        }

        if (!headings.length) {
            tocNav.classList.add('hidden');
            return;
        }

        const usedIds = new Set();
        for (const heading of headings) {
            if (heading.id) {
                usedIds.add(heading.id);
                continue;
            }

            let baseId = heading.textContent.trim()
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '') || 'heading';

            let finalId = baseId;
            let counter = 1;
            while (usedIds.has(finalId) || document.getElementById(finalId)) {
                finalId = `${baseId}-${counter++}`;
            }

            heading.id = finalId;
            usedIds.add(finalId);
        }

        const hierarchy = buildHierarchy(headings);

        // If tocContainer is already an <ol>, add the class and render items directly
        if (tocContainer.tagName === 'OL') {
            tocContainer.className = 'toc-list toc-nestlvl-0';
            tocContainer.innerHTML = renderToc(hierarchy);
        } else {
            // If tocContainer is a div or other element, render the full <ol>
            tocContainer.innerHTML = `<ol class="toc-list toc-nestlvl-0">${renderToc(hierarchy)}</ol>`;
        }

        tocNav.style.display = '';
        setupInteractions(tocNav, headings);
    }

    function init() {
        document.querySelectorAll('.table-of-contents[data-toc-config]').forEach(tocNav => {
            try {
                processToc(tocNav);
            } catch (error) {
                console.error('TOC: Error processing', tocNav.id, error);
            }
        });

        if (location.hash) {
            setTimeout(() => document.querySelector(location.hash)?.scrollIntoView({ behavior: 'smooth' }), 100);
        }
    }

    document.readyState === 'loading' ?
        document.addEventListener('DOMContentLoaded', init) : init();
})();
