<article class="article post h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header>
    <h1 class="p-name" itemprop="name headline">
      <?= $page->hed()->isNotEmpty()
          ? $page->hed()->html()
          : $page->title()->html() ?>
    </h1>

    <?php if($page->dek()->isNotEmpty()): ?>
      <?= $page->dek()->kt() ?>
    <?php endif?>

    <?php snippet('/components/on-at-in') ?>
  </header>
