<?php

/**
 * Single/Multiple Tag Controller
 *
 * Simplified controller for tag filtering pages.
 * Shows content filtered by one or more tags (always AND logic).
 *
 * @version 2.0.0
 */

use Yourusername\TagGarden\Helpers;

return function ($kirby, $page) {

    // If this is a virtual page from route, data is already prepared
    // Just return empty array to let route data through
    $slug = $page->slug();
    if (is_string($slug) && str_starts_with($slug, 'tag-')) {
        return [];
    }

    // Fallback for non-route usage
    $filterTags = $filterTags ?? [];

    if (!is_array($filterTags)) {
        $filterTags = !empty($filterTags) ? [$filterTags] : [];
    }

    // Query params
    $sort = get('sort', option('yourusername.tag-garden.default.sort', 'tended'));
    $groupFilter = get('group');

    // Get pages with all tags (AND logic)
    $pages = $kirby->collection('pages.byTags', [
        'tags' => $filterTags,
        'sort' => $sort,
    ]);

    // Optional group filter
    if ($groupFilter) {
        $groups = option('yourusername.tag-garden.content.groups', []);
        $types = $groups[$groupFilter] ?? [];

        if (!empty($types)) {
            $pages = $pages->filter(function ($page) use ($types) {
                return in_array(
                    $page->intendedTemplate()->name(),
                    $types,
                    true
                );
            });
        }
    }

    // Get related tags
    $relatedTags = [];
    if (count($filterTags) === 1) {
        $relatedTags = $kirby->collection('tags.related', [
            'tag' => $filterTags[0],
            'limit' => option('yourusername.tag-garden.related.tag-limit', 10)
        ]);
    } else {
        // For multiple tags, collect related tags from filtered pages
        $allRelatedTags = [];
        foreach ($pages as $p) {
            $pageTags = $p->tags()->split(',');
            foreach ($pageTags as $tag) {
                $tag = trim($tag);
                $tagLower = mb_strtolower($tag);

                // Skip if it's one of the filter tags
                $isFilterTag = false;
                foreach ($filterTags as $filterTag) {
                    if ($tagLower === mb_strtolower($filterTag)) {
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
            option('yourusername.tag-garden.related.tag-limit', 10),
            true
        );
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
        $status = $p->growth_status()->value();
        if (isset($growthStats[$status])) {
            $growthStats[$status]++;
        }
    }

    // Calculate group statistics
    $groupStats = [];
    $groups = option('yourusername.tag-garden.content.groups', []);

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

    // Get sort methods
    $sortMethods = option('yourusername.tag-garden.sort.methods', []);

    // Get all available groups for filtering UI
    $allGroups = [];
    foreach (array_keys($groups) as $key) {
        $def = Helpers::getGroupDefinition($key);
        if ($def) {
            $allGroups[$key] = $def;
        }
    }

    return [
        // Core data
        'filterTags' => $filterTags,
        'pages' => $pages,
        'relatedTags' => $relatedTags,
        'tagCount' => $pages->count(),

        // Current state
        'sort' => $sort,
        'groupFilter' => $groupFilter,

        // Statistics
        'growthStats' => $growthStats,
        'groupStats' => $groupStats,

        // UI options
        'sortMethods' => $sortMethods,
        'groups' => $allGroups,

        // Helper functions
        'getTagUrl' => function($tag) {
            return url('tags/' . \Kirby\Toolkit\Str::slug($tag));
        },

        'getCombinedTagUrl' => function($additionalTag) use ($filterTags) {
            $allTags = array_merge($filterTags, [$additionalTag]);
            $slugs = array_map(function($tag) {
                return \Kirby\Toolkit\Str::slug($tag);
            }, $allTags);
            sort($slugs);
            return url('tags/' . implode(',', $slugs));
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
};
