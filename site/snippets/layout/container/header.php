<header>
    <h1><?= $page->hed()->isNotEmpty()
            ? $page->hed()->html()
            : $page->title()->html() ?></h1>
    <?php if($page->dek()->isNotEmpty()): ?>
      <p><?= $page->dek()->html() ?></p>
    <?php endif ?>
</header>
