<?php snippet('header') ?>

  <article class="article wrapper">
      <h1 class="p-name" itemprop="headline"><?= $page->title()->html() ?></h1>
      <?php snippet('/components/byline') ?>

    <div class="e-content prose" itemprop="articleBody">
      <?= $page->text()->kirbytext() ?>
    </div>

    <footer>
      <p class="meta">
        <span class="p-location h-geo">
          <data class="p-latitude" value=""></data>
          <data class="p-longitude" value=""></data>
        </span>
        <time datetime="<?= $page->metadata()->date()->toDate('F j Y') ?> <?= $page->metadata()->time()->toDate('H:i') ?>" itemprop="dateCreated pubdate datePublished"><?= $page->metadata()->time()->toDate('H:i') ?></time>
      </p>
      <p class="meta">
        Tags
      </p>
    </footer>

  </article>




<?php snippet('footer') ?>
