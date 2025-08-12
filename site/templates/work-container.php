<?php snippet('site-header') ?>

<article class="article post h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header class="masthead work">
    <h1><?= $page->title()->html() ?></h1>

    <p class="dek"><span><?= $page->dek()->html() ?></span></p>

    <div class="lede">
      <?= $page->lede()->kt() ?>
    </div>
    <ul class="metrics">
      <li>
        <span>18+</span>
        Years of experience
      </li>
      <li>
        <span>10+</span>
        Years in leadership
      </li>
      <li>
        <span>~100</span>
        Largest org managed
      </li>
      <li>
        <span>~150</span>
        Performance reviews
      </li>
      <li>
        <span>40+</span>
        Promotions awarded
      </li>
      <li>
        <span>~40</span>
        Teams managed
      </li>
    </ul>

  </header>


  <h2>Selected Work</h2>
  <?php snippet('/components/case-studies', ['hedLevel' => 3]) ?>


<?php snippet('site-footer') ?>
