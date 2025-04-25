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
              $validSections = ['journal', 'links'];
              // Check if valid section
              if (!in_array($section, $validSections) || !page($section)) {
                  return false;
              }

              // Determine the format for the snippet
              $snippetFormat = $format;
              if ($format === 'feed.json') $snippetFormat = 'json';
              if ($format === 'feed.atom') $snippetFormat = 'atom';

              // Section-specific descriptions
              $descriptions = [
                  'journal' => 'Personal writings and articles from Jonathan Stephens',
                  'links' => 'Interesting links and bookmarks curated by Jonathan Stephens'
              ];

              // Basic feed options
              $options = [
                  'title' => site()->title() . ' - ' . ucfirst($section) . ' ' . strtoupper($snippetFormat),
                  'description' => $descriptions[$section] ?? 'The latest ' . $section . ' from ' . site()->title(),
                  'link' => $section,
                  'snippet' => 'feed/' . $snippetFormat,
                  'feedurl' => site()->url() . '/' . $section . '/' . $format,
                  'modified' => time(),
                  'language' => 'en',
                  'managingEditor' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                  'webMaster' => 'hello@jonathanstephens.us (Jonathan Stephens)',

                  // Custom item generation for additional fields
                  'item' => function($page) use ($section) {
                      $item = [
                          'title' => $page->title()->value(),
                          'link' => $page->url(),
                          'description' => $page->text()->kirbytext()->value(),
                          'pubDate' => $page->date()->exists() ? date('r', strtotime($page->date()->value())) : date('r', $page->modified()),
                      ];

                      // Add GUID (use website URL for links section)
                      if ($section === 'links' && $page->website()->exists() && $page->website()->isNotEmpty()) {
                          $item['guid'] = $page->website()->value();
                      } else {
                          $item['guid'] = $page->url();
                      }

                      // Add categories/tags if they exist
                      if ($page->tags()->exists() && $page->tags()->isNotEmpty()) {
                          $categories = [];
                          foreach ($page->tags()->split() as $tag) {
                              $categories[] = ['name' => $tag];
                          }
                          if (!empty($categories)) {
                              $item['category'] = $categories;
                          }
                      }

                      return $item;
                  }
                ];

              // Special handling for atom feeds to fix timestamp issue
              if ($snippetFormat === 'atom') {
                  $options['datefield'] = function($page) {
                      $date = $page->date()->toDate();
                      return $date ? $date : $page->modified();
                  };
              }

              return feed(fn() => page($section)->children()->listed()->flip()->limit(20), $options);
          }
      ],
      // Main RSS feed for all content
      [
          'pattern' => 'rss',
          'method' => 'GET',
          'action'  => function () {
              // Collect entries from all sections
              $items = new Pages();
              $sections = ['journal', 'links'];
              foreach ($sections as $section) {
                  if ($page = page($section)) {
                      $items = $items->add($page->children()->listed());
                  }
              }

              return feed(fn() => $items->sortBy('date', 'desc')->limit(20), [
                  'title' => site()->title() . ' - All Content RSS',
                  'description' => 'The latest content from Jonathan Stephens',
                  'link' => 'rss',
                  'snippet' => 'feed/rss',
                  'feedurl' => site()->url() . '/rss',
                  'modified' => time(),
                  'language' => 'en',
                  'managingEditor' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                  'webMaster' => 'hello@jonathanstephens.us (Jonathan Stephens)',

                  // Inside the main RSS feed action:
                  'item' => function($page) {
                      $section = $page->parent()->slug();

                      $item = [
                          'title' => $page->title()->value(),
                          'link' => $page->url(),
                          'description' => $page->text()->kirbytext()->value(),
                          'pubDate' => $page->date()->exists() ? date('r', strtotime($page->date()->value())) : date('r', $page->modified()),
                      ];

                      // Add GUID (use website URL for links section)
                      if ($section === 'links' && $page->website()->exists() && $page->website()->isNotEmpty()) {
                          $item['guid'] = $page->website()->value();
                      } else {
                          $item['guid'] = $page->url();
                      }

                      // Add categories/tags if they exist
                      if ($page->tags()->exists() && $page->tags()->isNotEmpty()) {
                          $categories = [];
                          foreach ($page->tags()->split() as $tag) {
                              $categories[] = ['name' => $tag];
                          }
                          if (!empty($categories)) {
                              $item['category'] = $categories;
                          }
                      }

                      return $item;
                  }              ]);
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
