<?php

namespace Yourusername\TagGarden;

use Kirby\Toolkit\Str;

/**
 * Tag Garden Helpers
 *
 * Simplified helper methods for tag-based content exploration.
 *
 * @version 2.0.0
 */
class Helpers {

    /**
     * Get a growth status definition
     *
     * @param string $status The status key (sown, sprouting, rooting, crowning, evergreen)
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
     * Get the group that a content type belongs to
     *
     * @param string $type The content type/template name
     * @return string|null The group name or null if not found
     */
    public static function getGroupForType(string $type): ?string {
        $groups = option('yourusername.tag-garden.content.groups', []);

        foreach ($groups as $groupKey => $types) {
            if (in_array($type, $types)) {
                return $groupKey;
            }
        }

        return null;
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
     * Parse tag string from URL into normalized tag array
     *
     * Handles comma-separated tags with URL encoding.
     * Examples:
     *  web-development           → ["web-development"]
     *  web-development,design    → ["web-development", "design"]
     *  Web%20Development,Design  → ["web-development", "design"]
     *
     * @param string $tagString URL tag string
     * @return array Normalized tag array
     */
    public static function parseTagsFromUrl(string $tagString): array {
        // Split on comma
        $rawTags = explode(',', $tagString);

        return array_values(array_filter(array_map(function ($tag) {
            // Decode URL encoding
            $tag = rawurldecode($tag);

            // Normalize whitespace
            $tag = preg_replace('/\s+/', ' ', trim($tag));

            // Convert to slug for consistency
            $tag = Str::slug($tag);

            return $tag !== '' ? $tag : null;
        }, $rawTags)));
    }

    /**
     * Get pages filtered by one or more tags (ALWAYS AND LOGIC)
     *
     * Pages must have ALL specified tags to be included.
     *
     * @param array|string $tags Single tag or array of tags
     * @return \Kirby\Cms\Pages Filtered pages collection
     */
    public static function getPagesByTags($tags): \Kirby\Cms\Pages {
        if (is_string($tags)) {
            $tags = [$tags];
        }

        $tags = array_filter(array_map('trim', $tags));

        if (empty($tags)) {
            return new \Kirby\Cms\Pages([]);
        }

        // Convert search tags to lowercase for case-insensitive comparison
        $searchTags = array_map('mb_strtolower', $tags);

        // Pages with ALL of the tags (AND logic)
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

    /**
     * Generate canonical tag URL with sorted, slugified tags
     *
     * Ensures consistent URLs for SEO and caching:
     * - Tags are slugified (lowercase, hyphens)
     * - Tags are sorted alphabetically
     * - Tags are comma-separated
     *
     * @param array $filterTags Array of tag names
     * @return string Canonical URL path (e.g., "tags/design,web-development")
     */
    public static function canonicalTagUrl(array $filterTags): string {
        // Trim and remove empties
        $tags = array_filter(array_map('trim', $filterTags));

        // Slugify each tag
        $tags = array_map(function($tag) {
            return Str::slug($tag);
        }, $tags);

        // Sort alphabetically for consistency
        sort($tags);

        return 'tags/' . implode(',', $tags);
    }

    /**
     * Sort pages collection by specified method
     *
     * @param \Kirby\Cms\Pages $pages Pages to sort
     * @param string $method Sort method (planted, tended, growth)
     * @param string $direction Sort direction (asc, desc)
     * @return \Kirby\Cms\Pages Sorted pages collection
     */
    public static function sortPages($pages, string $method = 'tended', string $direction = 'desc') {
        switch ($method) {
            case 'planted':
                return $pages->sortBy('date_planted', $direction);

            case 'tended':
                return $pages->sortBy('last_tended', $direction);

            case 'growth':
                return $pages->sortBy(function($page) {
                    $status = $page->growth_status()->value();
                    $def = self::getGrowthDefinition($status);
                    return $def['sort-order'] ?? 999;
                }, 'asc');

            default:
                return $pages->sortBy('last_tended', 'desc');
        }
    }
}
