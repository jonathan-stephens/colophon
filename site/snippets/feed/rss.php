<?php
/**
 * Enhanced RSS Feed Snippet for Kirby CMS
 * Using Kirby's built-in XML helper functions
 */
echo '<?xml version="1.0" encoding="utf-8"?>';
?>

  <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
      <title><?= \Kirby\Toolkit\Xml::encode($title ?? site()->title()) ?></title>
      <description><?= \Kirby\Toolkit\Xml::encode($description ?? site()->description()) ?></description>
      <language><?= $language ?? 'en-us' ?></language>
      <lastBuildDate><?= date('r', is_int($modified) ? $modified : time()) ?></lastBuildDate>
      <?php if (isset($feedurl)): ?><atom:link href="<?= \Kirby\Toolkit\Xml::encode($feedurl) ?>" rel="self" type="application/rss+xml" /><?php endif ?>

      <?php if (isset($managingEditor)): ?><managingEditor><?= \Kirby\Toolkit\Xml::encode($managingEditor) ?></managingEditor><?php endif ?>

      <?php if (isset($webMaster)): ?><webMaster><?= \Kirby\Toolkit\Xml::encode($webMaster) ?></webMaster><?php endif ?>

      <copyright><?= \Kirby\Toolkit\Xml::encode($copyright ?? '© ' . date('Y') . ' ' . site()->title()) ?></copyright>
      <?php foreach ($items as $item): ?>
<item>
      <?php
        // Process item data
        $itemData = null;

        // If we have a custom item callback function, use it to generate item data
        if (isset($item) && is_callable($item)) {
            $itemData = $item($item);
        }
      ?>
  <title><?= \Kirby\Toolkit\Xml::encode($itemData['title'] ?? $item->title()->value()) ?></title>
        <link><?= \Kirby\Toolkit\Xml::encode($itemData['link'] ?? $item->url()) ?></link>
        <description><![CDATA[<?= $itemData['description'] ?? $item->text()->kirbytext() ?>]]></description>
        <pubDate><?= $itemData['pubDate'] ?? date('r', $item->date()->exists() ? $item->date()->toTimestamp() : $item->modified()) ?></pubDate>
      <?php
      // Special handling for links section - use website URL as GUID if available
      if ($item->parent() && $item->parent()->slug() === 'links' && $item->website()->exists() && $item->website()->isNotEmpty()) {
        $guid = $item->website()->html();
      } else {
        $guid = $itemData['guid'] ?? $item->url();
      }
    ?>
  <guid><?= \Kirby\Toolkit\Xml::encode($guid) ?></guid>
  <?php if ($item->tags()->isNotEmpty()): ?>
    <?php foreach ($item->tags()->split() as $tag): ?>
  <category><?= \Kirby\Toolkit\Xml::encode($tag) ?></category>
      <?php endforeach ?><?php endif ?></item>
      <?php endforeach ?>
  </channel>
</rss>
