<?php snippet('header') ?>
<div class="masthead wrapper">
    <!--<h1><?= $page->title()->html() ?></h1>-->
</div>
<div class="wrapper">
<?php
  // Get all articles and group them by date
  $articles = $page->children()->listed()->flip();
  $groupedArticles = [];

  foreach($articles as $article) {
    $date = $article->metadata()->date()->toDate('F j, Y');
    if(!isset($groupedArticles[$date])) {
      $groupedArticles[$date] = [];
    }
    $groupedArticles[$date][] = $article;
  }

  // Now loop through the grouped articles
  foreach($groupedArticles as $date => $dayArticles):
?>
  <section class="day-group">
    <h2 class="date-header"><?= $date ?></h2>

    <?php foreach($dayArticles as $article): ?>
      <article class="h-entry cluster">
        <box-l class="e-content">
          <h3 class="p-name u-bookmark-of hed"><a href="<?= $article->website()->html() ?>"><?= $article->title()->html() ?></a></h3>
          <p><?= $article->text()->kirbytext() ?></p>
          <div class="meta flow">
            <a rel="bookmark" class="u-url" href="<?= $article->url() ?>"><time class="dt-published" datetime="<?= $article->metadata()->date()->toDate('F j Y') ?><?= $article->metadata()->time()->toDate('H:i') ?>"><?= $article->metadata()->time()->toDate('H:i') ?></time></a>
            <?php snippet('/components/tags', ['reference' => $article]) ?>
          </div>
        </box-l>
      </article>
    <?php endforeach ?>

  </section>
<?php endforeach ?>
</div>
<?php snippet('footer') ?>
