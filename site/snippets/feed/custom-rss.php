<?php
// Set the content type header
header('Content-type: application/xml; charset=utf-8');

// Output XML declaration only
echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;

// No XSL stylesheet reference
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title><?= xml($title) ?></title>
    <link><?= xml($url) ?></link>
    <description><?= xml($description) ?></description>
    <language><?= xml($language) ?></language>
    <lastBuildDate><?= date('r', $modified) ?></lastBuildDate>
    <managingEditor><?= xml($managingEditor) ?></managingEditor>
    <webMaster><?= xml($webMaster) ?></webMaster>
    <atom:link href="<?= xml($feedurl) ?>" rel="self" type="application/rss+xml" />

    <?php foreach ($items as $item): ?>
    <item>
      <title><?= xml($item->{$titlefield}()) ?></title>
      <link><?= xml($item->{$urlfield}()) ?></link>
      <?php
      // Use custom guid if available
      $guid = $item->{$urlfield}();
      if (isset($customData['customGuids'][$item->id()])) {
          $guid = $customData['customGuids'][$item->id()];
      }
      ?>
      <guid><?= xml($guid) ?></guid>

      <?php
      // Generate publication date
      $pubDate = $item->modified();
      if ($item->{$datefield}()->exists()) {
          $pubDate = $item->{$datefield}()->toDate('U') ?: $item->modified();
      }
      ?>
      <pubDate><?= date('r', $pubDate) ?></pubDate>

      <?php if(isset($customData['feedCategories'][$item->id()])): ?>
        <?php foreach($customData['feedCategories'][$item->id()] as $tag): ?>
      <category><?= xml($tag) ?></category>
        <?php endforeach ?>
      <?php endif ?>
      <description><![CDATA[<?= $item->{$textfield}()->kirbytext() ?>]]></description>
    </item>
    <?php endforeach ?>
  </channel>
</rss>
