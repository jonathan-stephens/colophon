<?php

// Feed configuration - add new sections here
return [
    'sections' => [
        'journal' => [
            'description' => 'Personal writings and articles from Jonathan Stephens',
            'limit' => 20,
        ],
        'links' => [
            'description' => 'Interesting links and bookmarks curated by Jonathan Stephens',
            'limit' => 20,
        ],
        'articles' => [
            'description' => 'Stuff I have written, by Jonathan Stephens',
            'limit' => 20,
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
