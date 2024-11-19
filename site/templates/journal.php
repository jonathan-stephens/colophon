<?php snippet('header') ?>

  <article class="wrapper">
    <header>
      <h1><?= $page->title()->html() ?></h1>
      <?php snippet('/components/byline') ?>
    </header>

    <?= $page->text()->kirbytext() ?>
  </article>




<?php snippet('footer') ?>
