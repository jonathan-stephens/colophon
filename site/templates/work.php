<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('components/breadcrumb') ?>


    <div class="splash">
      <?= $page->text()->kirbytext() ?>
    </div>

    <?php foreach($page->children()->listed()->flip() as $project): ?>
      <article>
        <h2 class="hed"><?= $project->hed()->html() ?></h2>
        <p class="dek"><?= $project->dek()->excerpt(300) ?></p>
        <a href="<?= $project->url() ?>">Read more…</a>
      </article>
    <?php endforeach ?>

  </section>
<?php snippet('footer') ?>
