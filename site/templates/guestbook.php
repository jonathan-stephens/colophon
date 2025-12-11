<?php snippet('site-header') ?>
<div class="wrapper">
  <header>
    <h1 class="p-name" itemprop="name headline">
      <?= $page->hed()->isNotEmpty()
          ? $page->hed()->html()
          : $page->title()->html() ?>
    </h1>

    <?php if($page->dek()->isNotEmpty()): ?>
      <?= $page->dek()->kt() ?>
    <?php endif?>
    <?php snippet('/components/on-at-in') ?>
  </header>

  <?php snippet('komments/kommentform'); ?>
  <?php snippet('komments/list/comments'); ?>
  <?php snippet('komments/list/likes'); ?>
  <?php snippet('komments/list/reposts'); ?>
  <?php snippet('komments/list/replies'); ?>
</div>
<?php snippet('site-footer') ?>
