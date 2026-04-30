<?php

/**
 * Tag Garden Plugin Configuration
 *
 * Extended configuration options for the Tag Garden plugin.
 * These are loaded via site/config/config.php with the namespace:
 * 'jonathanstephens.tag-garden' => require __DIR__ . '/../plugins/tag-garden/config/options.php'
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
        'growth' => ['label' => 'Growth Status'],
    ],
    'sort.direction' => 'desc',
    'growth.sort-order' => ['evergreen', 'crowning', 'rooting', 'sprouting', 'sown'],

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
    'content.groups' => [
        'garden' => ['journal', 'essay', 'article', 'book'],
        'soil'   => ['link', 'library', 'quote'],
        'work'   => ['project', 'experience', 'work'],
        'about'  => ['strengths', 'skills', 'now'],
    ],
    'growth.definitions' => [
        'sown' => [
            'label' => 'Sown',
            'emoji' => '🌱',
            'color' => '#86efac',
            'description' => 'A placholder, a seed just planted; showing what sort of content I may want to grow there',
            'sort-order' => 0,
        ],
        'sprouting' => [
            'label' => 'Sprouting',
            'emoji' => '🌿',
            'color' => '#4ade80',
            'description' => 'Something is beginning to form, exploring what it may become; not refined, details and definitions may be mission.',
            'sort-order' => 1,
        ],
        'rooting' => [
            'label' => 'Rooting',
            'emoji' => '🌲',
            'color' => '#22c55e',
            'description' => 'Foundations are stabilizing, stronger structures forming, but not all details are completely added or yet to be worked out; content is more resilient &  persistent.',
            'sort-order' => 2,
        ],
        'crowning' => [
            'label' => 'Crowning',
            'emoji' => '🍂',
            'color' => '#fb923c',
            'description' => 'Mature enough to be published, focused on feasibility; what needs to be adjusted, to avoid content crown shyness?',
            'sort-order' => 3,
        ],
        'evergreen' => [
            'label' => 'Evergreen',
            'emoji' => '🍂',
            'color' => '#fb923c',
            'description' => 'There may be some light edits and occasional pruning needed, but is considered complete, in maintenance mode, and syndicated.',
            'sort-order' => 4,
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
            'types' => ['project', 'experience', 'work'],
        ],
        'about' => [
            'label' => 'About',
            'emoji' => '👤',
            'color' => '#10b981',
            'description' => 'Personal information and profiles',
            'types' => ['strengths', 'skills', 'now'],
        ],
    ],

    /**
     * URL & ROUTING CONFIGURATION
     */
    'url.base' => 'tags',
    'url.sort-param' => 'sort',
    'url.group-param' => 'group',
    'url.theme-param' => 'theme',
    'url.tag-separator' => ',',

    /**
     * CACHE CONFIGURATION
     */
    'cache.enabled' => true,
    'cache.duration' => 60,
    'cache.prefix' => 'tag-garden',
];
