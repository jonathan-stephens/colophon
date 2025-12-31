<?php

namespace TagGarden;

/**
 * Tag Garden Helpers
 *
 * Static helper methods for working with tags, content length, growth status,
 * and other plugin functionality throughout Kirby.
 *
 * Usage: TagGarden\Helpers::methodName($args)
 *
 * @version 1.0.0
 */
class Helpers {

    /**
     * Get a growth status definition
     *
     * @param string $status The status key (seedling, budding, evergreen, wilting)
     * @return array|null The status definition or null if not found
     */
     public static function getGrowthDefinition(?string $status): ?array
     {
         if (empty($status)) {
             return null;
         }

         $defaults = [
             'seedling' => [
                 'label' => 'Seedling',
                 'emoji' => '🌱',
                 'sort-order' => 1,
             ],
             'budding' => [
                 'label' => 'Budding',
                 'emoji' => '🪴',
                 'sort-order' => 2,
             ],
             'evergreen' => [
                 'label' => 'Evergreen',
                 'emoji' => '🌲',
                 'sort-order' => 3,
             ],
             'wilting' => [
                 'label' => 'Wilting',
                 'emoji' => '🥀',
                 'sort-order' => 4,
             ],
         ];

         $definitions = array_merge(
             $defaults,
             option('yourusername.tag-garden.growth.definitions', [])
         );

         return $definitions[$status] ?? null;
     }
    /**
     * Get a content group definition
     *
     * @param string $group The group key (garden, soil, work, about)
     * @return array|null The group definition or null if not found
     */
     public static function getGroupDefinition(string $group): ?array
     {
       $defaults = [
           'garden' => [
               'label' => 'Garden',
               'description' => 'Growing ideas and explorations',
               'types' => ['journal', 'essays', 'articles', 'books'],
           ],
           'soil' => [
               'label' => 'Soil',
               'description' => 'Foundations and background thinking',
               'types' => ['links', 'library', 'quotes'],
           ],
           'work' => [
               'label' => 'Work',
               'description' => 'Projects and case studies',
               'types' => ['work', 'experience', 'projects'],
           ],
           'about' => [
               'label' => 'About',
               'description' => 'Context and personal information',
               'types' => ['strengths', 'skills', 'about', 'now'],
           ],
       ];

         $groups = array_merge(
             $defaults,
             option('yourusername.tag-garden.groups', [])
         );

         return $groups[$group] ?? null;
     }

    /**
     * Get a theme definition
     *
     * @param string $theme The theme key (topic, medium, status, audience)
     * @return array|null The theme definition or null if not found
     */
     public static function getThemeDefinition(string $theme): ?array
     {
         $defaults = [
             'topic' => [
                 'label' => 'Topic',
             ],
             'medium' => [
                 'label' => 'Medium',
             ],
             'status' => [
                 'label' => 'Status',
             ],
             'audience' => [
                 'label' => 'Audience',
             ],
         ];

         $themes = array_merge(
             $defaults,
             option('yourusername.tag-garden.theme.definitions', [])
         );

         return $themes[$theme] ?? null;
     }

    /**
     * Calculate length category from word count
     *
     * Returns a category string based on configured thresholds.
     *
     * @param int $wordCount The word count
     * @return string The length category (quick, short, medium, long, epic)
     */
     public static function getLengthCategory(int $wordCount): string
     {
         $defaults = [
             'quick'  => 300,
             'short'  => 600,
             'medium' => 1200,
             'long'   => 2500,
         ];

         $thresholds = array_merge(
             $defaults,
             option('yourusername.tag-garden.length.thresholds', [])
         );

         if ($wordCount <= $thresholds['quick'])  return 'quick';
         if ($wordCount <= $thresholds['short'])  return 'short';
         if ($wordCount <= $thresholds['medium']) return 'medium';
         if ($wordCount <= $thresholds['long'])   return 'long';

         return 'deep';
     }

    /**
     * Get human-readable label for length category
     *
     * @param string $category The length category (quick, short, medium, long, epic)
     * @return string The human-readable label
     */
    public static function getLengthLabel(string $category): string {
        $labels = option('yourusername.tag-garden.length.labels', []);
        return $labels[$category] ?? $category;
    }

    /**
     * Format reading time array into display string
     *
     * Takes the array returned by $page->readingTime() and formats it
     * into a human-readable string with range (e.g., "3-5 min read")
     *
     * @param array $timeData Array from $page->readingTime()
     * @return string Formatted reading time string
     */
    public static function formatReadingTime(array $timeData): string {
        $min = $timeData['minSeconds'];
        $max = $timeData['maxSeconds'];

        if ($min < 60) {
            // Display in seconds
            if ($min === $max) {
                return $min . ' sec read';
            }
            return $max . '&thinsp;–&thinsp;' . $min . ' sec read';
        } else {
            // Display in minutes
            $minMinutes = $timeData['minMinutes'];
            $maxMinutes = $timeData['maxMinutes'];

            if ($minMinutes === $maxMinutes) {
                return $minMinutes . ' min read';
            }
            return $maxMinutes . '&thinsp;–&thinsp;' . $minMinutes . ' min read';
        }
    }

    /**
     * Get all available sort methods as array
     *
     * @return array Associative array of sort keys => labels
     */
     public static function getSortMethods(): array
     {
         return [
             'planted' => [
                 'label' => 'Planted',
                 'description' => 'Oldest first',
             ],
             'tended' => [
                 'label' => 'Recently tended',
                 'description' => 'Recently updated',
             ],
             'notable' => [
                 'label' => 'Featured',
                 'description' => 'Highlighted content',
             ],
             'length-asc' => [
                 'label' => 'Shortest first',
             ],
             'length-desc' => [
                 'label' => 'Longest first',
             ],
             'growth' => [
                 'label' => 'Growth stage',
             ],
             'title' => [
                 'label' => 'Title (A–Z)',
             ],
         ];
     }

    /**
     * Calculate font size for tag cloud based on usage count
     *
     * Uses min/max font sizes and scales based on min/max usage counts
     * in the dataset.
     *
     * @param int $count Tag usage count
     * @param int $minCount Minimum count in dataset
     * @param int $maxCount Maximum count in dataset
     * @return float Font size in rem units
     */
    public static function getTagFontSize(int $count, int $minCount, int $maxCount): float {
        $minSize = option('yourusername.tag-garden.cloud.font-min', 0.875);
        $maxSize = option('yourusername.tag-garden.cloud.font-max', 2);

        // Avoid division by zero
        if ($maxCount === $minCount) {
            return ($minSize + $maxSize) / 2;
        }

        // Linear interpolation between min and max
        $ratio = ($count - $minCount) / ($maxCount - $minCount);
        return $minSize + ($ratio * ($maxSize - $minSize));
    }

    /**
     * Validate and sanitize a tag string
     *
     * Trims whitespace, converts to lowercase, and removes invalid characters.
     *
     * @param string $tag The tag to sanitize
     * @return string Sanitized tag
     */
    public static function sanitizeTag(string $tag): string {
        $tag = trim($tag);
        $tag = mb_strtolower($tag);
        // Remove any characters that aren't alphanumeric, dash, or space
        $tag = preg_replace('/[^a-z0-9\s\-]/u', '', $tag);
        return $tag;
    }

    /**
     * Convert array of tags to URL-safe string
     *
     * @param array $tags Array of tag strings
     * @return string URL-safe tag string
     */
    public static function tagsToUrl(array $tags): string {
        $separator = option('yourusername.tag-garden.url.tag-separator', '+');
        $tags = array_map('urlencode', $tags);
        return implode($separator, $tags);
    }

    /**
     * Convert URL-safe tag string to array
     *
     * @param string $tagString URL tag string
     * @return array Array of tag strings
     */
    public static function urlToTags(string $tagString): array {
        $separator = option('yourusername.tag-garden.url.tag-separator', '+');
        $tags = explode($separator, $tagString);
        return array_map('urldecode', $tags);
    }

    /**
     * Get all unique tags from site with usage counts
     *
     * @return array Associative array of tag => count
     */
    public static function getAllTags(): array {
        $taggedPages = site()->index()->filterBy('tags', '!=', null);
        $tags = [];

        foreach ($taggedPages as $page) {
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

        return $tags;
    }

    /**
     * Get pages filtered by one or more tags
     *
     * @param array|string $tags Single tag or array of tags
     * @param string $logic 'AND' or 'OR' - whether pages need all tags or any tag
     * @return \Kirby\Cms\Pages Filtered pages collection
     */
    public static function getPagesByTags($tags, string $logic = 'OR') {
        if (is_string($tags)) {
            $tags = [$tags];
        }

        $tags = array_filter(array_map('trim', $tags));

        if (empty($tags)) {
            return new \Kirby\Cms\Pages([]);
        }

        if ($logic === 'OR') {
            // Pages with ANY of the tags
            return site()->index()->filterBy('tags', 'in', $tags);
        } else {
            // Pages with ALL of the tags (AND logic)
            $pages = site()->index();
            foreach ($tags as $tag) {
                $pages = $pages->filterBy('tags', '*=', $tag);
            }
            return $pages;
        }
    }

    /**
     * Sort pages collection by specified method
     *
     * @param \Kirby\Cms\Pages $pages Pages to sort
     * @param string $method Sort method (planted, tended, notable, length, growth, title)
     * @param string $direction Sort direction (asc, desc)
     * @return \Kirby\Cms\Pages Sorted pages collection
     */
    public static function sortPages($pages, string $method = 'tended', string $direction = 'desc') {
        switch ($method) {
            case 'planted':
                return $pages->sortBy('date_planted', $direction);

            case 'tended':
                return $pages->sortBy('last_tended', $direction);

            case 'notable':
                // Notable first, then by last_tended
                return $pages->sortBy('notable', 'desc', 'last_tended', 'desc');

            case 'length-asc':
                return $pages->sortBy(function($page) {
                    return $page->wordCount();
                }, 'asc');

            case 'length-desc':
                return $pages->sortBy(function($page) {
                    return $page->wordCount();
                }, 'desc');

            case 'growth':
                return $pages->sortBy(function($page) {
                    $status = $page->growth_status()->value();
                    $def = self::getGrowthDefinition($status);
                    return $def['sort-order'] ?? 999;
                }, 'asc');

            case 'title':
                return $pages->sortBy('title', $direction);

            default:
                return $pages->sortBy('last_tended', 'desc');
        }
    }
}
