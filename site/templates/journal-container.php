<?php snippet('header') ?>

<div class="masthead wrapper">
    <!--<h1><?= $page->title()->html() ?></h1>-->
</div>

<div class="wrapper">
<?php foreach($page->children()->listed()->flip() as $article): ?>

  <article class="cluster">
    <box-l>
      <a href="<?= $article->url() ?>">
        <h2><?= $article->hed()->html() ?></h2>
        <p class="subtitle"><?= $article->dek()->html() ?></p>
        <time datetime="<?= $article->metadata()->date()->toDate('F j Y') ?> <?= $article->metadata()->time()->toDate('H:i') ?>"><?= $article->metadata()->date()->toDate('M j Y') ?></time>
      </a>
    </box-l>
  </article>

  <?php endforeach ?>
</div>




<?php snippet('footer') ?>
