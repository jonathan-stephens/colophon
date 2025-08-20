<?php snippet('site-header') ?>

<div class="wrapper">
  <header>
      <h1><?= $page->title()->html() ?></h1>
  </header>
  <div class="content">
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
      <a href="<?= $article->url() ?>">
        <box-l class="e-content">
          <div class="meta">
              <time class="dt-published" datetime="<?= $article->date()->toDate('F j Y') ?> <?= $article->time()->toDate('H:i') ?>"><?= $article->date()->toDate('j M Y') ?></time>
              <p class="text-stats">
                <?php
                  $wordCount = $article->text()->words();
                  $minSpeed = 167; // words per minute
                  $maxSpeed = 285; // words per minute

                  $minSeconds = ceil($wordCount / ($minSpeed / 60));
                  $maxSeconds = ceil($wordCount / ($maxSpeed / 60));

                  if ($minSeconds < 60) {
                    if ($minSeconds === $maxSeconds) {
                      echo $minSeconds . ' sec read';
                    } else {
                      echo $maxSeconds . '&thinsp;–&thinsp;' . $minSeconds . '  sec read';
                    }
                  } else {
                    $minMinutes = ceil($minSeconds / 60);
                    $maxMinutes = ceil($maxSeconds / 60);
                    if ($minMinutes === $maxMinutes) {
                      echo $minMinutes . ' min read';
                    } else {
                      echo $maxMinutes . '&thinsp;–&thinsp;' . $minMinutes . ' min read';
                    }
                  }
                ?>
                <?php snippet('components/pub-status', ['page' => $article]) ?>
              </p>
            </div>
            <h2 class="p-name hed">
              <?= $article->hed()->html() ?>
            </h2>
            <p class="dek"><?= $article->dek()->html() ?></p>
        </box-l>
      </a>
    </article>
    <?php endforeach ?>
  </div>
  <footer>
    <?php snippet('components/pagination', ['pagination' => $pagination]) ?>
  </footer>
</div>

<?php snippet('site-footer') ?>
