<?php snippet('header') ?>

  <article class="article post wrapper h-entry" itemscope itemtype="http://schema.org/Article">
    <header>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
      <p><?= $page->dek()->html() ?><p>
      <?php snippet('/components/on-at-in') ?>
    </header>


    <div class="e-content prose" itemprop="articleBody">
      <?= $page->text()->footnotes() ?>
    </div>



    <footer class="meta">
      <?php snippet('/components/tags', ['reference' => $page]) ?>
    </footer>

  </article>

<?php snippet('footer') ?>
