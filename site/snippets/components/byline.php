<h2 class="byline">
  <span class="author">Jonathan Stephens</span>
  <time class="dt-published" datetime="<?= $page->metadata()->date()->toDate('F j Y') ?> <?= $page->metadata()->time()->toDate('H:i') ?>" itemprop="dateCreated pubdate datePublished"><?= $page->metadata()->date()->toDate('M j Y') ?></time>
  <?= $page->metadata()->location() ?>
</h2>
