<?php

use Kirby\Http\Response;
use Kirby\Toolkit\Str;

return [
  [
    'pattern' => '/pwa/share',
    'method'  => 'POST',
    'action'  => function () {
      $configToken = option('pwa.share.token');
      $sentToken   = get('token');

      // --- AUTH ---
      if (!$configToken || $sentToken !== $configToken) {
        return new Response('Unauthorized', 401);
      }

      // --- INPUTS ---
      $title = get('title');
      $text  = get('text');
      $url   = get('url');

      if (!$url) {
        return new Response('Missing URL', 400);
      }

      // --- DOMAIN / TLD PARSE ---
      $host = parse_url($url, PHP_URL_HOST);
      $tld  = '';
      if ($host) {
        $parts = explode('.', $host);
        $tld   = end($parts);
      }

      // --- TAG INFERENCE ---
      $tags = [];
      $tagMap = [
        'github.com'     => ['code', 'project'],
        'youtube.com'    => ['video'],
        'youtu.be'       => ['video'],
        'medium.com'     => ['article'],
        'substack.com'   => ['article'],
        'arxiv.org'      => ['research'],
        'wikipedia.org'  => ['reference'],
        'nytimes.com'    => ['news'],
        'reddit.com'     => ['discussion'],
        'figma.com'      => ['design', 'tool'],
        'dribbble.com'   => ['design', 'inspiration']
      ];

      foreach ($tagMap as $pattern => $autoTags) {
        if (str_contains($host, $pattern)) {
          $tags = array_merge($tags, $autoTags);
        }
      }

      // --- CREATE PAGE ---
      $parent = page('links'); // adjust if your parent is named differently
      $slug   = Str::slug($title ?? $url ?? 'shared-link');

      $page = $parent->createChild([
        'slug'     => $slug,
        'template' => 'link',
        'content'  => [
          'title'   => $title ?? 'Untitled',
          'website' => $url,
          'tld'     => $tld,
          'tags'    => implode(',', array_unique($tags)),
          'text'    => $text ?? ''
        ]
      ]);

      // --- RESPONSE ---
      return new Response(json_encode([
        'status' => 'ok',
        'slug'   => $page->slug(),
        'url'    => $page->url(),
        'tags'   => $tags
      ]), 'application/json');
    }
  ]
];
