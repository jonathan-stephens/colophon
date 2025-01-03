<?php snippet('header') ?>

  <article class="article post wrapper" itemscope itemtype="http://schema.org/Article">
    <header>
      <h1 class="p-name" itemprop="name headline"><?= $page->title()->html() ?></h1>
      <?php snippet('/components/on-at-in') ?>
    </header>

    <div class="e-content prose" itemprop="articleBody">
      <?= $page->text()->kirbytext() ?>

      <p class="written-url">URL: <a class="u-bookmark-of" href="<?= $page->website()->html() ?>"><?= $page->website()->html() ?></a></p>
    </div>

    <footer class="meta">
      <?php snippet('/components/tags', ['reference' => $page]) ?>
    </footer>

  </article>
<?php snippet('footer') ?>
