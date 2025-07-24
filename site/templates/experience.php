<?php snippet('site-header') ?>

  <article class="h-resume post wrapper flow" itemscope itemtype="http://schema.org/Article">
    <header>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
      <p><?= $page->dek()->html() ?><p>
    </header>

    <section class="work-history">

      <?php foreach ($page->blocks()->toBlocks() as $block): ?>


    <div id="<?= $block->id() ?>" class="block block-type-<?= $block->type() ?>">
      <?= $block ?>
    </div>

    <?php endforeach ?>
  </section>


  </article>

<?php snippet('site-footer') ?>
