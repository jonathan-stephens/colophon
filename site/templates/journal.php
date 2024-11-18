<?php snippet('header') ?>

  <article class="wrapper">
    <h1><?= $page->title()->html() ?></h1>
    <p class="byline">
      <span class="author">Jonathan Stephens</span>
      <?= $page->metadata()->date() ?>
      <?= $page->metadata()->location() ?>

    <?= $page->text()->kirbytext() ?>
  </article>




<?php snippet('footer') ?>
