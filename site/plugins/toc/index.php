<?php
use Kirby\Toolkit\Str;

Kirby::plugin('k-cookbook/toc', [
  'hooks' => [
    'kirbytext:after' => [
      function($text) {
        // Add IDs to headlines (h2 through h6)
        $headlines = option('k-cookbook.toc.headlines', 'h2|h3|h4|h5|h6');
        $headlinesPattern = is_array($headlines) ? implode('|', $headlines) : $headlines;

        return preg_replace_callback('!<(' . $headlinesPattern . ')>(.*?)</\\1>!s', function ($match) {
            $id = Str::slug(Str::unhtml($match[2]));
            return '<' . $match[1] . ' id="' . $id . '"><a href="#' . $id . '">' . $match[2] . '</a></' . $match[1] . '>';
        }, $text);
      },
    ]
  ],
  'snippets' => [
    'toc' => __DIR__ . '/snippets/toc.php'
  ],
  'fieldMethods' => [
    'toc' => function($field, $minLevel = 2, $maxLevel = 6) {
      // Ensure parameters are integers
      $minLevel = (int)$minLevel;
      $maxLevel = (int)$maxLevel;

      // Get content and prepare pattern to match specified headline levels
      $text = $field->value();
      $levels = [];
      for ($i = $minLevel; $i <= $maxLevel; $i++) {
        $levels[] = 'h' . $i;
      }

      // Find all headlines in the content
      preg_match_all('!<(' . implode('|', $levels) . ').*?>(.*?)</\\1>!s', $text, $matches, PREG_SET_ORDER);

      // Process matches into flat array
      $items = [];
      foreach ($matches as $match) {
        $level = (int)substr($match[1], 1);
        $text = trim(strip_tags($match[2]));

        $items[] = [
          'text' => $text,
          'url' => '#' . Str::slug(Str::unhtml($text)),
          'level' => $level
        ];
      }

      if (empty($items)) return [];

      // Build hierarchical structure
      $tree = [];
      $stack = [['level' => $minLevel - 1, 'children' => &$tree]];

      foreach ($items as $item) {
        // Find parent in stack
        while (end($stack)['level'] >= $item['level']) {
          array_pop($stack);
        }

        // Add item to parent's children
        $parent = &$stack[count($stack) - 1]['children'];
        $current = [
          'text' => $item['text'],
          'url' => $item['url'],
          'level' => $item['level'],
          'children' => []
        ];

        $parent[] = $current;
        $stack[] = ['level' => $item['level'], 'children' => &$parent[count($parent) - 1]['children']];
      }

      return $tree;
    }
  ]
]);
