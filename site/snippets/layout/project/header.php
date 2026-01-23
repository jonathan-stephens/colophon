<article class="article project-overview h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header>

    <h1 class="p-name" itemprop="name headline">
      <?= $page->hed()->isNotEmpty()
          ? $page->hed()->html()
          : $page->title()->html() ?>
    </h1>

    <?php if($page->dek()->isNotEmpty()): ?>
      <p><?= $page->dek()->html() ?><p>
    <?php endif?>
  </header>
