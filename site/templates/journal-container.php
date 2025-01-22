<?php snippet('header') ?>

<div class="masthead wrapper">
    <!--<h1><?= $page->title()->html() ?></h1>-->
</div>

<div class="wrapper">
<?php foreach($page->children()->listed()->flip() as $article): ?>

  <article class="h-entry">
    <a href="<?= $article->url() ?>">
      <box-l class="e-content">
          <div class="meta">
            <time class="dt-published" datetime="<?= $article->metadata()->date()->toDate('F j Y') ?> <?= $article->metadata()->time()->toDate('H:i') ?>"><?= $article->metadata()->date()->toDate('j M Y') ?></time>
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
          </div>
          <h2 class="p-name hed"><?= $article->hed()->html() ?></h2>
          <p class="dek"><?= $article->dek()->html() ?></p>
      </box-l>
    </a>
  </article>

  <?php endforeach ?>
</div>




<?php snippet('footer') ?>
