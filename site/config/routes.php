// In your config.php or routes.php
return [
  'routes' => [
    [
      'pattern' => 'tags/(:any)',
      'action' => function ($tag) {
        return page('tags')->render([
          'tag' => urldecode($tag)
        ]);
      }
    ]
  ]
];
