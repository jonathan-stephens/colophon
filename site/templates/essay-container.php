<?php snippet('header') ?>

<div class="masthead wrapper">
    <!--<h1><?= $page->title()->html() ?></h1>-->
</div>

<div class="wrapper">
<?php foreach($page->children()->listed()->flip() as $article): ?>

  <article class="cluster">
    <a href="<?= $article->url() ?>">
      <box-l>
          <h2 class="hed"><?= $article->hed()->html() ?></h2>
          <p class="dek"><?= $article->dek()->html() ?></p>
          <time datetime="<?= $article->metadata()->date()->toDate('F j Y') ?> <?= $article->metadata()->time()->toDate('H:i') ?>"><?= $article->metadata()->date()->toDate('j M Y') ?></time>
      </box-l>
    </a>
  </article>

  <?php endforeach ?>
</div>




<?php snippet('footer') ?>
