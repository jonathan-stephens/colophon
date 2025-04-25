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
        // Section-specific RSS feeds (journal/rss, links/rss)
        [
            'pattern' => '(:any)/rss',
            'method' => 'GET',
            'action'  => function ($section) {
                // Valid sections
                $validSections = ['journal', 'links'];
                // Check if valid section
                if (!in_array($section, $validSections) || !page($section)) {
                    return false;
                }
                // Section-specific descriptions
                $descriptions = [
                    'journal' => 'Personal writings and articles from Jonathan Stephens',
                    'links' => 'Interesting links and bookmarks curated by Jonathan Stephens'
                ];
                // Create pages collection first
                $collection = page($section)->children()->listed()->flip()->limit(20);

                // Create custom options array to pass to feed snippet
                $customData = [
                    'customGuids' => [],
                    'feedCategories' => []
                ];

                // Process items manually to add custom fields
                foreach($collection as $page) {
                    // Handle the guid for links
                    if ($section === 'links' && $page->website()->exists() && $page->website()->isNotEmpty()) {
                        // Store in custom array instead of as property
                        $customData['customGuids'][$page->id()] = $page->website()->value();
                    }

                    // Process tags if they exist
                    if ($page->tags()->exists() && $page->tags()->isNotEmpty()) {
                        $tags = $page->tags()->split(',');
                        $customData['feedCategories'][$page->id()] = array_map('trim', $tags);
                    }
                }

                // Basic feed options
                $options = [
                    'title' => site()->title() . ' - ' . ucfirst($section),
                    'description' => $descriptions[$section] ?? 'The latest ' . $section . ' from ' . site()->title(),
                    'link' => $section,
                    'snippet' => 'feed/custom-rss', // Use our custom RSS snippet
                    'feedurl' => site()->url() . '/' . $section . '/rss',
                    'modified' => time(),
                    'language' => 'en',
                    'managingEditor' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                    'webMaster' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                    // Fix datefield to ensure it's reliable
                    'date' => function($page) {
                      // Handle dates explicitly
                      if ($page->date()->exists() && $page->date()->isNotEmpty()) {
                          // Store the formatted date directly in customData
                          $customData['pubDates'][$page->id()] = date('r', $page->date()->toDate('U'));
                      } else {
                          $customData['pubDates'][$page->id()] = date('r', $page->modified());
                      }
                    },
                    // Pass the custom data
                    'customData' => $customData
                ];

                return feed($collection, $options);
            }
        ],

        // Main RSS feed combining all content
        [
            'pattern' => 'feed',
            'method' => 'GET',
            'action'  => function () {
                // Valid sections to include in the main feed
                $validSections = ['journal', 'links'];

                // Start with an empty collection
                $collection = null;

                // Build combined collection from all valid sections
                foreach ($validSections as $section) {
                    if (page($section)) {
                        $sectionPages = page($section)->children()->listed();
                        if ($collection === null) {
                            $collection = $sectionPages;
                        } else {
                            $collection = $collection->merge($sectionPages);
                        }
                    }
                }

                // Sort combined collection by date and limit
                $collection = $collection->sortBy('date', 'desc')->flip()->limit(30);

                // Create custom options array to pass to feed snippet
                $customData = [
                    'customGuids' => [],
                    'feedCategories' => []
                ];

                // Process items manually to add custom fields
                foreach($collection as $page) {
                    // Add section as category
                    $section = $page->parent()->id();
                    $customData['feedCategories'][$page->id()] = [$section];

                    // Handle the guid for links
                    if ($section === 'links' && $page->website()->exists() && $page->website()->isNotEmpty()) {
                        $customData['customGuids'][$page->id()] = $page->website()->value();
                    }

                    // Add additional tags if they exist
                    if ($page->tags()->exists() && $page->tags()->isNotEmpty()) {
                        $tags = $page->tags()->split(',');
                        if (isset($customData['feedCategories'][$page->id()])) {
                            $customData['feedCategories'][$page->id()] = array_merge(
                                $customData['feedCategories'][$page->id()],
                                array_map('trim', $tags)
                            );
                        } else {
                            $customData['feedCategories'][$page->id()] = array_map('trim', $tags);
                        }
                    }
                }

                // Basic feed options
                $options = [
                    'title' => site()->title() . ' - All Content',
                    'description' => 'Recent content from all sections of ' . site()->title(),
                    'link' => '/',
                    'snippet' => 'feed/custom-rss', // Use custom RSS snippet
                    'feedurl' => site()->url() . '/feed',
                    'modified' => time(),
                    'language' => 'en',
                    'managingEditor' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                    'webMaster' => 'hello@jonathanstephens.us (Jonathan Stephens)',
                    // Fix datefield to ensure it's reliable
                    'date' => function($page) {
                      // Handle dates explicitly
                      if ($page->date()->exists() && $page->date()->isNotEmpty()) {
                          // Store the formatted date directly in customData
                          $customData['pubDates'][$page->id()] = date('r', $page->date()->toDate('U'));
                      } else {
                          $customData['pubDates'][$page->id()] = date('r', $page->modified());
                      }
                    },
                    // Pass the custom data
                    'customData' => $customData
                ];

                return feed($collection, $options);
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
