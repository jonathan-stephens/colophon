<?php

/**
 * Tag Garden Plugin for Kirby CMS
 *
 * A digital garden tag exploration system that allows users to wander through
 * content by discovering tags, viewing tagged content, and finding related tags
 * through content relationships.
 *
 * Features:
 * - Tag-based content exploration with "drilling deeper" navigation
 * - Multiple sort methods (date planted, last tended, notable, length, growth)
 * - Range-based reading time calculation (fast/slow readers)
 * - Content grouping (garden, soil, work, about)
 * - Growth status tracking (seedling, budding, evergreen, wilting)
 *
 * @version 1.0.0
 * @author Your Name
 */

// Ensure Kirby is loaded
if (!class_exists('Kirby\\Cms\\App')) {
    return;
}

// Load helper class
require_once __DIR__ . '/lib/Helpers.php';

// Register the plugin
Kirby::plugin('yourusername/tag-garden', [

    /**
     * ============================================================================
     * PLUGIN OPTIONS
     * ============================================================================
     *
     * Basic options. Extended options are loaded via site/config/config.php
     */
    'options' => [
        // Default number of items to show in embedded section view
        'section.limit' => 10,

        // Default sort method: 'planted', 'tended', 'notable', 'length', 'growth', 'title'
        'default.sort' => 'tended',

        // Growth status options - these match what users can select
        'growth.statuses' => ['seedling', 'budding', 'evergreen', 'wilting'],

        // Theme options for organizing tags in the admin panel
        'themes' => ['topic', 'medium', 'status', 'audience'],

        // Content groups configuration - maps template names to groups
        'content.groups' => [
            'garden' => ['journal', 'essay', 'article', 'book'],
            'soil' => ['library', 'quote', 'link'],
            'work' => ['overview', 'experience', 'projects', 'work'],
            'about' => ['strengths', 'skills', 'about', 'now']
        ],

        // Reading speed in words per minute (range for different reading speeds)
        'reading.speed.min' => 167,  // Slow readers
        'reading.speed.max' => 285,  // Fast readers
    ],

    /**
     * ============================================================================
     * BLUEPRINTS
     * ============================================================================
     *
     * Extend existing page blueprints to add tag-related fields.
     * These fields appear in the Kirby admin panel.
     *
     * Usage in your blueprints:
     * fields:
     *   myfields: fields/tag-garden
     */
    'blueprints' => [
        'fields/tag-garden' => [
            'type' => 'fields',
            'fields' => [

                // Main tags field with autocomplete from existing tags
                'tags' => [
                    'label' => 'Tags',
                    'type' => 'tags',
                    'help' => 'Add tags to help organize and connect this content. Start typing to see existing tags.',
                    'accept' => 'options',
                    'options' => 'query',
                    // Query gets all unique tags from all pages across the site
                    'query' => 'site.index.pluck("tags", ",", true)',
                    'icon' => 'tag',
                ],

                // Optional: Theme categorization for organizing tags
                'tag_theme' => [
                    'label' => 'Primary Tag Theme',
                    'type' => 'select',
                    'help' => 'What is the primary theme/category for this content\'s tags?',
                    'options' => 'query',
                    'query' => 'kirby.option("yourusername.tag-garden.themes")',
                    'placeholder' => 'Select a theme...',
                    'width' => '1/2',
                ],

                // Growth status indicator for digital garden metaphor
                'growth_status' => [
                    'label' => 'Growth Status',
                    'type' => 'select',
                    'help' => 'Current state of this content in your digital garden',
                    'options' => 'query',
                    'query' => 'kirby.option("yourusername.tag-garden.growth.statuses")',
                    'default' => 'seedling',
                    'width' => '1/2',
                ],

                // Horizontal rule for visual separation
                'separator1' => [
                    'type' => 'headline',
                    'label' => 'Dates & Status',
                    'numbered' => false,
                ],

                // Date planted (first published)
                'date_planted' => [
                    'label' => 'Date Planted',
                    'type' => 'date',
                    'help' => 'When was this content first published?',
                    'default' => 'now',
                    'time' => true,
                    'width' => '1/2',
                ],

                // Last tended (last meaningfully updated)
                'last_tended' => [
                    'label' => 'Last Tended',
                    'type' => 'date',
                    'help' => 'When was this content last meaningfully updated?',
                    'default' => 'now',
                    'time' => true,
                    'width' => '1/2',
                ],

                // Notable/favorite flag for featured content
                'notable' => [
                    'label' => 'Notable',
                    'type' => 'toggle',
                    'help' => 'Mark this as a featured or particularly important piece of content',
                    'text' => ['Featured', 'Standard'],
                    'default' => false,
                ],
            ]
        ],
    ],

    /**
     * ============================================================================
     * PAGE METHODS
     * ============================================================================
     *
     * Custom methods available on any $page object throughout Kirby.
     * These calculate metadata about content for sorting, filtering, and display.
     *
     * Example usage: $page->wordCount() or $page->readingTime()
     */
    'pageMethods' => [

        /**
         * Get word count from page content
         *
         * Looks for content in 'text' field first, falls back to 'content' field.
         * Strips HTML tags before counting.
         *
         * @return int Total word count
         */
         'wordCount' => function () {
             if ($this->text()->isNotEmpty()) {
                 $text = $this->text()->value();
             } else {
                 $text = $this->content()->toString();
             }

             $text = strip_tags($text);

             return str_word_count($text);
         },

        /**
         * Get character count from page content
         *
         * @return int Total character count (excluding HTML tags)
         */
         'charCount' => function () {
             if ($this->text()->isNotEmpty()) {
                 $text = $this->text()->value();
             } else {
                 $text = $this->content()->toString();
             }

             $text = strip_tags($text);

             return mb_strlen($text);
         },

        /**
         * Calculate reading time range based on word count
         *
         * Returns a range for fast and slow readers using configurable speeds.
         * Calculation: wordCount / wordsPerMinute
         *
         * @return array Array with min/max times in seconds and minutes
         *   [
         *     'wordCount' => int,
         *     'minSeconds' => int,    // Time for fast readers in seconds
         *     'maxSeconds' => int,    // Time for slow readers in seconds
         *     'minMinutes' => int,    // Time for fast readers in minutes
         *     'maxMinutes' => int,    // Time for slow readers in minutes
         *   ]
         */
        'readingTime' => function() {
            $wordCount = $this->wordCount();

            // Get reading speeds from config (words per minute)
            $minSpeed = option('yourusername.tag-garden.reading.speed.min', 167);
            $maxSpeed = option('yourusername.tag-garden.reading.speed.max', 285);

            // Calculate time in minutes first
            $minMinutes = $wordCount / $minSpeed;  // Slow readers take longer
            $maxMinutes = $wordCount / $maxSpeed;  // Fast readers are quicker

            // Convert to seconds and round up
            $minSeconds = ceil($minMinutes * 60);
            $maxSeconds = ceil($maxMinutes * 60);

            // Also provide minute values (rounded up)
            return [
                'wordCount' => $wordCount,
                'minSeconds' => $minSeconds,
                'maxSeconds' => $maxSeconds,
                'minMinutes' => ceil($minMinutes),
                'maxMinutes' => ceil($maxMinutes),
                'avgMinutes' => ceil(($minMinutes + $maxMinutes) / 2), // For sorting
            ];
        },

        /**
         * Get formatted reading time string
         *
         * Formats the reading time range in a human-readable way:
         * - "45 sec read" (if same for both speeds and under 60 seconds)
         * - "30–45 sec read" (if different and under 60 seconds)
         * - "5 min read" (if same for both speeds)
         * - "3–5 min read" (if different speeds)
         *
         * @return string Formatted reading time
         */
        'readingTimeFormatted' => function() {
            $time = $this->readingTime();
            $min = $time['minSeconds'];
            $max = $time['maxSeconds'];

            if ($min < 60) {
                // Display in seconds
                if ($min === $max) {
                    return $min . ' sec read';
                }
                return $max . '&thinsp;–&thinsp;' . $min . ' sec read';
            } else {
                // Display in minutes
                $minMinutes = $time['minMinutes'];
                $maxMinutes = $time['maxMinutes'];

                if ($minMinutes === $maxMinutes) {
                    return $minMinutes . ' min read';
                }
                return $maxMinutes . '&thinsp;–&thinsp;' . $minMinutes . ' min read';
            }
        },

        /**
         * Get the content group this page belongs to
         *
         * Determines which group (garden, soil, work, about) based on the
         * page's intended template name.
         *
         * @return string|null The group name or null if not in a defined group
         */
        'contentGroup' => function() {
            $groups = option('yourusername.tag-garden.content.groups', []);
            $template = $this->intendedTemplate()->name();

            // Check each group's content types
            foreach ($groups as $groupName => $contentTypes) {
                if (in_array($template, $contentTypes)) {
                    return $groupName;
                }
            }

            return null;
        },

        /**
         * Get related tags from pages that share tags with this page
         *
         * Logic:
         * 1. Get this page's tags
         * 2. Find all pages that share ANY of these tags
         * 3. Collect all unique tags from those pages
         * 4. Remove this page's tags from the result
         * 5. Return as array for exploration
         *
         * This powers the "drilling deeper" navigation.
         *
         * @return array Array of related tag names
         */
        'relatedTags' => function() {
            // Get current page's tags
            $currentTags = $this->tags()->split(',');

            // Clean and filter empty tags
            $currentTags = array_filter(array_map('trim', $currentTags));

            if (empty($currentTags)) {
                return [];
            }

            // Find all pages that share at least one tag with this page
            $relatedPages = site()->index()->filterBy('tags', 'in', $currentTags);

            // Collect all tags from related pages
            $allRelatedTags = [];
            foreach ($relatedPages as $page) {
                $pageTags = $page->tags()->split(',');
                $pageTags = array_filter(array_map('trim', $pageTags));
                $allRelatedTags = array_merge($allRelatedTags, $pageTags);
            }

            // Remove duplicates
            $allRelatedTags = array_unique($allRelatedTags);

            // Remove current page's tags (we want to discover NEW tags)
            $allRelatedTags = array_diff($allRelatedTags, $currentTags);

            // Re-index array and return
            return array_values($allRelatedTags);
        },

        /**
         * Get pages that share tags with this page
         *
         * Useful for "related content" sections.
         *
         * @param int $limit Maximum number of related pages to return
         * @return Pages Collection of related pages
         */
        'relatedPages' => function(int $limit = 5) {
            $currentTags = $this->tags()->split(',');
            $currentTags = array_filter(array_map('trim', $currentTags));

            if (empty($currentTags)) {
                return new \Kirby\Cms\Pages([]);
            }

            // Find pages with shared tags, excluding current page
            return site()
                ->index()
                ->filterBy('tags', 'in', $currentTags)
                ->not($this)
                ->limit($limit);
        },
    ],

    /**
     * ============================================================================
     * COLLECTIONS
     * ============================================================================
     *
     * Global collections that can be accessed via $kirby->collection('name')
     * Loaded from separate file for better organization.
     */
    'collections' => file_exists(__DIR__ . '/collections/tags.php')
        ? require __DIR__ . '/collections/tags.php'
        : [],

    /**
     * ============================================================================
     * ROUTES
     * ============================================================================
     *
     * Custom URL routing for tag pages.
     * Loaded from separate file for better organization.
     */
    'routes' => file_exists(__DIR__ . '/routes/tags.php')
        ? require __DIR__ . '/routes/tags.php'
        : [],

    /**
     * ============================================================================
     * TEMPLATES
     * ============================================================================
     *
     * Register template locations for tag pages
     */
    'templates' => [
        'tags' => __DIR__ . '/templates/tags.php',
        'tag' => __DIR__ . '/templates/tag.php',
    ],

    /**
     * ============================================================================
     * CONTROLLERS
     * ============================================================================
     *
     * Register controller locations for preparing data for templates
     */
    'controllers' => [
        'tags' => file_exists(__DIR__ . '/controllers/tags.php')
            ? require __DIR__ . '/controllers/tags.php'
            : function() { return []; },
        'tag' => file_exists(__DIR__ . '/controllers/tag.php')
            ? require __DIR__ . '/controllers/tag.php'
            : function() { return []; },
    ],

    /**
 * ============================================================================
 * SNIPPETS
 * ============================================================================
 *
 * Register snippet locations
 */
'snippets' => [
    'tag-garden/reading-time' => __DIR__ . '/snippets/reading-time.php',
    'tag-garden/badge' => __DIR__ . '/snippets/tag-badge.php',
    'tag-garden/explorer' => __DIR__ . '/snippets/tags-explorer.php',
    'tag-garden/section' => __DIR__ . '/snippets/tags-section.php',
],
]);
