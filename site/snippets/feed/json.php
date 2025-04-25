<?php
/**
 * JSON Feed Snippet for Kirby CMS
 * Compatible with JSON Feed version 1.1
 * https://www.jsonfeed.org/
 */

// Set content type header
header('Content-Type: application/feed+json');

// Build the feed object
$feed = [
  'version' => 'https://jsonfeed.org/version/1.1',
  'title' => $title ?? site()->title()->value(),
  'home_page_url' => url(),
  'feed_url' => $feedurl ?? url($link ?? ''),
  'description' => $description ?? site()->description()->value(),
  'authors' => [
    [
      'name' => site()->title()->value(),
      'url' => url()
    ]
  ],
  'language' => $language ?? 'en',
  'items' => []
];

// Add items to the feed
foreach ($items as $item) {
  // Process item data
  $itemData = null;

  // If we have a custom item callback function, use it to generate item data
  if (isset($item) && is_callable($item)) {
      $itemData = $item($item);
  }

  // Build the JSON item
  $jsonItem = [
    'id' => $itemData['guid'] ?? $item->url(),
    'url' => $itemData['link'] ?? $item->url(),
    'title' => $itemData['title'] ?? $item->title()->value(),
    'content_html' => $itemData['description'] ?? $item->text()->kirbytext()->value(),
    'date_published' => date('c', $item->date()->exists() ? strtotime($item->date()->value()) : $item->modified())
  ];

  // Add external_url for links section
  $section = $item->parent() ? $item->parent()->slug() : null;
  if ($section === 'links' && $item->website()->exists() && $item->website()->isNotEmpty()) {
    $jsonItem['external_url'] = $item->website()->value();
  }

  // Add tags if available
  if (isset($itemData['category']) && is_array($itemData['category'])) {
    $jsonItem['tags'] = array_map(function($cat) { return $cat['name']; }, $itemData['category']);
  } elseif ($item->tags()->isNotEmpty()) {
    $jsonItem['tags'] = $item->tags()->split();
  }

  // Add to items array
  $feed['items'][] = $jsonItem;
}

// Output the JSON feed
echo json_encode($feed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
