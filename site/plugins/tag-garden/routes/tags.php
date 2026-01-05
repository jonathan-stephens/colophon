<?php

/**
 * Tag Garden Routes
 *
 * Custom URL routing for tag pages.
 *
 * Routes:
 * - /tags                    → Tags index (all tags)
 * - /tags/design             → Single tag view
 * - /tags/design+code        → Multiple tags view (AND logic)
 * - /tags?sort=planted       → Tags index with sort parameter
 * - /tags/design?sort=length → Single tag with sort parameter
 *
 * @version 1.0.0
 */

use TagGarden\Helpers;

return [

    /**
     * Tags Index Route
     *
     * Displays all tags in a cloud/list view with optional filtering
     * URL: /tags
     * Query params:
     * - sort: Sort method for displayed content
     * - group: Filter by content group
     * - theme: Filter by tag theme
     */
    [
        'pattern' => 'tags',
        'action' => function() {
            // Get query parameters
            $sort = get('sort', option('yourusername.tag-garden.default.sort', 'tended'));
            $group = get('group');
            $theme = get('theme');

            // Get all tags
            $tags = kirby()->collection('tags.all');

            // Filter by group if specified
            if ($group) {
                $tags = kirby()->collection('tags.byGroup', ['group' => $group]);
            }

            // Filter by theme if specified
            if ($theme) {
                $tags = kirby()->collection('tags.byTheme', ['theme' => $theme]);
            }

            // Check if a tags page exists in content
            $tagsPage = page('tags');

            if ($tagsPage) {
                // Use existing tags page
                return $tagsPage->render([
                    'tags' => $tags,
                    'sort' => $sort,
                    'group' => $group,
                    'theme' => $theme,
                ]);
            } else {
                // Create virtual page
                return Page::factory([
                    'slug' => 'tags',
                    'template' => 'tags',
                    'model' => 'tags',
                    'content' => [
                        'title' => 'Tags',
                        'tags' => $tags,
                        'sort' => $sort,
                        'group' => $group,
                        'theme' => $theme,
                    ]
                ]);
            }
        }
    ],

    /**
     * Single/Multiple Tag Route
     *
     * Displays content filtered by one or more tags
     * URL: /tags/{tag} or /tags/{tag}+{tag2}
     * Query params:
     * - sort: Sort method
     * - logic: 'OR' (default) or 'AND' for multiple tags
     */
    [
        'pattern' => 'tags/(:any)',
        'action' => function(string $tagString) {
          // DEBG: Build debug output
          $debug = "=== TAG ROUTE DEBUG ===\n";
          $debug .= "Raw tagString from URL: " . $tagString . "\n";

          // ADD CONFIG CHECK HERE
          $debug .= "\n=== CONFIG CHECK ===\n";
          $debug .= "sort.methods config: " . (option('yourusername.tag-garden.sort.methods') ? 'EXISTS' : 'NULL') . "\n";
          $debug .= "group.definitions config: " . (option('yourusername.tag-garden.group.definitions') ? 'EXISTS' : 'NULL') . "\n";
          $debug .= "Garden definition direct test: " . print_r(option('yourusername.tag-garden.group.definitions'), true) . "\n";

            // Get query parameters
            $sort = get('sort', option('yourusername.tag-garden.default.sort', 'tended'));
            $logic = get('logic', 'OR');
            $groupFilter = get('group'); // Filter by content group
            $typeFilter = get('type'); // Filter by content type
            $page = get('page', 1); // Pagination

            // If type is specified, auto-detect its group
            $autoDetectedGroup = null;
            if ($typeFilter && !$groupFilter) {
                $autoDetectedGroup = Helpers::getGroupForType($typeFilter);
            }

            $debug .= "Query params - sort: $sort, group: " . ($groupFilter ?? 'all') . ", type: " . ($typeFilter ?? 'all') . ", page: $page\n";
            if ($autoDetectedGroup) {
                $debug .= "Auto-detected group for type '$typeFilter': $autoDetectedGroup\n";
            }

            // Parse tags from URL
            $tags = Helpers::urlToTags($tagString);
            $debug .= "After urlToTags: " . print_r($tags, true) . "\n";

            // Sanitize tags
            $tags = array_map([Helpers::class, 'sanitizeTag'], $tags);
            $debug .= "After sanitize: " . print_r($tags, true) . "\n";

            $tags = array_filter($tags);
            $debug .= "After filter: " . print_r($tags, true) . "\n";

            if (empty($tags)) {
                // No valid tags, redirect to tags index
                go('tags');
            }

            // IMPORTANT: Preserve the original tags before they get modified
            $filterTags = $tags;
            $debug .= "filterTags preserved: " . print_r($filterTags, true) . "\n";

            // Store debug for template
            $routeDebug = $debug;

            // Get pages with these tags (use helper directly for case-insensitive matching)
            $pages = Helpers::getPagesByTags($filterTags, $logic);
            // Get pages with these tags (use helper directly for case-insensitive matching)
            $pages = Helpers::getPagesByTags($filterTags, $logic);

            // DEBUG: Check what getPagesByTags returned
            $debug .= "\n=== GETPAGESBYTAGS DETAILED DEBUG ===\n";
            $debug .= "Pages returned from getPagesByTags: " . $pages->count() . "\n";

            // Check if ANY pages have tags
            $allPagesWithTags = kirby()->site()->index()->filterBy('tags', '!=', '');
            $debug .= "Total pages with tags in entire site: " . $allPagesWithTags->count() . "\n";

            // Show a few sample tags to see the format
            $sampleCount = 0;
            foreach ($allPagesWithTags as $p) {
                if ($sampleCount >= 5) break;
                $debug .= "  Sample: " . $p->title() . " → tags: [" . $p->tags() . "]\n";
                $sampleCount++;
            }

            // Try a manual filter to see if the issue is in Helpers
            $manualFilter = kirby()->site()->index()->filter(function($page) use ($filterTags) {
                $pageTags = $page->tags()->split(',');
                foreach ($pageTags as $tag) {
                    $tag = trim($tag);
                    if (mb_strtolower($tag) === mb_strtolower($filterTags[0])) {
                        return true;
                    }
                }
                return false;
            });
            $debug .= "Manual case-insensitive filter found: " . $manualFilter->count() . " pages\n";
            // Apply group filter if specified
            if ($groupFilter) {
                $groupDef = Helpers::getGroupDefinition($groupFilter);
                if ($groupDef && isset($groupDef['types'])) {
                    $pages = $pages->filter(function($page) use ($groupDef) {
                        $template = $page->intendedTemplate()->name();
                        return in_array($template, $groupDef['types']);
                    });
                    $debug .= "Filtered by group '$groupFilter': " . $pages->count() . " pages\n";
                }
            }

            // Apply type filter if specified
            if ($typeFilter) {
                $pages = $pages->filterBy('intendedTemplate', $typeFilter);
                $debug .= "Filtered by type '$typeFilter': " . $pages->count() . " pages\n";
            }

            // Apply sorting
            $pages = Helpers::sortPages($pages, $sort);

            $debug .= "Pages found after getPagesByTags: " . $pages->count() . "\n";

            // Calculate pagination FIRST
            $perPage = 20;
            $total = $pages->count();
            $pagination = $pages->paginate($perPage, ['page' => $page]);

            $debug .= "\n=== PAGINATION ===\n";
            $debug .= "Total pages: $total, Per page: $perPage, Current page: $page\n";
            $debug .= "Paginated count: " . $pagination->count() . "\n";
            if ($pagination->count() > 0) {
                $debug .= "First paginated item: " . $pagination->first()->title() . "\n";
            }

            // Get related tags for drilling down
            $relatedTags = [];
            $combinableTags = [];

            if (count($filterTags) === 1) {
                foreach ($pages as $pageItem) {
                    $pageTags = $pageItem->tags()->split(',');
                    foreach ($pageTags as $tag) {
                        $tag = trim($tag);
                        $tagLower = mb_strtolower($tag);
                        if (!empty($tag) && $tagLower !== mb_strtolower($filterTags[0])) {
                            if (!isset($combinableTags[$tag])) {
                                $combinableTags[$tag] = 0;
                            }
                            $combinableTags[$tag]++;
                        }
                    }
                }
                arsort($combinableTags);
                $relatedTags = array_slice($combinableTags, 0,
                    option('yourusername.tag-garden.related.tag-limit', 10),
                    true
                );
            } else {
                $allRelatedTags = [];
                foreach ($pages as $pageItem) {
                    $pageTags = $pageItem->tags()->split(',');
                    foreach ($pageTags as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag) && !in_array(mb_strtolower($tag), array_map('mb_strtolower', $filterTags))) {
                            if (!isset($allRelatedTags[$tag])) {
                                $allRelatedTags[$tag] = 0;
                            }
                            $allRelatedTags[$tag]++;
                        }
                    }
                }
                arsort($allRelatedTags);
                $relatedTags = array_slice($allRelatedTags, 0,
                    option('yourusername.tag-garden.related.tag-limit', 10),
                    true
                );
            }

            // Calculate pagination ONCE - before we need it
            $perPage = 20;
            $total = $pages->count();
            $pagination = $pages->paginate($perPage, ['page' => $page]);

            $debug .= "\n=== PAGINATION ===\n";
            $debug .= "Total pages: $total, Per page: $perPage, Current page: $page\n";
            $debug .= "Paginated count: " . $pagination->count() . "\n";
            if ($pagination->count() > 0) {
                $debug .= "First paginated item: " . $pagination->first()->title() . "\n";
            }

            // Calculate group stats
            $debug .= "\n=== GROUP STATS DEBUG ===\n";
            $debug .= "Pages count before group stats: " . $pages->count() . "\n";

            $groupStats = [];
            foreach (['garden', 'soil', 'work', 'about'] as $group) {
                $groupDef = Helpers::getGroupDefinition($group);
                $debug .= "Group '$group' definition: " . ($groupDef ? 'EXISTS' : 'NULL') . "\n";
                if ($groupDef && isset($groupDef['types'])) {
                    $debug .= "  Types for $group: " . implode(', ', $groupDef['types']) . "\n";
                    $count = $pages->filter(function($p) use ($groupDef) {
                        $template = $p->intendedTemplate()->name();
                        return in_array($template, $groupDef['types']);
                    })->count();
                    $debug .= "  Count: $count\n";

                    if ($count > 0) {
                        $groupStats[$group] = [
                            'count' => $count,
                            'def' => $groupDef
                        ];
                    }
                }
            }
            $debug .= "Final groupStats: " . count($groupStats) . " groups\n";
            $debug .= "groupStats structure:\n" . print_r($groupStats, true) . "\n";

            // Calculate type stats (for the active group or all groups) - ONLY ONCE
            $typeStats = [];
            $typesToShow = [];

            if ($groupFilter || $autoDetectedGroup) {
                // Show types for the active group
                $activeGroup = $groupFilter ?? $autoDetectedGroup;
                $groupDef = Helpers::getGroupDefinition($activeGroup);
                if ($groupDef && isset($groupDef['types'])) {
                    $typesToShow = $groupDef['types'];
                }
            } else {
                // Show all types from all groups
                foreach (['garden', 'soil', 'work', 'about'] as $group) {
                    $groupDef = Helpers::getGroupDefinition($group);
                    if ($groupDef && isset($groupDef['types'])) {
                        $typesToShow = array_merge($typesToShow, $groupDef['types']);
                    }
                }
            }

            foreach ($typesToShow as $type) {
                $count = $pages->filterBy('intendedTemplate', $type)->count();
                if ($count > 0) {
                    $typeStats[$type] = [
                        'count' => $count,
                        'group' => Helpers::getGroupForType($type)
                    ];
                }
            }

            // Calculate growth statistics
            $growthStats = ['seedling' => 0, 'budding' => 0, 'evergreen' => 0, 'wilting' => 0];
            foreach ($pages as $p) {
                $status = $p->growth_status()->value();
                if (isset($growthStats[$status])) {
                    $growthStats[$status]++;
                }
            }

            // Calculate length statistics
            $lengthStats = ['quick' => 0, 'short' => 0, 'medium' => 0, 'long' => 0, 'deep' => 0];
            $totalWords = 0;
            foreach ($pages as $p) {
                $wordCount = $p->wordCount();
                $totalWords += $wordCount;
                $category = Helpers::getLengthCategory($wordCount);
                if (isset($lengthStats[$category])) {
                    $lengthStats[$category]++;
                }
            }
            $avgWords = $pages->count() > 0 ? round($totalWords / $pages->count()) : 0;

            // Get all available groups
            $groups = [];
            $groupDefinitions = [
                'garden' => Helpers::getGroupDefinition('garden'),
                'soil' => Helpers::getGroupDefinition('soil'),
                'work' => Helpers::getGroupDefinition('work'),
                'about' => Helpers::getGroupDefinition('about'),
            ];
            foreach ($groupDefinitions as $key => $def) {
                if ($def) {
                    $groups[$key] = $def;
                }
            }

            // Create virtual page
            $virtualPage = Page::factory([
                'slug' => 'tag-' . implode('-', $filterTags),
                'template' => 'tag',
                'content' => [
                    'title' => count($filterTags) === 1
                        ? 'Tag: ' . $filterTags[0]
                        : 'Tags: ' . implode(' + ', $filterTags),
                ]
            ]);

            // Prepare template data
            $templateData = [
                // DEBUG DATA
                'routeDebug' => $debug,

                // Core data
                'filterTags' => $filterTags,
                'tagPages' => $pagination,      // NEW unique name
                'allPages' => $pagination,   // Backup for compatibility
                'pagination' => $pagination, // For pagination UI
                'relatedTags' => $relatedTags,
                'tagCount' => $total,
                'total' => $total,

                // Current state
                'sort' => $sort,
                'logic' => $logic,
                'groupFilter' => $groupFilter,
                'typeFilter' => $typeFilter,
                'autoDetectedGroup' => $autoDetectedGroup,
                'currentPage' => $page,
                'perPage' => $perPage,

                // Group and type filter stats
                'groupStats' => $groupStats,
                'typeStats' => $typeStats,

                // Statistics (from all pages)
                'growthStats' => $growthStats,
                'lengthStats' => $lengthStats,
                'avgWords' => $avgWords,

                // UI options
                'sortMethods' => Helpers::getSortMethods(),
                'groups' => $groups,

                // Helper functions for templates
                'getTagUrl' => function($tag) {
                    return url('tags/' . urlencode($tag));
                },

                'getCombinedTagUrl' => function($additionalTag) use ($filterTags) {
                    $allTags = array_merge($filterTags, [$additionalTag]);
                    return url('tags/' . Helpers::tagsToUrl($allTags));
                },

                'isActiveSort' => function($method) use ($sort) {
                    return $sort === $method;
                },

                'isActiveGroup' => function($group) use ($groupFilter, $autoDetectedGroup) {
                    return $groupFilter === $group || $autoDetectedGroup === $group;
                },

                'isActiveType' => function($type) use ($typeFilter) {
                    return $typeFilter === $type;
                },

                'getGrowthDefinition' => function($status) {
                    return Helpers::getGrowthDefinition($status);
                },

                'getLengthLabel' => function($category) {
                    return Helpers::getLengthLabel($category);
                },
            ];

            // DEBUG: Check what we're about to pass
            $debug .= "\n=== TEMPLATE DATA CHECK ===\n";
            $debug .= "filterTags in templateData: " . print_r($templateData['filterTags'], true) . "\n";
            $debug .= "pages count in templateData: " . $templateData['tagPages']->count() . "\n";
            $templateData['routeDebug'] = $debug;
            $debug .= "\n=== CONFIG CHECK ===\n";
            $debug .= "sort.methods config: " . (option('yourusername.tag-garden.sort.methods') ? 'EXISTS' : 'NULL') . "\n";
            $debug .= "group.definitions config: " . (option('yourusername.tag-garden.group.definitions') ? 'EXISTS' : 'NULL') . "\n";

            // Check ALL options with our prefix
            $allOptions = [];
            foreach (kirby()->options() as $key => $value) {
                if (strpos($key, 'yourusername.tag-garden') === 0) {
                    $allOptions[$key] = is_array($value) ? 'ARRAY' : (is_string($value) ? 'STRING' : gettype($value));
                }
            }
            $debug .= "All tag-garden options found: " . print_r($allOptions, true) . "\n";

            // Render with data
            return $virtualPage->render($templateData);          }
    ],

    /**
     * Tag API Route (optional)
     *
     * Returns tag data as JSON for AJAX requests
     * URL: /api/tags/{tag}
     */
    [
        'pattern' => 'api/tags/(:any)',
        'action' => function(string $tagString) {
            // Parse tags
            $tags = Helpers::urlToTags($tagString);
            $tags = array_map([Helpers::class, 'sanitizeTag'], $tags);
            $tags = array_filter($tags);

            if (empty($tags)) {
                return Response::json(['error' => 'No valid tags provided'], 400);
            }

            // Get pages with these tags
            $pages = kirby()->collection('pages.byTags', [
                'tags' => $tags,
                'logic' => get('logic', 'OR'),
                'sort' => get('sort', 'tended'),
            ]);

            // Get related tags
            $relatedTags = kirby()->collection('tags.related', [
                'tag' => $tags[0],
                'limit' => 10
            ]);

            // Format response
            $data = [
                'tags' => $tags,
                'count' => $pages->count(),
                'pages' => $pages->values(function($page) {
                    return [
                        'title' => $page->title()->value(),
                        'url' => $page->url(),
                        'excerpt' => $page->text()->excerpt(200),
                        'readingTime' => $page->readingTimeFormatted(),
                        'growthStatus' => $page->growth_status()->value(),
                        'tags' => $page->tags()->split(','),
                    ];
                }),
                'relatedTags' => array_keys($relatedTags),
            ];

            return Response::json($data);
        }
    ],

    /**
     * All Tags API Route (optional)
     *
     * Returns all tags with counts as JSON
     * URL: /api/tags
     */
    [
        'pattern' => 'api/tags',
        'action' => function() {
            $tags = kirby()->collection('tags.all', [
                'sortBy' => get('sortBy', 'count'),
                'direction' => get('direction', 'desc'),
            ]);

            // Format response
            $data = [
                'total' => count($tags),
                'tags' => array_map(function($tag, $count) {
                    return [
                        'tag' => $tag,
                        'count' => $count,
                        'url' => url('tags/' . urlencode($tag)),
                    ];
                }, array_keys($tags), $tags),
            ];

            return Response::json($data);
        }
    ],

];
