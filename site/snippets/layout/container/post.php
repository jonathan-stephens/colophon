<?php
$layout = $layout ?? 'default';
foreach($articles as $article):
  $wordCount = $article->text()->words();
  $lengthCategory = \yourusername\TagGarden\Helpers::getLengthCategory($wordCount);

  if ($layout === 'links'): ?>
    <article class="h-entry box-l flow">
      <a href="<?= $article->website()->html() ?>" class="p-name u-bookmark-of">
        <h2 class="hed">
          <?= $article->title()->html() ?>
          <span class="tld">
            (<?= $article->tld()->html() ?>)
          </span>
        </h2>
      </a>
      <a rel="bookmark" class="u-url" href="<?= $article->url() ?>">
        <span class="length-category"><?= $lengthCategory ?> •
          <?php if($article->tags()->isNotEmpty()): ?>
            <?php snippet('/components/tags', ['reference' => $article]) ?>
          <?php endif ?>•
          <?= $wordCount ?> words
        </span>
      </a>
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
  <?php endif; ?>
<?php endforeach ?>
