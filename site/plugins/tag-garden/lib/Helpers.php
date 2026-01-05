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
    public static function getGrowthDefinition(string $status): ?array {
        $definitions = option('yourusername.tag-garden.growth.definitions', []);
        return $definitions[$status] ?? null;
    }

    /**
     * Get a content group definition
     *
     * @param string $group The group key (garden, soil, work, about)
     * @return array|null The group definition or null if not found
     */
    public static function getGroupDefinition(string $group): ?array {
        $definitions = option('yourusername.tag-garden.group.definitions', []);
        return $definitions[$group] ?? null;
    }

    /**
     * Get a theme definition
     *
     * @param string $theme The theme key (topic, medium, status, audience)
     * @return array|null The theme definition or null if not found
     */
    public static function getThemeDefinition(string $theme): ?array {
        $definitions = option('yourusername.tag-garden.theme.definitions', []);
        return $definitions[$theme] ?? null;
    }
    /**
     * Get the group that a content type belongs to
     *
     * @param string $type The content type/template name
     * @return string|null The group name or null if not found
     */
    public static function getGroupForType(string $type): ?string {
        $groupDefinitions = option('yourusername.tag-garden.group.definitions', []);

        foreach ($groupDefinitions as $groupKey => $groupDef) {
            if (isset($groupDef['types']) && in_array($type, $groupDef['types'])) {
                return $groupKey;
            }
        }

        return null;
    }
    /**
     * Calculate length category from word count
     *
     * Returns a category string based on configured thresholds.
     *
     * @param int $wordCount The word count
     * @return string The length category (quick, short, medium, long, deep)
     */
    public static function getLengthCategory(int $wordCount): string {
        $thresholds = option('yourusername.tag-garden.length.thresholds', [
            'quick' => 500,
            'short' => 1500,
            'medium' => 3000,
            'long' => 5000,
        ]);

        if ($wordCount < $thresholds['quick']) return 'quick';
        if ($wordCount < $thresholds['short']) return 'short';
        if ($wordCount < $thresholds['medium']) return 'medium';
        if ($wordCount < $thresholds['long']) return 'long';
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
    public static function getSortMethods(): array {
        return option('yourusername.tag-garden.sort.methods', []);
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
     * Get pages filtered by one or more tags (CASE-INSENSITIVE)
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

        // Convert search tags to lowercase for case-insensitive comparison
        $searchTags = array_map('mb_strtolower', $tags);

        if ($logic === 'OR') {
            // Pages with ANY of the tags (case-insensitive)
            return site()->index()->filter(function($page) use ($searchTags) {
                $pageTags = $page->tags()->split(',');
                foreach ($pageTags as $tag) {
                    $tag = trim($tag);
                    if (in_array(mb_strtolower($tag), $searchTags, true)) {
                        return true;
                    }
                }
                return false;
            });
        } else {
            // Pages with ALL of the tags (AND logic, case-insensitive)
            return site()->index()->filter(function($page) use ($searchTags) {
                $pageTags = $page->tags()->split(',');
                $pageTagsLower = array_map(function($tag) {
                    return mb_strtolower(trim($tag));
                }, $pageTags);

                // Check if all search tags are present in page tags
                foreach ($searchTags as $searchTag) {
                    if (!in_array($searchTag, $pageTagsLower, true)) {
                        return false;
                    }
                }
                return true;
            });
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
