<?php snippet('site-header') ?>
<div class="wrapper">
<header>
    <h1><?= $page->title()->html() ?></h1>
</header>
<div>
  <?php
    // Get limit from URL parameter or default to 16
    $limit = (int) get('limit', 16);

    // Ensure limit is within reasonable bounds
    $allowedLimits = [16, 56, 121, 211, 326];
    $totalItems = $page->children()->listed()->count();

    // If limit is greater than total items, show all
    if ($limit >= $totalItems) {
      $limit = $totalItems;
    } elseif (!in_array($limit, $allowedLimits)) {
      $limit = 16; // fallback to default
    }

    // Get all articles with dynamic pagination
    $articles = $page->children()->listed()->flip()->paginate($limit);
    $pagination = $articles->pagination();
  ?>

  <?php foreach($articles as $article): ?>
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
</div>
<footer>
  <?php snippet('components/pagination', ['pagination' => $pagination]) ?>
</footer>
</div>

<?php snippet('site-footer') ?>
