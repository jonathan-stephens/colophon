<?php
return [
    'panel' => [
        'install' => true,
        'debug' => true,
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
    'routes' => [
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
    ],
        'routes' => [
            // RSS, JSON and Atom feeds for links
            [
                'pattern' => 'links/(:any)',
                'method' => 'GET',
                'action'  => function ($format) {
                    // Valid formats list
                    $validFormats = ['rss', 'json', 'atom'];

                    // Default to RSS if format isn't valid
                    if (!in_array($format, $validFormats)) {
                        $format = 'rss';
                    }

                    return feed(fn() => page('links')->children()->listed()->flip()->limit(20), [
                        'title' => 'My Links Feed',
                        'description' => 'A collection of interesting links I want to share',
                        'link' => 'links',
                        'snippet' => 'feed/' . $format,
                        'feedurl' => site()->url() . '/links/' . $format . '/',
                    ]);
                }
            ],
        ],
    'mauricerenck.indieConnector.secret' => 'supercalifragilisticexpialidocious',
    'mauricerenck.indieConnector.sqlitePath' => 'content/.sqlite/',

    'jonathanstephens.timekeeper' => [
        'useClientTime' => true,
        'allowManualOverride' => true,
        'cookieDuration' => 60 * 60 * 24
    ]
];
