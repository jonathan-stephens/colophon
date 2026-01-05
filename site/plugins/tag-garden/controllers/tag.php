<?php

/**
 * Single Tag Controller
 *
 * Prepares data for the single tag template (tag.php)
 * Shows content filtered by one or more tags
 *
 * Available template variables:
 * - $filterTags: Array of tag(s) being filtered
 * - $pages: Filtered pages collection
 * - $groupedPages: Pages grouped by content type/section
 * - $relatedTags: Related tags with counts
 * - $sort: Current sort method
 * - $logic: Tag filter logic (OR/AND)
 * - $tagCount: Total number of filtered pages
 * - $sortMethods: Available sort methods
 * - $growthStats: Statistics about growth statuses
 * - $lengthStats: Statistics about content length
 *
 * @version 1.0.0
 */

use TagGarden\Helpers;

return function ($kirby, $page) {

    // Check if data was already prepared by route
    // If the page is a virtual page from the route, it already has all the data
    // and we should not override it
    $slug = $page->slug();
    if (is_string($slug) && str_starts_with($slug, 'tag-')) {
        // This is a virtual page from the route, data is already prepared
        // Just return empty array to let route data through
        return [];
    }

    // Get filter tags from custom page property (set by route)
    $filterTags = $page->_filterTags ?? [];

    // Ensure it's an array
    if (!is_array($filterTags)) {
        $filterTags = !empty($filterTags) ? [$filterTags] : [];
    }

    // Get query parameters
    $sort = get('sort', option('yourusername.tag-garden.default.sort', 'tended'));
    $logic = get('logic', 'OR');
    $groupFilter = get('group');

    // Get filtered pages
    $pages = $kirby->collection('pages.byTags', [
        'tags' => $filterTags,
        'logic' => $logic,
        'sort' => $sort,
    ]);

    // Apply additional group filter if specified
    if ($groupFilter) {
        $groupDef = Helpers::getGroupDefinition($groupFilter);
        if ($groupDef && isset($groupDef['types'])) {
            $pages = $pages->filter(function($page) use ($groupDef) {
                $template = $page->intendedTemplate()->name();
                return in_array($template, $groupDef['types']);
            });
        }
    }

    // Group pages by section/template for organized display
    $groupedPages = [];
    foreach ($pages as $p) {
        $group = $p->contentGroup() ?? 'other';
        if (!isset($groupedPages[$group])) {
            $groupedPages[$group] = [];
        }
        $groupedPages[$group][] = $p;
    }

    // Sort the groups in a logical order
    $groupOrder = ['garden', 'soil', 'work', 'about', 'other'];
    uksort($groupedPages, function($a, $b) use ($groupOrder) {
        $posA = array_search($a, $groupOrder);
        $posB = array_search($b, $groupOrder);
        $posA = $posA === false ? 999 : $posA;
        $posB = $posB === false ? 999 : $posB;
        return $posA - $posB;
    });

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
                if (!empty($tag) && !in_array($tag, $filterTags)) {
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

    // Calculate statistics about filtered pages

    // Growth status statistics
    $growthStats = [
        'seedling' => 0,
        'budding' => 0,
        'evergreen' => 0,
        'wilting' => 0,
    ];
    foreach ($pages as $p) {
        $status = $p->growth_status()->value();
        if (isset($growthStats[$status])) {
            $growthStats[$status]++;
        }
    }

    // Length statistics
    $lengthStats = [
        'quick' => 0,
        'short' => 0,
        'medium' => 0,
        'long' => 0,
        'deep' => 0,
    ];
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

    // Get sort methods for UI
    $sortMethods = Helpers::getSortMethods();

    // Get all available groups for filtering UI
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

    return [
        // Core data
        'filterTags' => $filterTags,
        'pages' => $pages,
        'groupedPages' => $groupedPages,
        'relatedTags' => $relatedTags,
        'tagCount' => $pages->count(),

        // Current state
        'sort' => $sort,
        'logic' => $logic,
        'groupFilter' => $groupFilter,

        // Statistics
        'growthStats' => $growthStats,
        'lengthStats' => $lengthStats,
        'avgWords' => $avgWords,

        // UI options
        'sortMethods' => $sortMethods,
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

        'getGrowthDefinition' => function($status) {
            return Helpers::getGrowthDefinition($status);
        },

        'getLengthLabel' => function($category) {
            return Helpers::getLengthLabel($category);
        },

        // Pagination helper (if needed)
        'getPaginatedPages' => function($limit = 20) use ($pages) {
            $page = get('page', 1);
            return $pages->paginate($limit, ['page' => $page]);
        },
    ];
};
