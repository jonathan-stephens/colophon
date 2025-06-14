<article class="article post h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header>
    <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
    <p><?= $page->dek()->html() ?><p>
    <?php snippet('/components/on-at-in') ?>
  </header>
