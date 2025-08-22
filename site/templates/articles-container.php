<?php snippet('site-header') ?>

<div class="wrapper">
  <header>
    <h1><?= $page->title()->html() ?></h1>
  </header>

<div class="content">
  <?php
    // Check if user is logged in
    $user = kirby()->user();

    // Get the appropriate collection based on login status
    if ($user) {
        // User is logged in - show all posts (draft, listed, unlisted)
        $articles = $page->children()->listed()->add(
            $page->children()->unlisted()
        )->add(
            $page->children()->drafts()
        )->flip();
    } else {
        // User not logged in - show only listed posts
        $articles = $page->children()->listed()->flip();
    }
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
            <h2 class="p-name hed"><?= $article->hed()->html() ?></h2>
            <p class="dek"><?= $article->dek()->html() ?></p>
        </box-l>
      </a>
    </article>
  <?php endforeach ?>
</div>

<?php snippet('site-footer') ?>
