(function() {
    'use strict';

    // Helper functions
    function buildHierarchy(headings) {
        var result = [];
        var stack = [];

        headings.forEach(function(heading) {
            var level = parseInt(heading.tagName.charAt(1));
            var item = {
                element: heading,
                level: level,
                text: heading.textContent.trim(),
                id: heading.id,
                children: []
            };

            // Find correct parent level
            while (stack.length > 0 && stack[stack.length - 1].level >= level) {
                stack.pop();
            }

            if (stack.length === 0) {
                result.push(item);
            } else {
                stack[stack.length - 1].children.push(item);
            }

            stack.push(item);
        });

        return result;
    }

    function shouldShowToc(hierarchy, config) {
        function hasEnoughSubheadings(items) {
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                if (item.children.length >= config.minSubheadings) {
                    return true;
                }
                if (hasEnoughSubheadings(item.children)) {
                    return true;
                }
            }
            return false;
        }

        return hasEnoughSubheadings(hierarchy);
    }

    function renderTocLevel(items, config, level) {
        if (items.length === 0) return '';

        var html = '<ol class="toc-list toc-level-' + level + '">';

        items.forEach(function(item) {
            var hasChildren = item.children.length > 0;
            var showChildren = hasChildren && (item.children.length >= config.minSubheadings || level > 0);

            html += '<li class="toc-item toc-level-' + item.level + '">';
            html += '<a href="#' + item.id + '" class="toc-link" data-level="' + item.level + '">';
            html += escapeHtml(item.text);
            html += '</a>';

            if (showChildren) {
                html += renderTocLevel(item.children, config, level + 1);
            }

            html += '</li>';
        });

        html += '</ol>';
        return html;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function setupTOCInteractions(tocNav, headings) {
        var tocLinks = tocNav.querySelectorAll('.toc-link');

        // Smooth scrolling for TOC links
        tocLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var targetId = this.getAttribute('href').substring(1);
                var target = document.getElementById(targetId);

                if (target) {
                    var offset = 20;
                    var elementPosition = target.getBoundingClientRect().top;
                    var offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Update URL without triggering scroll
                    if (history.replaceState) {
                        history.replaceState(null, null, '#' + targetId);
                    }
                }
            });
        });

        // Active state management with intersection observer
        if ('IntersectionObserver' in window) {
            var observerOptions = {
                rootMargin: '-10% 0px -80% 0px',
                threshold: 0
            };

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    var id = entry.target.getAttribute('id');
                    var tocLink = tocNav.querySelector('.toc-link[href="#' + id + '"]');

                    if (tocLink) {
                        if (entry.isIntersecting) {
                            // Remove active class from all links
                            tocLinks.forEach(function(link) {
                                link.classList.remove('active');
                            });
                            // Add active class to current link
                            tocLink.classList.add('active');
                        }
                    }
                });
            }, observerOptions);

            // Observe all headings
            headings.forEach(function(heading) {
                observer.observe(heading);
            });
        }

        // Handle page load with hash
        if (window.location.hash) {
            setTimeout(function() {
                var target = document.querySelector(window.location.hash);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }, 100);
        }
    }

    function processTOC(tocNav, tocContainer, mainContent, config) {
        // Build heading selector based on level range
        var headingLevels = [];
        for (var i = config.minLevel; i <= config.maxLevel; i++) {
            headingLevels.push('h' + i);
        }
        var headingSelector = headingLevels.join(', ');

        // Get all headings from main content
        var headings = Array.from(mainContent.querySelectorAll(headingSelector));

        // Filter out excluded headings
        if (config.exclude.length > 0) {
            headings = headings.filter(function(heading) {
                var shouldExclude = config.exclude.some(function(selector) {
                    try {
                        // Check if heading matches the exclusion selector
                        return heading.matches(selector) ||
                               heading.closest(selector) !== null ||
                               heading.querySelector(selector) !== null;
                    } catch (error) {
                        console.warn('TOC: Invalid exclusion selector:', selector, error.message);
                        return false;
                    }
                });
                return !shouldExclude;
            });
        }

        if (headings.length === 0) {
            tocNav.classList.add('hidden');
            return;
        }

        // Generate IDs for headings that don't have them
        var usedIds = new Set();
        headings.forEach(function(heading) {
            if (!heading.id) {
                var text = heading.textContent.trim();
                var baseId = text
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');

                // Fallback for empty or invalid IDs
                if (!baseId) {
                    baseId = 'heading';
                }

                var finalId = baseId;
                var counter = 1;
                while (usedIds.has(finalId) || document.getElementById(finalId)) {
                    finalId = baseId + '-' + counter;
                    counter++;
                }

                heading.id = finalId;
                usedIds.add(finalId);
            } else {
                usedIds.add(heading.id);
            }
        });

        // Build and check hierarchy
        var hierarchy = buildHierarchy(headings);

        if (!shouldShowToc(hierarchy, config)) {
            tocNav.classList.add('hidden');
            return;
        }

        // Render TOC
        tocContainer.innerHTML = renderTocLevel(hierarchy, config, 0);
        tocNav.style.display = '';

        // Setup interactions
        setupTOCInteractions(tocNav, headings);
    }

    function initTOC() {
        // Find all TOC containers on the page
        var tocNavs = document.querySelectorAll('.table-of-contents[data-toc-config]');

        if (tocNavs.length === 0) {
            return;
        }

        // Process each TOC
        tocNavs.forEach(function(tocNav) {
            try {
                // Configuration from data attribute
                var config = JSON.parse(tocNav.getAttribute('data-toc-config'));
                var tocId = tocNav.id;
                var tocContainer = document.getElementById(tocId + '-content');
                var mainContent = document.querySelector(config.mainSelector);

                if (!tocNav || !tocContainer || !mainContent) {
                    console.warn('TOC: Required elements not found for', tocId);
                    return;
                }

                processTOC(tocNav, tocContainer, mainContent, config);

            } catch (error) {
                console.error('TOC: Error processing TOC', error);
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTOC);
    } else {
        initTOC();
    }

})();
