<?php snippet('header') ?>

  <article class="article post wrapper" itemscope itemtype="http://schema.org/Article">
    <header>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
      <p><?= $page->dek()->html() ?><p>
      <?php snippet('/components/on-at-in') ?>
    </header>


    <div class="e-content prose" itemprop="articleBody">
      <?= $page->text()->kirbytext() ?>
    </div>

    <footer class="meta wrapper">
      <?php snippet('/components/tags') ?>
    </footer>

  </article>




<?php snippet('footer') ?>
