<?php snippet('site-header') ?>
<div class="wrapper">
  <header>
      <h1><?= $page->hed()->isNotEmpty()
              ? $page->hed()->html()
              : $page->title()->html() ?></h1>
      <?php if($page->dek()->isNotEmpty()): ?>
        <p><?= $page->dek()->html() ?></p>
      <?php endif ?>
  </header>
  <div class="content">
    <?php
      // Check if user is logged in
      $user = kirby()->user();

      // Get the appropriate collection based on login status
      if ($user) {
          // User is logged in - show all posts (draft, listed, unlisted)
          $allChildren = $page->children()->listed()->add(
              $page->children()->unlisted()
          )->add(
              $page->children()->drafts()
          );
      } else {
          // User not logged in - show only listed posts
          $allChildren = $page->children()->listed();
      }

      // Get limit from URL parameter or default to 16
      $limit = (int) get('limit', 16);
      // Ensure limit is within reasonable bounds
      $allowedLimits = [16, 56, 121, 211, 326];
      $totalItems = $allChildren->count();

      // If limit is greater than total items, show all
      if ($limit >= $totalItems) {
        $limit = $totalItems;
      } elseif (!in_array($limit, $allowedLimits)) {
        $limit = 16; // fallback to default
      }

      // Get all articles with dynamic pagination
      $articles = $allChildren->flip()->paginate($limit);
      $pagination = $articles->pagination(); ?>
    <?php foreach($articles as $article): ?>
      <article class="h-entry">
        <a href="<?= $article->url() ?>" class="box-l e-content">
            <div class="meta">
              <time class="dt-published" datetime="<?= $article->date()->toDate('F j Y') ?> <?= $article->time()->toDate('H:i') ?>">
                <?= $article->date()->toDate('j M Y') ?>
              </time>
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
                <?php snippet('components/pub-status', ['page' => $article]) ?></p>
            </div>
            <h2 class="p-name hed">
              <?= $article->hed()->isNotEmpty() ? $article->hed()->html() : $article->title()->html() ?>
            </h2>
            <?php if($article->dek()->isNotEmpty()): ?>
              <p class="dek"><?= strip_tags($article->dek()) ?></p>
            <?php endif ?>
        </a>
    </article>
    <?php endforeach ?>
  </div>
  <footer>
    <?php snippet('components/pagination', ['pagination' => $pagination]) ?>
  </footer>
</div>

<?php snippet('site-footer') ?>
