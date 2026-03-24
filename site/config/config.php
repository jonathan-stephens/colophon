<?php
require_once __DIR__ . '/helpers.php';

return [

  'debug' => true,
    'panel' => [
        'install' => false,
    ],
    'date.timezone' => 'America/New_York',
    'ready' => function ($kirby) {
        return [
           'pechente.kirby-admin-bar' => [
                'active' => $kirby->user() !== null
            ]
        ];
    },
    'api' => [
      'basicAuth' => true,
      'allowInsecure' => false, // Only for local development - set to false in production!
    ],
    'cache' => [
      'tags' => [
          'type' => 'file'
      ]
    ],
    'tags.api.url' => function () {
      return kirby()->url() . '/api/cached-tags';
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
    /*
    |--------------------------------------------------------------------------
    | Stream options
    |--------------------------------------------------------------------------
    |
    | subtrees – 'all' crawls the entire site, or pass an array of top-level
    |             page IDs to limit scope: ['books', 'essays', 'notes']
    |
    | minDepth  – pages at this depth or deeper are included.
    |             Depth 1 = top-level section pages (/books, /essays).
    |             Depth 2 = their children (/books/my-book) — usually what you want.
    |
    | maxDepth  – null = no limit; set an integer to cap how deep the crawl goes.
    |
    | exclude   – page IDs that are always omitted regardless of other settings.
    |
    */
    'stream' => [
        'subtrees' => 'all',
        'minDepth' => 2,
        'maxDepth' => null,
        'exclude'  => ['stream', 'error'],
    ],

    'moinframe.loop.enabled' => false,
    'timnarr.imagex' => [
      'cache' => true,
      'formats' => ['avif', 'webp'], // our modern formats
      'noSrcsetInImg' => false, // skip srcset in <img> with initial img-format -> less HTML
      'relativeUrls' => false, // relative URLs -> less HTML
    ],
    'kirbytext' => [
      'image' => [
        'width' => 'auto',
      ],
    ],
    'thumbs' => [
      'driver'    => 'gd',
      'interlace' => true,
      'format'    => 'webp',
      'blurred' => ['blur' => true],
      'srcsets' => [
        'default' => [ // preset for jpeg and png
          '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 80],
          '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 80],
          '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 80],
        ],
        'webp' => [ // preset for webp
          '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
          '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
          '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 85, 'format' => 'webp', 'sharpen' => 10],
        ],
        'avif' => [ // preset for avif
          '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 65, 'format' => 'avif', 'sharpen' => 25],
          '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 65, 'format' => 'avif', 'sharpen' => 25],
          '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 85, 'format' => 'avif', 'sharpen' => 25],
        ]
      ],
    ],
      // Tag Garden Plugin Extended Configuration
      'yourusername.tag-garden' => require __DIR__ . '/../plugins/tag-garden/config/options.php',
      'pageModels' => [
    'tag' => 'TagGarden\TagCollection',
],

    'afbora.kirby-minify-html' => [
      'enabled' => function () {
        return !kirby()->user();
      },
        'ignore' => [
            'sitemap',
            'rss'
        ],
        'options' => [
            'doOptimizeViaHtmlDomParser'     => true,
            'doRemoveSpacesBetweenTags'      => false,
            'doMakeSameDomainsLinksRelative' => ['jonathanstephens.us']
        ]
    ],
    // Load custom helpers
    'hooks' => [
        'system.loadPlugins:after' => function () {
            require_once __DIR__ . '/../helpers/feeds.php';
        },
        'page.create:after' => function ($page) {

          // Only apply if the page has a location field
          if (!$page->blueprint()->field('locationAuthored')) {
            return;
          }

          // Only apply if location is empty
          if ($page->location()->isNotEmpty()) {
            return;
          }

          $default = site()->default_location()->value();

          if (!$default) {
            return;
          }

          $page->update([
            'locationAuthored' => $default
          ]);
        }
    ],
    'routes' => [
        // Section-specific feeds: /journal/rss, /links/rss, /journal/feed, /links/feed
        [
            'pattern' => '(:any)/(rss|feed)',
            'method' => 'GET',
            'action'  => function ($section, $format) {
                // Set custom filename header before generating feed
                $filename = $section . '.' . $format;
                header('Content-Disposition: inline; filename="' . $filename . '"');

                return generateSectionFeed($section, $format);
            }
        ],

        // Tag-based feeds for specific sections: /journal/tags/design/rss, /links/tags/css/feed
        [
            'pattern' => '(:any)/tags/(:any)/(rss|feed)',
            'method' => 'GET',
            'action'  => function ($section, $tag, $format) {
                $filename = $section . '-' . $tag . '.' . $format;
                header('Content-Disposition: inline; filename="' . $filename . '"');

                return generateTagFeed($tag, $format, $section);
            }
        ],

        // Tag-based feeds across all content: /tags/design/rss, /tags/css/feed
        [
            'pattern' => 'tags/(:any)/(rss|feed)',
            'method' => 'GET',
            'action'  => function ($tag, $format) {
                $filename = 'tags-' . $tag . '.' . $format;
                header('Content-Disposition: inline; filename="' . $filename . '"');

                return generateTagFeed($tag, $format);
            }
        ],

        // Main feeds: /rss, /feed
        [
            'pattern' => '(rss|feed)',
            'method' => 'GET',
            'action'  => function ($format) {
                // Set custom filename header before generating feed
                $siteName = Str::slug(site()->title());
                $filename = $siteName . '-all.' . $format;
                header('Content-Disposition: inline; filename="' . $filename . '"');

                return generateMainFeed($format);
            }
        ],

        // Tags handling route (keep your existing one)
        [
            'pattern' => 'tags/(:any)',
            'action'  => function ($tag) {
                $tags = array_map('trim', explode(',', urldecode($tag)));
                $tagsPage = page('tags');

                if (!$tagsPage) {
                    return site()->errorPage();
                }

                return $tagsPage->render(['filterTags' => $tags]);
            }
        ],

        // Sitemap
        [
            'pattern' => 'sitemap.xml',
            'method' => 'GET',
            'action'  => function () {
                return sitemap(fn() => site()->index()->listed()->limit(50000));
            }
        ],

        // Sitemap stylesheet
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
