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
    'mauricerenck.indieConnector.send.url-fields' => [
      'text:text',
      'main:text',
      'website:text',
    ],
    'jonathanstephens.timekeeper' => [
        'useClientTime' => true,
        'allowManualOverride' => true,
        'cookieDuration' => 60 * 60 * 24
    ],
    'johannschopplich.locked-pages' => [
        'slug' => 'locked',
        'title' => 'Protected Page',
        'error' => [
            'csrf' => 'The CSRF-Token is not correct.',
            'password' => 'The password is not correct.',
        ]
    ],
    'moinframe.loop.enabled' => false,
    'timnarr.imagex' => [
      'formats' => ['avif', 'webp'], // our modern formats
      'noSrcsetInImg' => false, // skip srcset in <img> with initial img-format -> less HTML
      'relativeUrls' => false, // relative URLs -> less HTML
    ],
    'thumbs' => [
      'driver'    => 'gd',
      'interlace' => true,
      'format'    => 'webp',
      'srcsets' => [
        'default' => [ // preset for jpeg and png
          '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 80],
          '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 80],
          '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 80],
        ],
        'webp' => [ // preset for webp
          '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
          '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
          '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
        ],
        'avif' => [ // preset for avif
          '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 65, 'format' => 'avif', 'sharpen' => 25],
          '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 65, 'format' => 'avif', 'sharpen' => 25],
          '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 65, 'format' => 'avif', 'sharpen' => 25],
        ]
      ],
    ],
    'routes' => [
      // Section-specific feeds: /journal/rss, /links/rss, /journal/feed, /links/feed
      [
          'pattern' => '(:any)/(rss|feed)',
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
              if ($format === 'feed') $snippetFormat = 'json';

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

              return feed(fn() => page($section)->children()->listed()->flip()->limit(20), $options);
          }
      ],
      // Main feeds: /rss, /feed
      [
          'pattern' => '(rss|feed)',
          'method' => 'GET',
          'action'  => function ($format) {
              // Collect entries from all sections
              $items = new Pages();
              $sections = ['journal', 'links'];
              foreach ($sections as $section) {
                  if ($page = page($section)) {
                      $items = $items->add($page->children()->listed());
                  }
              }

              // Determine the format for the snippet
              $snippetFormat = $format;
              if ($format === 'feed') $snippetFormat = 'json';

              return feed(fn() => $items->sortBy('date', 'desc')->limit(20), [
                  'title' => site()->title() . ' - All Content ' . strtoupper($snippetFormat),
                  'description' => 'The latest content from Jonathan Stephens',
                  'link' => $format,
                  'snippet' => 'feed/' . $snippetFormat,
                  'feedurl' => site()->url() . '/' . $format,
                  'modified' => time(),
                  'language' => 'en',
                  'managingEditor' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                  'webMaster' => 'hello@jonathanstephens.us (Jonathan Stephens)',

                  // Inside the main feed action:
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
                  }
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
      ],
      [
    'pattern' => 'sitemap.xml',
    'method' => 'GET',
    'action'  => function () {
        // while this would be possible
        // return site()->index()->listed()->limit(50000)->sitemap();

        // using a closure allows for better performance on a cache hit
        return sitemap(fn() => site()->index()->listed()->limit(50000));
          }
      ],
      // (optional) Add stylesheet for human readable version of the xml file.
      // With that stylesheet visiting the xml in a browser will per-generate the images.
      // The images will NOT be pre-generated if the xml file is downloaded (by google).
      [
          'pattern' => 'sitemap.xsl',
          'method' => 'GET',
          'action'  => function () {
              snippet('feed/sitemapxsl');
              die;
          }
      ],
    ]
  ];
