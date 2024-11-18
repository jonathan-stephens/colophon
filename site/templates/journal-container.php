<?php snippet('header') ?>

<div class="masthead wrapper">
  <h1><?= $page->title()->html() ?></h1>
</div>

<div class="wrapper">
<?php foreach($page->children()->listed()->flip() as $article): ?>

  <article>
    <h2><?= $article->hed()->html() ?></h2>
    <p><?= $article->dek()->html() ?></p>
    <a href="<?= $article->url() ?>" class="button" data-button-type="secondary">Read post</a>
  </article>

  <?php endforeach ?>





<?php snippet('footer') ?>
