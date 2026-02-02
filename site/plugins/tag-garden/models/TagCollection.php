<?php

namespace Yourusername\TagGarden;

use Kirby\Cms\Page;

/**
 * Tag Collection Model
 *
 * Custom page model for virtual tag pages created by routes.
 * Adds helper methods for working with tag-specific data.
 *
 * To enable this model, register it in index.php:
 * 'pageModels' => [
 *     'tag' => 'Yourusername\TagGarden\TagCollection',
 * ]
 *
 * @version 1.0.0
 */
class TagCollection extends Page
{
    /**
     * Get the tag(s) this page represents
     *
     * @return array Array of tag names
     */
    public function filterTags(): array
    {
        // Tags should be set in content when page is created
        $tags = $this->content()->get('filterTags');

        if (is_array($tags)) {
            return $tags;
        }

        if (is_string($tags)) {
            return explode(',', $tags);
        }

        return [];
    }

    /**
     * Get pages tagged with this tag collection
     *
     * @param string $sort Sort method
     * @param string $logic 'OR' or 'AND'
     * @return \Kirby\Cms\Pages
     */
    public function taggedPages(string $sort = 'tended', string $logic = 'OR')
    {
        $tags = $this->filterTags();

        if (empty($tags)) {
            return new \Kirby\Cms\Pages([]);
        }

        return kirby()->collection('pages.byTags', [
            'tags' => $tags,
            'logic' => $logic,
            'sort' => $sort,
        ]);
    }

    /**
     * Get related tags for this tag collection
     *
     * @param int $limit Maximum number of related tags
     * @return array Associative array of tag => count
     */
    public function relatedTags(int $limit = 10): array
    {
        $tags = $this->filterTags();

        if (empty($tags)) {
            return [];
        }

        // If single tag, use collection
        if (count($tags) === 1) {
            return kirby()->collection('tags.related', [
                'tag' => $tags[0],
                'limit' => $limit
            ]);
        }

        // Multiple tags: find tags that appear with these tags
        $pages = $this->taggedPages();
        $relatedTags = [];

        foreach ($pages as $page) {
            $pageTags = $page->tags()->split(',');
            foreach ($pageTags as $tag) {
                $tag = trim($tag);
                if (!empty($tag) && !in_array($tag, $tags)) {
                    if (!isset($relatedTags[$tag])) {
                        $relatedTags[$tag] = 0;
                    }
                    $relatedTags[$tag]++;
                }
            }
        }

        arsort($relatedTags);
        return array_slice($relatedTags, 0, $limit, true);
    }

    /**
     * Get statistics about content in this tag collection
     *
     * @return array Statistics array
     */
    public function stats(): array
    {
        $pages = $this->taggedPages();

        // Growth status distribution
        $growthStats = [
            'seedling' => 0,
            'budding' => 0,
            'evergreen' => 0,
            'wilting' => 0,
        ];

        // Length distribution
        $lengthStats = [
            'quick' => 0,
            'short' => 0,
            'medium' => 0,
            'long' => 0,
            'deep' => 0,
        ];

        // Content group distribution
        $groupStats = [];

        $totalWords = 0;
        $totalReadingTime = 0;

        foreach ($pages as $page) {
            // Growth status
            $status = $page->growth_status()->value();
            if (isset($growthStats[$status])) {
                $growthStats[$status]++;
            }

            // Length category
            $wordCount = $page->wordCount();
            $totalWords += $wordCount;
            $category = Helpers::getLengthCategory($wordCount);
            if (isset($lengthStats[$category])) {
                $lengthStats[$category]++;
            }

            // Content group
            $group = $page->contentGroup();
            if ($group) {
                if (!isset($groupStats[$group])) {
                    $groupStats[$group] = 0;
                }
                $groupStats[$group]++;
            }

            // Reading time
            $readingTime = $page->readingTime();
            $totalReadingTime += $readingTime['avgMinutes'];
        }

        $pageCount = $pages->count();

        return [
            'total' => $pageCount,
            'growth' => $growthStats,
            'length' => $lengthStats,
            'groups' => $groupStats,
            'avgWords' => $pageCount > 0 ? round($totalWords / $pageCount) : 0,
            'avgReadingTime' => $pageCount > 0 ? round($totalReadingTime / $pageCount) : 0,
            'totalWords' => $totalWords,
        ];
    }

    /**
     * Get the most recent page in this collection
     *
     * @param string $dateField 'date_planted' or 'last_tended'
     * @return Page|null
     */
    public function mostRecent(string $dateField = 'last_tended')
    {
        $pages = $this->taggedPages($dateField, 'OR');
        return $pages->first();
    }

    /**
     * Get the oldest page in this collection
     *
     * @param string $dateField 'date_planted' or 'last_tended'
     * @return Page|null
     */
    public function oldest(string $dateField = 'date_planted')
    {
        $pages = $this->taggedPages();
        $sorted = Helpers::sortPages($pages, 'planted', 'asc');
        return $sorted->first();
    }

    /**
     * Get notable/featured pages from this collection
     *
     * @param int $limit Maximum number of pages
     * @return \Kirby\Cms\Pages
     */
    public function notable(int $limit = 5)
    {
        return $this->taggedPages()
            ->filterBy('notable', true)
            ->limit($limit);
    }

    /**
     * Check if this is a single-tag page
     *
     * @return bool
     */
    public function isSingleTag(): bool
    {
        return count($this->filterTags()) === 1;
    }

    /**
     * Check if this is a multi-tag page (AND combination)
     *
     * @return bool
     */
    public function isMultiTag(): bool
    {
        return count($this->filterTags()) > 1;
    }

    /**
     * Get a human-readable title for this tag collection
     *
     * @return string
     */
    public function displayTitle(): string
    {
        $tags = $this->filterTags();

        if (empty($tags)) {
            return 'Tag Collection';
        }

        if (count($tags) === 1) {
            return 'Tag: ' . $tags[0];
        }

        return 'Tags: ' . implode(' + ', $tags);
    }

    /**
     * Generate a description for this tag collection
     *
     * @return string
     */
    public function description(): string
    {
        $tags = $this->filterTags();
        $pages = $this->taggedPages();
        $count = $pages->count();

        if (empty($tags)) {
            return '';
        }

        if (count($tags) === 1) {
            return "Explore {$count} " . ($count === 1 ? 'page' : 'pages') . " tagged with '{$tags[0]}'.";
        }

        return "Explore {$count} " . ($count === 1 ? 'page' : 'pages') . " that combine these tags: " . implode(', ', $tags) . ".";
    }

    /**
     * Get URL for adding another tag to this collection
     *
     * @param string $additionalTag Tag to add
     * @return string
     */
    public function urlWithTag(string $additionalTag): string
    {
        $tags = $this->filterTags();
        $tags[] = $additionalTag;
        $tags = array_unique($tags);

        return url('tags/' . Helpers::tagsToUrl($tags) . '?logic=AND');
    }

    /**
     * Get URL for removing a tag from this collection
     *
     * @param string $tagToRemove Tag to remove
     * @return string
     */
    public function urlWithoutTag(string $tagToRemove): string
    {
        $tags = $this->filterTags();
        $tags = array_filter($tags, function($tag) use ($tagToRemove) {
            return $tag !== $tagToRemove;
        });

        if (empty($tags)) {
            return url('tags');
        }

        if (count($tags) === 1) {
            return url('tags/' . Helpers::tagsToUrl($tags));
        }

        return url('tags/' . Helpers::tagsToUrl($tags) . '?logic=AND');
    }
}
