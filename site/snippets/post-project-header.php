<article class="article project-overview h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header>

    <?php if($page->hed()->isNotEmpty()): ?>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
    <?php else: ?>
      <h1 class="p-name" itemprop="name headline"><?= $page->title() ?></h1>
    <?php endif ?>

    <?php if($page->dek()->isNotEmpty()): ?>
      <p><?= $page->dek()->html() ?><p>
    <?php endif?>
  </header>
