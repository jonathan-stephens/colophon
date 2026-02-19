<?php

/**
 * Tag Garden Plugin for Kirby CMS
 *
 * Simplified tag-based content exploration system for digital gardens.
 * Explore content by tags with AND logic for deep discovery.
 *
 * Features:
 * - Tag-based content exploration with AND filtering
 * - Related tags discovery
 * - Growth status tracking (sown → evergreen)
 * - Content grouping (garden, soil, work, about)
 * - Reading time calculation
 *
 * @version 2.0.0
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
     * PLUGIN OPTIONS
     */
    'options' => [
        // Display
        'section.limit' => 10,
        'related.tag-limit' => 10,

        // Sorting
        'default.sort' => 'tended',
        'sort.methods' => [
            'planted' => 'Date Planted',
            'tended' => 'Last Tended',
            'growth' => 'Growth Status',
        ],

        // Growth status configuration
        'growth.statuses' => ['sown', 'sprouting', 'rooting', 'crowning', 'evergreen'],
        'growth.definitions' => [
            'sown' => [
                'label' => 'Sown',
                'emoji' => '🌰',
                'color' => '#fef3c7',
                'description' => 'New seeds, initial ideas',
                'sort-order' => 0,
            ],
            'sprouting' => [
                'label' => 'Sprouting',
                'emoji' => '🌱',
                'color' => '#d9f99d',
                'description' => 'Early growth, developing concepts',
                'sort-order' => 1,
            ],
            'rooting' => [
                'label' => 'Rooting',
                'emoji' => '🌿',
                'color' => '#86efac',
                'description' => 'Taking hold, establishing connections',
                'sort-order' => 2,
            ],
            'crowning' => [
                'label' => 'Crowning',
                'emoji' => '🌳',
                'color' => '#4ade80',
                'description' => 'Mature and flourishing',
                'sort-order' => 3,
            ],
            'evergreen' => [
                'label' => 'Evergreen',
                'emoji' => '🌲',
                'color' => '#22c55e',
                'description' => 'Timeless, well-maintained content',
                'sort-order' => 4,
            ],
        ],

        // Content groups configuration
        'content.groups' => [
            'garden' => ['journal', 'essay', 'article', 'book'],
            'soil' => ['library', 'quote', 'link'],
            'work' => ['overview', 'experience', 'projects', 'work'],
            'about' => ['strengths', 'skills', 'about', 'now'],
        ],
        'group.definitions' => [
            'garden' => [
                'label' => 'Garden',
                'emoji' => '🌸',
                'color' => '#ec4899',
                'description' => 'Writing and long-form content',
            ],
            'soil' => [
                'label' => 'Soil',
                'emoji' => '📚',
                'color' => '#8b5cf6',
                'description' => 'Links, references, and collections',
            ],
            'work' => [
                'label' => 'Work',
                'emoji' => '💼',
                'color' => '#3b82f6',
                'description' => 'Professional projects and experience',
            ],
            'about' => [
                'label' => 'About',
                'emoji' => '👤',
                'color' => '#10b981',
                'description' => 'Personal information and profiles',
            ],
        ],

        // Reading time calculation
        'reading.speed.min' => 167,  // Slow readers (words per minute)
        'reading.speed.max' => 285,  // Fast readers (words per minute)
    ],

    /**
     * BLUEPRINTS
     */
    'blueprints' => [
        'fields/tag-garden' => [
            'type' => 'fields',
            'fields' => [

                // Main tags field
                'tags' => [
                    'label' => 'Tags',
                    'type' => 'tags',
                    'help' => 'Add tags to organize and connect this content',
                    'accept' => 'options',
                    'options' => 'query',
                    'query' => 'site.index.pluck("tags", ",", true)',
                    'icon' => 'tag',
                ],

                // Growth status
                'growth_status' => [
                    'label' => 'Growth Status',
                    'type' => 'select',
                    'help' => 'Current state of this content',
                    'options' => 'query',
                    'query' => 'kirby.option("yourusername.tag-garden.growth.statuses")',
                    'default' => 'sown',
                    'width' => '1/2',
                ],

                'separator1' => [
                    'type' => 'headline',
                    'label' => 'Dates',
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

                // Last tended (last updated)
                'last_tended' => [
                    'label' => 'Last Tended',
                    'type' => 'date',
                    'help' => 'When was this content last updated?',
                    'default' => 'now',
                    'time' => true,
                    'width' => '1/2',
                ],
            ]
        ],
    ],

    /**
     * PAGE METHODS
     */
    'pageMethods' => [

        /**
         * Get word count from page content
         * Works independently of plugin configuration
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
         * Calculate reading time range for different reading speeds
         *
         * @return array [wordCount, minSeconds, maxSeconds, minMinutes, maxMinutes, avgMinutes]
         */
        'readingTime' => function() {
            $wordCount = $this->wordCount();

            // Get reading speeds from config with fallback defaults
            $minSpeed = option('yourusername.tag-garden.reading.speed.min', 167);
            $maxSpeed = option('yourusername.tag-garden.reading.speed.max', 285);

            // Calculate time in minutes
            $minMinutes = $wordCount / $minSpeed;  // Slow readers
            $maxMinutes = $wordCount / $maxSpeed;  // Fast readers

            return [
                'wordCount' => $wordCount,
                'minSeconds' => ceil($minMinutes * 60),
                'maxSeconds' => ceil($maxMinutes * 60),
                'minMinutes' => ceil($minMinutes),
                'maxMinutes' => ceil($maxMinutes),
                'avgMinutes' => ceil(($minMinutes + $maxMinutes) / 2),
            ];
        },

        /**
         * Get formatted reading time string
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
                return $max . '–' . $min . ' sec read';
            } else {
                // Display in minutes
                $minMinutes = $time['minMinutes'];
                $maxMinutes = $time['maxMinutes'];

                if ($minMinutes === $maxMinutes) {
                    return $minMinutes . ' min read';
                }
                return $maxMinutes . '–' . $minMinutes . ' min read';
            }
        },

        /**
         * Get the content group this page belongs to
         */
        'contentGroup' => function() {
            $groups = option('yourusername.tag-garden.content.groups', []);
            $template = $this->intendedTemplate()->name();

            foreach ($groups as $groupName => $contentTypes) {
                if (in_array($template, $contentTypes)) {
                    return $groupName;
                }
            }

            return null;
        },

        /**
         * Get related tags from pages that share tags with this page
         */
        'relatedTags' => function() {
            $currentTags = $this->tags()->split(',');
            $currentTags = array_filter(array_map('trim', $currentTags));

            if (empty($currentTags)) {
                return [];
            }

            // Find all pages that share at least one tag
            $relatedPages = site()->index()->filter(function($page) use ($currentTags) {
                $pageTags = $page->tags()->split(',');
                $pageTags = array_filter(array_map('trim', $pageTags));

                foreach ($pageTags as $pageTag) {
                    if (in_array(mb_strtolower($pageTag), array_map('mb_strtolower', $currentTags))) {
                        return true;
                    }
                }
                return false;
            });

            // Collect all tags from related pages
            $allRelatedTags = [];
            foreach ($relatedPages as $page) {
                $pageTags = $page->tags()->split(',');
                $pageTags = array_filter(array_map('trim', $pageTags));
                $allRelatedTags = array_merge($allRelatedTags, $pageTags);
            }

            // Remove duplicates and current tags
            $allRelatedTags = array_unique($allRelatedTags);
            $allRelatedTags = array_diff($allRelatedTags, $currentTags);

            return array_values($allRelatedTags);
        },

        /**
         * Get pages that share tags with this page
         */
        'relatedPages' => function(int $limit = 5) {
            $currentTags = $this->tags()->split(',');
            $currentTags = array_filter(array_map('trim', $currentTags));

            if (empty($currentTags)) {
                return new \Kirby\Cms\Pages([]);
            }

            // Find pages with shared tags
            return site()
                ->index()
                ->filter(function($page) use ($currentTags) {
                    if ($page->is($this)) {
                        return false;
                    }

                    $pageTags = $page->tags()->split(',');
                    $pageTags = array_filter(array_map('trim', $pageTags));

                    foreach ($pageTags as $pageTag) {
                        if (in_array(mb_strtolower($pageTag), array_map('mb_strtolower', $currentTags))) {
                            return true;
                        }
                    }
                    return false;
                })
                ->limit($limit);
        },
    ],

    /**
     * COLLECTIONS
     */
    'collections' => file_exists(__DIR__ . '/collections/tags.php')
        ? require __DIR__ . '/collections/tags.php'
        : [],

    /**
     * ROUTES
     */
    'routes' => file_exists(__DIR__ . '/routes/tags.php')
        ? require __DIR__ . '/routes/tags.php'
        : [],

    /**
     * TEMPLATES
     */
    'templates' => [
        'tags' => __DIR__ . '/templates/tags.php',
        'tag' => __DIR__ . '/templates/tag.php',
    ],

    /**
     * CONTROLLERS
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
     * SNIPPETS
     */
    'snippets' => [
        'tag-garden/reading-time' => __DIR__ . '/snippets/reading-time.php',
        'tag-garden/badge' => __DIR__ . '/snippets/tag-badge.php',
        'tag-garden/explorer' => __DIR__ . '/snippets/tags-explorer.php',
        'tag-garden/section' => __DIR__ . '/snippets/tags-section.php',
    ],
]);
