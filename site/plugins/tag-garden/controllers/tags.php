<?php

/**
 * Tags Index Controller
 *
 * Simplified controller for the tags index page.
 * Shows all tags with optional filtering by group or growth status.
 *
 * @version 2.0.0
 */

use Yourusername\TagGarden\Helpers;

return function ($kirby, $page) {

    // Get query parameters
    $groupFilter = get('group');
    $growthFilter = get('growth');

    // Get tags based on filters
    if ($groupFilter) {
        $tags = $kirby->collection('tags.byGroup', ['group' => $groupFilter]);
    } elseif ($growthFilter) {
        $tags = $kirby->collection('tags.byGrowth', ['status' => $growthFilter]);
    } else {
        $tags = $kirby->collection('tags.all');
    }

    // Sort tags alphabetically or by count
    $tagSort = get('tagSort', 'count');
    if ($tagSort === 'alpha') {
        ksort($tags);
    } else {
        arsort($tags); // By count, descending
    }

    // Get recently tended pages
    $recentlyTended = $kirby->collection('pages.recentlyTended', ['limit' => 5]);

    // Get recently planted pages
    $recentlyPlanted = $kirby->collection('pages.recentlyPlanted', ['limit' => 5]);

    // Calculate total pages with tags
    $totalTaggedPages = $kirby->site()->index()
        ->filterBy('tags', '!=', '')
        ->count();

    // Get all available groups for filtering UI
    $groups = [];
    $groupKeys = array_keys(option('yourusername.tag-garden.content.groups', []));
    foreach ($groupKeys as $key) {
        $def = Helpers::getGroupDefinition($key);
        if ($def) {
            $groups[$key] = $def;
        }
    }

    // Get all available growth statuses for filtering UI
    $growthStatuses = [];
    $statusKeys = option('yourusername.tag-garden.growth.statuses', []);
    foreach ($statusKeys as $key) {
        $def = Helpers::getGrowthDefinition($key);
        if ($def) {
            $growthStatuses[$key] = $def;
        }
    }

    return [
        // Core data
        'tags' => $tags,
        'totalTags' => count($tags),
        'totalTaggedPages' => $totalTaggedPages,

        // Current state
        'groupFilter' => $groupFilter,
        'growthFilter' => $growthFilter,
        'tagSort' => $tagSort,

        // UI options
        'groups' => $groups,
        'growthStatuses' => $growthStatuses,

        // Featured content
        'recentlyTended' => $recentlyTended,
        'recentlyPlanted' => $recentlyPlanted,

        // Helper functions
        'getTagUrl' => function($tag) {
            return url('tags/' . \Kirby\Toolkit\Str::slug($tag));
        },

        'isActiveGroup' => function($groupKey) use ($groupFilter) {
            return $groupFilter === $groupKey;
        },

        'isActiveGrowth' => function($statusKey) use ($growthFilter) {
            return $growthFilter === $statusKey;
        },
    ];
};
