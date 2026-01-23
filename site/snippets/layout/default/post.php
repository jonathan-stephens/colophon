<div class="e-content prose" itemprop="articleBody">
  <?php if(!$page->website()->isEmpty()): ?>
    <p class="written-url">URL: <a class="u-bookmark-of u-in-reply-to"  href="<?= $page->website()->html() ?>"><?= $page->website()->html() ?></a></p>
  <?php endif?>
  <?= $page->text()->footnotes() ?>
</div>
