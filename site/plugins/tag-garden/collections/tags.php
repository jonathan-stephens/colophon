<?php

/**
 * Tag Garden Collections
 *
 * Global collections that can be accessed via $kirby->collection('collection-name')
 * These collections provide reusable queries for tags and tagged content.
 *
 * Usage examples:
 * - $kirby->collection('tags.all')
 * - $kirby->collection('tags.popular', ['limit' => 10])
 * - $kirby->collection('tags.byTheme', ['theme' => 'topic'])
 *
 * @version 1.0.0
 */

use TagGarden\Helpers;

return [

    /**
     * Get all unique tags with usage counts
     *
     * Returns an associative array of tag => count
     * Sorted by count (descending) by default
     *
     * Options:
     * - sortBy: 'count' (default) or 'alpha' (alphabetical)
     * - direction: 'desc' (default) or 'asc'
     * - minCount: Minimum usage count to include (default: 1)
     *
     * @return array
     */
    'tags.all' => function ($kirby, $options = []) {
        $sortBy = $options['sortBy'] ?? 'count';
        $direction = $options['direction'] ?? 'desc';
        $minCount = $options['minCount'] ?? option('yourusername.tag-garden.cloud.min-count', 1);

        // Get all tags with counts
        $tags = Helpers::getAllTags();

        // Filter by minimum count
        if ($minCount > 1) {
            $tags = array_filter($tags, function($count) use ($minCount) {
                return $count >= $minCount;
            });
        }

        // Sort
        if ($sortBy === 'alpha') {
            ksort($tags);
            if ($direction === 'desc') {
                $tags = array_reverse($tags, true);
            }
        } else {
            // Sort by count
            if ($direction === 'desc') {
                arsort($tags);
            } else {
                asort($tags);
            }
        }

        return $tags;
    },

    /**
     * Get popular tags (most used)
     *
     * Options:
     * - limit: Number of tags to return (default: 10)
     *
     * @return array
     */
    'tags.popular' => function ($kirby, $options = []) {
        $limit = $options['limit'] ?? 5;

        $tags = $kirby->collection('tags.all', ['sortBy' => 'count', 'direction' => 'desc']);

        return array_slice($tags, 0, $limit, true);
    },

    /**
     * Get tags filtered by theme
     *
     * Options:
     * - theme: Theme key (topic, medium, status, audience)
     *
     * @return array
     */
    'tags.byTheme' => function ($kirby, $options = []) {
        $theme = $options['theme'] ?? null;

        if (!$theme) {
            return [];
        }

        // Get all pages with this theme
        $pages = $kirby->site()->index()->filterBy('tag_theme', $theme);

        // Extract their tags
        $tags = [];
        foreach ($pages as $page) {
            $pageTags = $page->tags()->split(',');
            foreach ($pageTags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    if (!isset($tags[$tag])) {
                        $tags[$tag] = 0;
                    }
                    $tags[$tag]++;
                }
            }
        }

        // Sort by count
        arsort($tags);

        return $tags;
    },

    /**
     * Get tags filtered by content group
     *
     * Options:
     * - group: Group key (garden, soil, work, about)
     *
     * @return array
     */
    'tags.byGroup' => function ($kirby, $options = []) {
        $group = $options['group'] ?? null;

        if (!$group) {
            return [];
        }

        // Get group definition to find content types
        $groupDef = Helpers::getGroupDefinition($group);
        if (!$groupDef || !isset($groupDef['types'])) {
            return [];
        }

        // Get all pages of these content types
        $pages = $kirby->site()->index()->filter(function($page) use ($groupDef) {
            $template = $page->intendedTemplate()->name();
            return in_array($template, $groupDef['types']);
        });

        // Extract their tags
        $tags = [];
        foreach ($pages as $page) {
            $pageTags = $page->tags()->split(',');
            foreach ($pageTags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    if (!isset($tags[$tag])) {
                        $tags[$tag] = 0;
                    }
                    $tags[$tag]++;
                }
            }
        }

        // Sort by count
        arsort($tags);

        return $tags;
    },

    /**
     * Get tags filtered by growth status
     *
     * Options:
     * - status: Growth status (seedling, budding, evergreen, wilting)
     *
     * @return array
     */
    'tags.byGrowth' => function ($kirby, $options = []) {
        $status = $options['status'] ?? null;

        if (!$status) {
            return [];
        }

        // Get all pages with this growth status
        $pages = $kirby->site()->index()->filterBy('growth_status', $status);

        // Extract their tags
        $tags = [];
        foreach ($pages as $page) {
            $pageTags = $page->tags()->split(',');
            foreach ($pageTags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    if (!isset($tags[$tag])) {
                        $tags[$tag] = 0;
                    }
                    $tags[$tag]++;
                }
            }
        }

        // Sort by count
        arsort($tags);

        return $tags;
    },

    /**
     * Get related tags for a given tag
     *
     * Finds tags that commonly appear together with the specified tag.
     *
     * Options:
     * - tag: The tag to find related tags for (required)
     * - limit: Number of related tags to return (default: 10)
     * - excludeSelf: Whether to exclude the original tag (default: true)
     *
     * @return array
     */
    'tags.related' => function ($kirby, $options = []) {
        $tag = $options['tag'] ?? null;
        $limit = $options['limit'] ?? 10;
        $excludeSelf = $options['excludeSelf'] ?? true;

        if (!$tag) {
            return [];
        }

        // Get all pages with this tag
        $pages = Helpers::getPagesByTags($tag);

        // Collect all tags from these pages
        $relatedTags = [];
        foreach ($pages as $page) {
            $pageTags = $page->tags()->split(',');
            foreach ($pageTags as $relatedTag) {
                $relatedTag = trim($relatedTag);
                if (!empty($relatedTag)) {
                    // Skip the original tag if excludeSelf is true
                    if ($excludeSelf && $relatedTag === $tag) {
                        continue;
                    }

                    if (!isset($relatedTags[$relatedTag])) {
                        $relatedTags[$relatedTag] = 0;
                    }
                    $relatedTags[$relatedTag]++;
                }
            }
        }

        // Sort by count
        arsort($relatedTags);

        // Limit results
        return array_slice($relatedTags, 0, $limit, true);
    },

    /**
     * Get pages by tag(s) with optional sorting
     *
     * Options:
     * - tags: Single tag string or array of tags (required)
     * - logic: 'OR' (any tag) or 'AND' (all tags) - default: 'OR'
     * - sort: Sort method (planted, tended, notable, length, growth, title)
     * - direction: Sort direction (asc, desc)
     * - limit: Maximum number of pages to return (0 = unlimited)
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.byTags' => function ($kirby, $options = []) {
        $tags = $options['tags'] ?? [];
        $logic = $options['logic'] ?? 'OR';
        $sort = $options['sort'] ?? 'tended';
        $direction = $options['direction'] ?? 'desc';
        $limit = $options['limit'] ?? 0;

        // Get pages by tags
        $pages = Helpers::getPagesByTags($tags, $logic);

        // Sort pages
        $pages = Helpers::sortPages($pages, $sort, $direction);

        // Apply limit if specified
        if ($limit > 0) {
            $pages = $pages->limit($limit);
        }

        return $pages;
    },

    /**
     * Get recently planted pages (newest content)
     *
     * Options:
     * - limit: Number of pages to return (default: 5)
     * - tags: Optional array of tags to filter by
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.recentlyPlanted' => function ($kirby, $options = []) {
        $limit = $options['limit'] ?? 5;
        $tags = $options['tags'] ?? null;

        if ($tags) {
            $pages = Helpers::getPagesByTags($tags);
        } else {
            $pages = $kirby->site()->index();
        }

        return $pages
            ->filterBy('date_planted', '!=', '')
            ->sortBy('date_planted', 'desc')
            ->limit($limit);
    },

    /**
     * Get recently tended pages (recently updated)
     *
     * Options:
     * - limit: Number of pages to return (default: 5)
     * - tags: Optional array of tags to filter by
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.recentlyTended' => function ($kirby, $options = []) {
        $limit = $options['limit'] ?? 5;
        $tags = $options['tags'] ?? null;

        if ($tags) {
            $pages = Helpers::getPagesByTags($tags);
        } else {
            $pages = $kirby->site()->index();
        }

        return $pages
            ->filterBy('last_tended', '!=', '')
            ->sortBy('last_tended', 'desc')
            ->limit($limit);
    },

    /**
     * Get notable/featured pages
     *
     * Options:
     * - limit: Number of pages to return (default: 5)
     * - tags: Optional array of tags to filter by
     * - sort: Sort method (default: 'tended')
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.notable' => function ($kirby, $options = []) {
        $limit = $options['limit'] ?? 5;
        $tags = $options['tags'] ?? null;
        $sort = $options['sort'] ?? 'tended';

        if ($tags) {
            $pages = Helpers::getPagesByTags($tags);
        } else {
            $pages = $kirby->site()->index();
        }

        $pages = $pages->filterBy('notable', true);
        $pages = Helpers::sortPages($pages, $sort);

        return $pages->limit($limit);
    },

    /**
     * Get pages by growth status
     *
     * Options:
     * - status: Growth status (required)
     * - limit: Number of pages to return (0 = unlimited)
     * - sort: Sort method (default: 'tended')
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.byGrowth' => function ($kirby, $options = []) {
        $status = $options['status'] ?? null;
        $limit = $options['limit'] ?? 0;
        $sort = $options['sort'] ?? 'tended';

        if (!$status) {
            return new \Kirby\Cms\Pages([]);
        }

        $pages = $kirby->site()->index()->filterBy('growth_status', $status);
        $pages = Helpers::sortPages($pages, $sort);

        if ($limit > 0) {
            $pages = $pages->limit($limit);
        }

        return $pages;
    },

];
