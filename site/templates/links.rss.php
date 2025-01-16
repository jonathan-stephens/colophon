<?php
// Set the content type
header('Content-type: application/xml');

// Get all links
$links = $page->children()
    ->listed()
    ->sortBy('date', 'desc')
    ->limit(20);

// Create the RSS feed
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
  <channel>
    <title><?= $site->title() ?> Links</title>
    <link><?= $page->url() ?></link>
    <description>Latest links from <?= $site->title() ?></description>
    <?php foreach($links as $link): ?>
    <item>
      <title><?= $link->title()->xml() ?></title>
      <link><?= $link->website()->xml() ?></link>
      <guid><?= $link->url() ?></guid>
      <pubDate><?= date('r', $link->date()->toDate()) ?></pubDate>
      <description><?= $link->text()->excerpt(300)->xml() ?></description>
    </item>
    <?php endforeach ?>
  </channel>
</rss>
