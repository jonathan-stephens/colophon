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
            // Get query parameters
            $sort = get('sort', option('yourusername.tag-garden.default.sort', 'tended'));
            $logic = get('logic', 'OR');

            // Parse tags from URL
            $tags = Helpers::urlToTags($tagString);

            // Sanitize tags
            $tags = array_map([Helpers::class, 'sanitizeTag'], $tags);
            $tags = array_filter($tags);

            if (empty($tags)) {
                // No valid tags, redirect to tags index
                go('tags');
            }

            // Get pages with these tags
            $pages = kirby()->collection('pages.byTags', [
                'tags' => $tags,
                'logic' => $logic,
                'sort' => $sort,
            ]);

            // Get related tags
            $relatedTags = [];
            if (count($tags) === 1) {
                // For single tag, show related tags
                $relatedTags = kirby()->collection('tags.related', [
                    'tag' => $tags[0],
                    'limit' => option('yourusername.tag-garden.related.tag-limit', 5)
                ]);
            } else {
                // For multiple tags, collect related tags from all pages
                $allRelatedTags = [];
                foreach ($pages as $page) {
                    $pageTags = $page->tags()->split(',');
                    foreach ($pageTags as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag) && !in_array($tag, $tags)) {
                            if (!isset($allRelatedTags[$tag])) {
                                $allRelatedTags[$tag] = 0;
                            }
                            $allRelatedTags[$tag]++;
                        }
                    }
                }
                arsort($allRelatedTags);
                $relatedTags = array_slice($allRelatedTags, 0,
                    option('yourusername.tag-garden.related.tag-limit', 5),
                    true
                );
            }

            // Create virtual page for this tag view
            return Page::factory([
                'slug' => 'tag-' . implode('-', $tags),
                'template' => 'tag',
                'model' => 'tag',
                'content' => [
                    'title' => count($tags) === 1
                        ? 'Tag: ' . $tags[0]
                        : 'Tags: ' . implode(' + ', $tags),
                    'filterTags' => $tags,
                    'pages' => $pages,
                    'relatedTags' => $relatedTags,
                    'sort' => $sort,
                    'logic' => $logic,
                    'tagCount' => $pages->count(),
                ]
            ]);
        }
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
