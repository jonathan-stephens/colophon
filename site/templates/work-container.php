<?php snippet('site-header') ?>

<article class="article post h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header class="masthead work">
    <h1><?= $page->title()->html() ?></h1>

    <p class="dek"><?= $page->dek()->html() ?></p>
    <div class="lede">
      <?= $page->lede()->kt() ?>
    </div>
  </header>

  <h2>Selected Case Studies</h2>
  <?php snippet('/components/case-studies', ['hedLevel' => 3]) ?>


<?php snippet('site-footer') ?>
