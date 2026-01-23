<?php snippet('site-header') ?>
  <?php if($page->isHomePage()): ?>
    <?php snippet('layout/home') ?>
  <?php else: ?>
    <?php snippet('layout/default') ?>
  <?php endif ?>
<?php snippet('site-footer') ?>
