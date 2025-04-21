<?php
return [
    'panel' => [
        'install' => false,
        'debug' => false,
    ],
    'date.timezone' => 'America/New_York',
    'jonathanstephens.template-handler' => [
        'cache' => true,
        'defaultTemplate' => 'default'
    ],
    'ready' => function ($kirby) {
        return [
            'pechente.kirby-admin-bar' => [
                'active' => $kirby->user() !== null
            ]
        ];
    },
    'mauricerenck.indieConnector.secret' => 'supercalifragilisticexpialidocious',
    'mauricerenck.indieConnector.sqlitePath' => 'content/.sqlite/',

    'jonathanstephens.timekeeper' => [
        'useClientTime' => true,
        'allowManualOverride' => true,
        'cookieDuration' => 60 * 60 * 24
    ],
    'routes' => [
        // Generic feed handler for specific formats only
        [
            'pattern' => '(:any)/(rss|feed.json|feed.atom)',
            'method' => 'GET',
            'action'  => function ($section, $format) {
                // Valid sections
                $validSections = ['journal', 'links', 'articles', 'notes'];

                // Check if valid section
                if (!in_array($section, $validSections) || !page($section)) {
                    // Only handle routes that match our pattern, let Kirby handle the rest
                    return null;
                }

                // Determine the format for the snippet
                $snippetFormat = $format;
                if ($format === 'feed.json') $snippetFormat = 'json';
                if ($format === 'feed.atom') $snippetFormat = 'atom';

                return feed(fn() => page($section)->children()->listed()->flip()->limit(20), [
                    'title' => site()->title() . ' - ' . ucfirst($section) . ' ' . strtoupper($snippetFormat),
                    'description' => 'The latest ' . $section . ' from ' . site()->title(),
                    'link' => $section,
                    'snippet' => 'feed/' . $snippetFormat,
                    'feedurl' => site()->url() . '/' . $section . '/' . $format,
                ]);
            }
        ],

        // Main RSS feed for all content
        [
            'pattern' => 'rss',
            'method' => 'GET',
            'action'  => function () {
                // Collect entries from all sections
                $items = new Pages();
                $sections = ['journal', 'links', 'articles', 'notes'];

                foreach ($sections as $section) {
                    if ($page = page($section)) {
                        $items = $items->add($page->children()->listed());
                    }
                }

                return feed(fn() => $items->sortBy('date', 'desc')->limit(20), [
                    'title' => site()->title() . ' - All Content RSS',
                    'description' => 'The latest content from ' . site()->title(),
                    'link' => 'rss',
                    'snippet' => 'feed/rss',
                    'feedurl' => site()->url() . '/rss',
                ]);
            }
        ],

        // Tags handling route
        [
            'pattern' => 'tags/(:any)',
            'action'  => function ($tag) {
                // Handle comma-separated tags
                $tags = explode(',', urldecode($tag));
                $tags = array_map('trim', $tags);
                // Check if tags page exists
                $tagsPage = page('tags');
                if (!$tagsPage) {
                    return site()->errorPage();
                }
                // Pass the filtered tags to the template
                return $tagsPage->render(['filterTags' => $tags]);
            }
        ]
    ]
  ];
