<?php

/**
 * Tag Garden Plugin Configuration
 *
 * Extended configuration options and helper functions for the Tag Garden plugin.
 * These extend the base options defined in index.php and provide utilities
 * for working with tags, content length, growth status, and more.
 *
 * Users can override any of these in their site's config.php by setting:
 * 'yourusername.tag-garden.option-name' => 'value'
 *
 * @version 1.0.0
 */

return [

    /**
     * ============================================================================
     * DISPLAY CONFIGURATION
     * ============================================================================
     * Controls how tags and content are displayed in various views
     */

    // Maximum number of tags to show in embedded section before "see more"
    'section.tag-limit' => 20,

    // Maximum number of content items in embedded section
    'section.content-limit' => 10,

    // Maximum number of related tags to show per item
    'related.tag-limit' => 5,

    // Show empty tags (tags with no content) in tag explorer
    'show.empty-tags' => false,

    // Show tag counts in displays
    'show.tag-counts' => true,


    /**
     * ============================================================================
     * SORTING CONFIGURATION
     * ============================================================================
     * Default sort methods and their display labels
     */

    // Available sort methods with human-readable labels
    'sort.methods' => [
        'planted' => 'Date Planted',
        'tended' => 'Last Tended',
        'notable' => 'Notable First',
        'length-asc' => 'Shortest First',
        'length-desc' => 'Longest First',
        'growth' => 'Growth Status',
        'title' => 'Alphabetical',
    ],

    // Default sort direction (asc or desc)
    'sort.direction' => 'desc',

    // Sort order for growth statuses (first to last in display)
    // When sorting by 'growth', items will appear in this order
    'growth.sort-order' => ['evergreen', 'budding', 'seedling', 'wilting'],


    /**
     * ============================================================================
     * TAG THEME CONFIGURATION
     * ============================================================================
     * Defines how tags can be organized and displayed by theme
     */

    // Theme definitions with colors and descriptions for UI display
    'theme.definitions' => [
        'topic' => [
            'label' => 'Topic',
            'color' => '#3b82f6', // blue
            'description' => 'Subject matter or area of focus',
            'icon' => '🏷️',
        ],
        'medium' => [
            'label' => 'Medium',
            'color' => '#8b5cf6', // purple
            'description' => 'Format or type of content',
            'icon' => '📝',
        ],
        'status' => [
            'label' => 'Status',
            'color' => '#10b981', // green
            'description' => 'Current state or progress',
            'icon' => '✓',
        ],
        'audience' => [
            'label' => 'Audience',
            'color' => '#f59e0b', // amber
            'description' => 'Intended readers or users',
            'icon' => '👥',
        ],
    ],


    /**
     * ============================================================================
     * LENGTH CALCULATION CONFIGURATION
     * ============================================================================
     * Settings for calculating and displaying content length
     */

    // Words per minute for reading time calculation (range for fast/slow readers)
    // These are already defined in index.php but repeated here for reference
    'reading.speed.min' => 167,  // Slow readers
    'reading.speed.max' => 285,  // Fast readers

    // Word count thresholds for length categorization
    // Used for filtering and displaying length categories
    'length.thresholds' => [
        'quick' => 500,      // Under 500 words (~2-3 min read)
        'short' => 1500,     // 500-1500 words (~3-7 min read)
        'medium' => 3000,    // 1500-3000 words (~7-15 min read)
        'long' => 5000,      // 3000-5000 words (~15-25 min read)
        // Anything over 5000 is 'epic'
    ],

    // Display labels for length categories
    'length.labels' => [
        'quick' => 'Quick read',
        'short' => 'Short read',
        'medium' => 'Medium read',
        'long' => 'Long read',
        'epic' => 'Epic read',
    ],


    /**
     * ============================================================================
     * GROWTH STATUS CONFIGURATION
     * ============================================================================
     * Defines the lifecycle states of content in the digital garden
     */

    // Growth status definitions with visual indicators and descriptions
    'growth.definitions' => [
        'seedling' => [
            'label' => 'Seedling',
            'emoji' => '🌱',
            'color' => '#86efac', // light green
            'description' => 'New ideas, rough notes, early drafts',
            'sort-order' => 2, // Used when sorting by growth status
        ],
        'budding' => [
            'label' => 'Budding',
            'emoji' => '🌿',
            'color' => '#4ade80', // green
            'description' => 'Growing content, being developed and refined',
            'sort-order' => 1,
        ],
        'evergreen' => [
            'label' => 'Evergreen',
            'emoji' => '🌲',
            'color' => '#22c55e', // strong green
            'description' => 'Mature, well-developed, regularly maintained',
            'sort-order' => 0, // First when sorting by growth
        ],
        'wilting' => [
            'label' => 'Wilting',
            'emoji' => '🍂',
            'color' => '#fb923c', // orange
            'description' => 'Outdated or archived, may need revision',
            'sort-order' => 3, // Last when sorting by growth
        ],
    ],


    /**
     * ============================================================================
     * CONTENT GROUP CONFIGURATION
     * ============================================================================
     * Defines the main content groups and their properties
     */

    // Content group definitions with metadata and visual indicators
    'group.definitions' => [
        'garden' => [
            'label' => 'Garden',
            'emoji' => '🌸',
            'color' => '#ec4899', // pink
            'description' => 'Writing and long-form content',
            'types' => ['journal', 'essays', 'articles', 'books'],
        ],
        'soil' => [
            'label' => 'Soil',
            'emoji' => '📚',
            'color' => '#8b5cf6', // purple
            'description' => 'Links, references, and collections',
            'types' => ['links', 'library', 'quotes'],
        ],
        'work' => [
            'label' => 'Work',
            'emoji' => '💼',
            'color' => '#3b82f6', // blue
            'description' => 'Professional projects and experience',
            'types' => ['overview', 'experience', 'projects', 'work'],
        ],
        'about' => [
            'label' => 'About',
            'emoji' => '👤',
            'color' => '#10b981', // green
            'description' => 'Personal information and profiles',
            'types' => ['strengths', 'skills', 'about', 'now'],
        ],
    ],


    /**
     * ============================================================================
     * TAG CLOUD CONFIGURATION
     * ============================================================================
     * Settings for displaying tags in cloud/list views
     */

    // Font size range for tag cloud (in rem units)
    'cloud.font-min' => 0.875,  // Smallest tags
    'cloud.font-max' => 2,      // Largest tags

    // Minimum usage count for tag to appear in cloud
    'cloud.min-count' => 1,

    // Maximum number of tags to show in cloud view (0 = unlimited)
    'cloud.max-tags' => 0,


    /**
     * ============================================================================
     * URL & ROUTING CONFIGURATION
     * ============================================================================
     * Settings for tag page URLs and navigation
     */

    // Base URL for tag pages (e.g., /tags/design)
    'url.base' => 'tags',

    // URL parameter for sort method
    'url.sort-param' => 'sort',

    // URL parameter for filter by group
    'url.group-param' => 'group',

    // URL parameter for filter by theme
    'url.theme-param' => 'theme',

    // URL separator for multiple tags (e.g., /tags/design+code)
    'url.tag-separator' => '+',


    /**
     * ============================================================================
     * CACHE CONFIGURATION
     * ============================================================================
     * Performance optimization settings
     */

    // Enable caching of tag collections
    'cache.enabled' => true,

    // Cache duration in minutes
    'cache.duration' => 60,

    // Cache key prefix
    'cache.prefix' => 'tag-garden',
];
