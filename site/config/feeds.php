<?php

// Feed configuration - add new sections here
return [
    'sections' => [
      // Individual Content Types
        'journal' => [
            'description' => 'Personal writings and articles from Jonathan Stephens',
            'limit' => 20,
        ],
        'links' => [
            'description' => 'Interesting links and bookmarks curated by Jonathan Stephens',
            'limit' => 20,
        ],
        'library' => [
            'description' => 'Interesting links and bookmarks curated by Jonathan Stephens',
            'limit' => 20,
        ],
        'articles' => [
            'description' => 'Stuff I have written, by Jonathan Stephens',
            'limit' => 20,
        ],
        'essays' => [
            'description' => 'Stuff I have written, by Jonathan Stephens',
            'limit' => 20,
        ],
        // Combined Content Types
        'garden' => [
            'description' => 'All writings from my digital garden - journal entries and articles',
            'limit' => 30,
            'sections' => ['journal', 'articles', 'essays'], // Combine multiple sections
        ],
        'soil' => [
            'description' => 'All writings from my digital garden - journal entries and articles',
            'limit' => 30,
            'sections' => ['links', 'library'], // Combine multiple sections
        ],
        'stream' => [
            'description' => 'All writings from my digital garden - journal entries and articles',
            'limit' => 30,
            'sections' => ['journal', 'links', 'library', 'articles', 'essays'], // Combine multiple sections
        ],

        // Add more sections easily:
        // 'photos' => [
        //     'description' => 'Photography from Jonathan Stephens',
        //     'limit' => 30,
        // ],
    ],

    'defaults' => [
        'language' => 'en',
        'managingEditor' => 'hello@jonathanstephens.us (Jonathan Stephens)',
        'webMaster' => 'hello@jonathanstephens.us (Jonathan Stephens)',
    ]
];
