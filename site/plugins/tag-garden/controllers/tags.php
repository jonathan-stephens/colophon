<?php

/**
 * Tags Index Controller
 *
 * Prepares data for the tags index template (tags.php)
 * Shows all tags in a cloud/list view with filtering options
 *
 * Available template variables:
 * - $tags: Array of tag => count
 * - $sortedTags: Tags sorted for display
 * - $minCount: Minimum tag usage count in dataset
 * - $maxCount: Maximum tag usage count in dataset
 * - $sort: Current sort method
 * - $group: Current group filter (if any)
 * - $theme: Current theme filter (if any)
 * - $groups: Available content groups
 * - $themes: Available tag themes
 * - $sortMethods: Available sort methods
 *
 * @version 1.0.0
 */

use TagGarden\Helpers;

return function ($kirby, $page) {

    // Get query parameters
    $sort = get('sort', option('yourusername.tag-garden.default.sort', 'tended'));
    $group = get('group');
    $theme = get('theme');

    // Get tags based on filters
    if ($group) {
        $tags = $kirby->collection('tags.byGroup', ['group' => $group]);
    } elseif ($theme) {
        $tags = $kirby->collection('tags.byTheme', ['theme' => $theme]);
    } else {
        $tags = $kirby->collection('tags.all');
    }

    // Get tag statistics for cloud sizing
    $minCount = !empty($tags) ? min($tags) : 0;
    $maxCount = !empty($tags) ? max($tags) : 0;

    // Sort tags for display
    $sortedTags = $tags;
    if (get('tagSort') === 'alpha') {
        ksort($sortedTags);
    } else {
        // Default: sort by count (descending)
        arsort($sortedTags);
    }

    // Get available groups and themes for filtering UI
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

    $themes = [];
    $themeDefinitions = [
        'topic' => Helpers::getThemeDefinition('topic'),
        'medium' => Helpers::getThemeDefinition('medium'),
        'status' => Helpers::getThemeDefinition('status'),
        'audience' => Helpers::getThemeDefinition('audience'),
    ];
    foreach ($themeDefinitions as $key => $def) {
        if ($def) {
            $themes[$key] = $def;
        }
    }

    // Get sort methods for UI
    $sortMethods = Helpers::getSortMethods();

    // Get recently tended pages (for sidebar or featured section)
    $recentlyTended = $kirby->collection('pages.recentlyTended', ['limit' => 5]);

    // Get notable pages (for featured section)
    $notablePages = $kirby->collection('pages.notable', ['limit' => 5]);

    // Calculate total pages with tags
    $totalTaggedPages = $kirby->site()->index()->filterBy('tags', '!=', '')->count();

    // Get popular tags for quick navigation
    $popularTags = $kirby->collection('tags.popular', ['limit' => 10]);

    return [
        // Core data
        'tags' => $tags,
        'sortedTags' => $sortedTags,
        'minCount' => $minCount,
        'maxCount' => $maxCount,
        'totalTags' => count($tags),
        'totalTaggedPages' => $totalTaggedPages,

        // Current state
        'sort' => $sort,
        'group' => $group,
        'theme' => $theme,
        'activeFilter' => $group ?? $theme ?? null,

        // UI options
        'groups' => $groups,
        'themes' => $themes,
        'sortMethods' => $sortMethods,

        // Featured content
        'recentlyTended' => $recentlyTended,
        'notablePages' => $notablePages,
        'popularTags' => $popularTags,

        // Helper functions for templates
        'getTagFontSize' => function($count) use ($minCount, $maxCount) {
            return Helpers::getTagFontSize($count, $minCount, $maxCount);
        },

        'getTagUrl' => function($tag) {
            return url('tags/' . urlencode($tag));
        },

        'isActiveGroup' => function($groupKey) use ($group) {
            return $group === $groupKey;
        },

        'isActiveTheme' => function($themeKey) use ($theme) {
            return $theme === $themeKey;
        },
    ];
};
