<?php

/**
 * Tag Garden Collections
 *
 * Simplified collections for tag-based content exploration.
 * All collections accessible via $kirby->collection('collection-name')
 *
 * @version 2.0.0
 */

use Yourusername\TagGarden\Helpers;

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
     *
     * @return array
     */
    'tags.all' => function ($kirby, $options = []) {
        $sortBy = $options['sortBy'] ?? 'count';
        $direction = $options['direction'] ?? 'desc';

        // Get all tags with counts
        $tags = Helpers::getAllTags();

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
     * Get tags filtered by content group
     *
     * Options:
     * - group: Group key (garden, soil, work, about) - required
     *
     * @return array
     */
    'tags.byGroup' => function ($kirby, $options = []) {
        $group = $options['group'] ?? null;

        if (!$group) {
            return [];
        }

        // Get group definition to find content types
        $groups = option('yourusername.tag-garden.content.groups', []);
        $types = $groups[$group] ?? [];

        if (empty($types)) {
            return [];
        }

        // Get all pages of these content types
        $pages = $kirby->site()->index()->filter(function($page) use ($types) {
            $template = $page->intendedTemplate()->name();
            return in_array($template, $types);
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
     * - status: Growth status (sown, sprouting, rooting, crowning, evergreen) - required
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
     * - tag: The tag to find related tags for - required
     * - limit: Number of related tags to return (default: 10)
     *
     * @return array
     */
    'tags.related' => function ($kirby, $options = []) {
        $tag = $options['tag'] ?? null;
        $limit = $options['limit'] ?? 10;

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
                $relatedTagLower = mb_strtolower($relatedTag);
                $tagLower = mb_strtolower($tag);

                if (!empty($relatedTag) && $relatedTagLower !== $tagLower) {
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
     * - tags: Single tag string or array of tags - required
     * - sort: Sort method (planted, tended, growth) - default: 'tended'
     * - direction: Sort direction (asc, desc) - default: 'desc'
     * - limit: Maximum number of pages to return (0 = unlimited)
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.byTags' => function ($kirby, $options = []) {
        $tags = $options['tags'] ?? [];
        $sort = $options['sort'] ?? 'tended';
        $direction = $options['direction'] ?? 'desc';
        $limit = $options['limit'] ?? 0;

        // Get pages by tags (always AND logic)
        $pages = Helpers::getPagesByTags($tags);

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
     * - limit: Number of pages to return (default: 10)
     * - tags: Optional array of tags to filter by
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.recentlyPlanted' => function ($kirby, $options = []) {
        $limit = $options['limit'] ?? 10;
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
     * - limit: Number of pages to return (default: 10)
     * - tags: Optional array of tags to filter by
     *
     * @return \Kirby\Cms\Pages
     */
    'pages.recentlyTended' => function ($kirby, $options = []) {
        $limit = $options['limit'] ?? 10;
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
     * Get pages by growth status
     *
     * Options:
     * - status: Growth status - required
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
