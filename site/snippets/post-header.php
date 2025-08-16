<article class="article post h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header>

    <?php if($page->hed()->isNotEmpty()): ?>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
    <?php else: ?>
      <h1 class="p-name" itemprop="name headline"><?= $page->title() ?></h1>
    <?php endif ?>

    <?php if($page->dek()->isNotEmpty()): ?>
      <?= $page->dek()->kt() ?>
    <?php endif?>

    <?php snippet('/components/on-at-in') ?>
  </header>
