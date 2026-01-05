<?php

/**
 * Tag Garden Plugin Configuration
 *
 * Extended configuration options for the Tag Garden plugin.
 * These are loaded via site/config/config.php with the namespace:
 * 'yourusername.tag-garden' => require __DIR__ . '/../plugins/tag-garden/config/options.php'
 *
 * Users can override any of these in their site's config.php
 *
 * @version 1.0.0
 */

return [

    /**
     * DISPLAY CONFIGURATION
     */
    'section.tag-limit' => 20,
    'section.content-limit' => 10,
    'related.tag-limit' => 5,
    'show.empty-tags' => false,
    'show.tag-counts' => true,

    /**
     * SORTING CONFIGURATION
     */
    'sort.methods' => [
        'planted' => ['label' => 'Date Planted'],
        'tended' => ['label' => 'Last Tended'],
        'notable' => ['label' => 'Notable First'],
        'length-asc' => ['label' => 'Shortest First'],
        'length-desc' => ['label' => 'Longest First'],
        'growth' => ['label' => 'Growth Status'],
        'title' => ['label' => 'Alphabetical'],
    ],
    'sort.direction' => 'desc',
    'growth.sort-order' => ['evergreen', 'budding', 'seedling', 'wilting'],

    /**
     * TAG THEME CONFIGURATION
     */
    'theme.definitions' => [
        'topic' => [
            'label' => 'Topic',
            'color' => '#3b82f6',
            'description' => 'Subject matter or area of focus',
            'icon' => '🏷️',
        ],
        'medium' => [
            'label' => 'Medium',
            'color' => '#8b5cf6',
            'description' => 'Format or type of content',
            'icon' => '📝',
        ],
        'status' => [
            'label' => 'Status',
            'color' => '#10b981',
            'description' => 'Current state or progress',
            'icon' => '✓',
        ],
        'audience' => [
            'label' => 'Audience',
            'color' => '#f59e0b',
            'description' => 'Intended readers or users',
            'icon' => '👥',
        ],
    ],

    /**
     * LENGTH CALCULATION CONFIGURATION
     */
    'reading.speed.min' => 167,
    'reading.speed.max' => 285,
    'length.thresholds' => [
        'quick' => 500,
        'short' => 1500,
        'medium' => 3000,
        'long' => 5000,
    ],
    'length.labels' => [
        'quick' => 'Quick read',
        'short' => 'Short read',
        'medium' => 'Medium read',
        'long' => 'Long read',
        'deep' => 'Deep read',
    ],

    /**
     * GROWTH STATUS CONFIGURATION
     */
    'growth.definitions' => [
        'seedling' => [
            'label' => 'Seedling',
            'emoji' => '🌱',
            'color' => '#86efac',
            'description' => 'New ideas, rough notes, early drafts',
            'sort-order' => 2,
        ],
        'budding' => [
            'label' => 'Budding',
            'emoji' => '🌿',
            'color' => '#4ade80',
            'description' => 'Growing content, being developed and refined',
            'sort-order' => 1,
        ],
        'evergreen' => [
            'label' => 'Evergreen',
            'emoji' => '🌲',
            'color' => '#22c55e',
            'description' => 'Mature, well-developed, regularly maintained',
            'sort-order' => 0,
        ],
        'wilting' => [
            'label' => 'Wilting',
            'emoji' => '🍂',
            'color' => '#fb923c',
            'description' => 'Outdated or archived, may need revision',
            'sort-order' => 3,
        ],
    ],

    /**
     * CONTENT GROUP CONFIGURATION
     */
    'group.definitions' => [
        'garden' => [
            'label' => 'Garden',
            'emoji' => '🌸',
            'color' => '#ec4899',
            'description' => 'Writing and long-form content',
            'types' => ['journal', 'essay', 'article', 'book'],
        ],
        'soil' => [
            'label' => 'Soil',
            'emoji' => '📚',
            'color' => '#8b5cf6',
            'description' => 'Links, references, and collections',
            'types' => ['link', 'library', 'quote'],
        ],
        'work' => [
            'label' => 'Work',
            'emoji' => '💼',
            'color' => '#3b82f6',
            'description' => 'Professional projects and experience',
            'types' => ['work-container', 'project', 'experience', 'work'],
        ],
        'about' => [
            'label' => 'About',
            'emoji' => '👤',
            'color' => '#10b981',
            'description' => 'Personal information and profiles',
            'types' => ['strengths', 'skills', 'about', 'now'],
        ],
    ],

    /**
     * TAG CLOUD CONFIGURATION
     */
    'cloud.font-min' => 0.875,
    'cloud.font-max' => 2,
    'cloud.min-count' => 1,
    'cloud.max-tags' => 0,

    /**
     * URL & ROUTING CONFIGURATION
     */
    'url.base' => 'tags',
    'url.sort-param' => 'sort',
    'url.group-param' => 'group',
    'url.theme-param' => 'theme',
    'url.tag-separator' => '+',

    /**
     * CACHE CONFIGURATION
     */
    'cache.enabled' => true,
    'cache.duration' => 60,
    'cache.prefix' => 'tag-garden',
];
