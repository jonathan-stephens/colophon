<?php
$layout = $layout ?? 'default';

foreach($articles as $article):
  if ($layout === 'links'): ?>
    <article class="h-entry">
      <box-l class="e-content flow">
        <h2 class="p-name u-bookmark-of hed"><a href="<?= $article->website()->html() ?>"><?= $article->title()->html() ?> (<?= $article->tld()->html() ?>)</a></h2>
        <?= $article->text()->kirbytext() ?>
        <div class="meta flow">
          <?php if($article->tags()->isNotEmpty()): ?>
            <?php snippet('/components/tags', ['reference' => $article]) ?>
          <?php endif ?>
          <a rel="bookmark" class="u-url" href="<?= $article->url() ?>"><time class="dt-published" datetime="<?= $article->date()->toDate('F j Y') ?><?= $article->time()->toDate('H:i') ?>"><?= $article->time()->toDate('H:i') ?></time></a>
        </div>
      </box-l>
    </article>
  <?php else: ?>
    <article class="h-entry">
      <a href="<?= $article->url() ?>" class="box-l e-content">
          <div class="meta">
            <time class="dt-published" datetime="<?= $article->date()->toDate('F j Y') ?> <?= $article->time()->toDate('H:i') ?>">
              <?= $article->date()->toDate('j M Y') ?>
            </time>
            <p class="text-stats">
              <?= readingTime($article->text()->words()) ?>
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
  <?php endif;
endforeach ?>
