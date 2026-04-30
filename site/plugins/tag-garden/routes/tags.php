<?php

/**
 * Tag Garden Routes
 *
 * Simplified routing for tag pages with AND-only logic.
 * - Comma (,) separates tags (always AND)
 * - Tags are slugified (lowercase, hyphens for spaces)
 * - Canonical URLs enforced (alphabetically sorted, slugified)
 *
 * Routes:
 * - /tags                      → Tags index (all tags)
 * - /tags/web-development      → Single tag
 * - /tags/design,web-development → Multiple tags (AND logic)
 * - /tags?group=garden         → Tags index filtered by group
 * - /tags/design?sort=planted  → Single tag with sort parameter
 *
 * @version 2.0.0
 */

use jonathanstephens\TagGarden\Helpers;
use Kirby\Toolkit\Str;

return [

    /**
     * Tags Index Route
     *
     * Displays all tags with optional filtering
     */
    [
        'pattern' => 'tags',
        'action' => function() {
            // Check if a tags page exists in content
            $tagsPage = page('tags');

            if ($tagsPage) {
                return $tagsPage;
            }

            // Create virtual page
            return Page::factory([
                'slug' => 'tags',
                'template' => 'tags',
                'content' => [
                    'title' => 'Tags',
                ]
            ]);
        }
    ],

    /**
     * Single/Multiple Tag Route (AND logic only)
     *
     * Displays content filtered by one or more tags
     * All tags must be present (AND logic)
     */
    [
        'pattern' => 'tags/(:all)',
        'action' => function(string $tagString) {
            // Get query parameters
            $sort = get('sort', option('jonathanstephens.tag-garden.default.sort', 'tended'));
            $groupFilter = get('group');

            // Parse tags from URL
            $filterTags = Helpers::parseTagsFromUrl($tagString);

            if (empty($filterTags)) {
                go('tags');
            }

            // Generate canonical URL and redirect if needed
            $canonicalPath = Helpers::canonicalTagUrl($filterTags);
            $currentPath = trim(kirby()->request()->path()->toString(), '/');

            if ($currentPath !== $canonicalPath) {
                return go(url($canonicalPath), 301);
            }

            // Get pages with ALL tags (AND logic)
            $pages = Helpers::getPagesByTags($filterTags);

            // Apply group filter if specified
            if ($groupFilter) {
                $groups = option('jonathanstephens.tag-garden.content.groups', []);
                $types = $groups[$groupFilter] ?? [];

                if (!empty($types)) {
                    $pages = $pages->filter(function($page) use ($types) {
                        $template = $page->intendedTemplate()->name();
                        return in_array($template, $types);
                    });
                }
            }

            // Apply sorting
            $pages = Helpers::sortPages($pages, $sort);

            // Get related tags for further exploration
            $relatedTags = [];

            if (count($filterTags) === 1) {
                // Single tag: get related tags from collection
                $relatedTags = kirby()->collection('tags.related', [
                    'tag' => $filterTags[0],
                    'limit' => option('jonathanstephens.tag-garden.related.tag-limit', 10)
                ]);
            } else {
                // Multiple tags: find tags from the filtered pages
                $allRelatedTags = [];
                foreach ($pages as $pageItem) {
                    $pageTags = $pageItem->tags()->split(',');
                    foreach ($pageTags as $tag) {
                        $tag = trim($tag);
                        $tagSlug = Str::slug($tag);

                        // Skip if it's one of our filter tags
                        $isFilterTag = false;
                        foreach ($filterTags as $filterTag) {
                            if ($tagSlug === $filterTag) {
                                $isFilterTag = true;
                                break;
                            }
                        }

                        if (!empty($tag) && !$isFilterTag) {
                            if (!isset($allRelatedTags[$tag])) {
                                $allRelatedTags[$tag] = 0;
                            }
                            $allRelatedTags[$tag]++;
                        }
                    }
                }
                arsort($allRelatedTags);
                $relatedTags = array_slice(
                    $allRelatedTags,
                    0,
                    option('jonathanstephens.tag-garden.related.tag-limit', 10),
                    true
                );
            }

            // Calculate group statistics
            $groupStats = [];
            $groups = option('jonathanstephens.tag-garden.content.groups', []);

            foreach ($groups as $groupKey => $types) {
                $count = $pages->filter(function($p) use ($types) {
                    $template = $p->intendedTemplate()->name();
                    return in_array($template, $types);
                })->count();

                if ($count > 0) {
                    $groupDef = Helpers::getGroupDefinition($groupKey);
                    $groupStats[$groupKey] = [
                        'count' => $count,
                        'def' => $groupDef
                    ];
                }
            }

            // Calculate growth statistics
            $growthStats = [
                'sown' => 0,
                'sprouting' => 0,
                'rooting' => 0,
                'crowning' => 0,
                'evergreen' => 0,
            ];
            foreach ($pages as $p) {
                $status = $p->Growthstatus()->value();
                if (isset($growthStats[$status])) {
                    $growthStats[$status]++;
                }
            }

            // Get all available groups for UI
            $allGroups = [];
            foreach (array_keys($groups) as $key) {
                $def = Helpers::getGroupDefinition($key);
                if ($def) {
                    $allGroups[$key] = $def;
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
                // Core data
                'filterTags' => $filterTags,
                'pages' => $pages,
                'relatedTags' => $relatedTags,
                'tagCount' => $pages->count(),

                // Current state
                'sort' => $sort,
                'groupFilter' => $groupFilter,

                // Statistics
                'groupStats' => $groupStats,
                'growthStats' => $growthStats,

                // UI options
                'sortMethods' => option('jonathanstephens.tag-garden.sort.methods', []),
                'groups' => $allGroups,

                // Helper functions for templates
                'getTagUrl' => function($tag) {
                    return url('tags/' . Str::slug($tag));
                },

                'getCombinedTagUrl' => function($additionalTag) use ($filterTags) {
                    $allTags = array_merge($filterTags, [Str::slug($additionalTag)]);
                    sort($allTags);
                    return url('tags/' . implode(',', $allTags));
                },

                'isActiveSort' => function($method) use ($sort) {
                    return $sort === $method;
                },

                'isActiveGroup' => function($group) use ($groupFilter) {
                    return $groupFilter === $group;
                },

                'getGrowthDefinition' => function($status) {
                    return Helpers::getGrowthDefinition($status);
                },
            ];

            // Render with data
            return $virtualPage->render($templateData);
        }
    ],

];
