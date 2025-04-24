<?php snippet('header') ?>
<?php
    // Get the link number
    $number = $page->num();
    // Format it with leading zeros if desired
    $formattedNumber = sprintf("%03d", $number);
?>

  <article class="article post wrapper h-entry" itemscope itemtype="http://schema.org/Article">
    <header>
      <h1 class="p-name" itemprop="name headline"><?= $page->title()->html() ?> (<?= $page->tld()->html() ?>)</h1>
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
