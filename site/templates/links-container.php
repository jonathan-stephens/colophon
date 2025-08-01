<?php snippet('site-header') ?>
<div class="masthead wrapper">
    <!--<h1><?= $page->title()->html() ?></h1>-->
</div>
<div class="wrapper">
<?php
  // Get all articles and group them by date
  $articles = $page->children()->listed()->flip();
  $groupedArticles = [];

  foreach($articles as $article) {
    $date = $article->date()->toDate('F j, Y');
    if(!isset($groupedArticles[$date])) {
      $groupedArticles[$date] = [];
    }
    $groupedArticles[$date][] = $article;
  }

  // Now loop through the grouped articles
  foreach($groupedArticles as $date => $dayArticles):
?>
  <section class="day-group flow">
    <h2 class="date-header"><?= $date ?></h2>

    <?php foreach($dayArticles as $article): ?>
      <article class="h-entry">
        <box-l class="e-content flow">
          <h3 class="p-name u-bookmark-of hed"><a href="<?= $article->website()->html() ?>"><?= $article->title()->html() ?> (<?= $article->tld()->html() ?>)</a></h3>
          <?= $article->text()->kirbytext() ?>
          <div class="meta flow">
            <?php if($article->tags()->isNotEmpty()): ?>
              <?php snippet('/components/tags', ['reference' => $article]) ?>
            <?php endif ?>
            <a rel="bookmark" class="u-url" href="<?= $article->url() ?>"><time class="dt-published" datetime="<?= $article->date()->toDate('F j Y') ?><?= $article->time()->toDate('H:i') ?>"><?= $article->time()->toDate('H:i') ?></time></a>
          </div>
        </box-l>
      </article>
    <?php endforeach ?>

  </section>
<?php endforeach ?>
</div>
<?php snippet('site-footer') ?>
