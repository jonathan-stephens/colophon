<?php snippet('site-header') ?>

  <article class="post wrapper" itemscope itemtype="http://schema.org/Article">
    <header>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
      <p><?= $page->dek()->html() ?><p>
    </header>

    <?php foreach ($page->layout()->toLayouts() as $layout): ?>
    <section class="grid" id="<?= $layout->id() ?>">
      <?php foreach ($layout->columns() as $column): ?>
      <div class="column" style="--span:<?= $column->span() ?>">
        <div class="blocks">
          <?= $column->blocks() ?>
        </div>
      </div>
      <?php endforeach ?>
    </section>
    <?php endforeach ?>

  </article>

<?php snippet('site-footer') ?>
