<?php
/**
 * Case Study Cards Snippet
 *
 * Usage examples:
 * <?php snippet('case-study-cards') ?>
 * <?php snippet('case-study-cards', ['source' => site()->find('projects')]) ?>
 * <?php snippet('case-study-cards', ['source' => $page->children(), 'limit' => 6]) ?>
 * <?php snippet('case-study-cards', ['hedLevel' => 3]) ?>
 * <?php snippet('case-study-cards', ['limit' => 4, 'hedLevel' => 4]) ?>
 */
$defaultSource = page('work') ? page('work')->children()->filterBy('template', 'project') : site()->pages()->listed();
$source   = $source   ?? $defaultSource;
$limit    = $limit    ?? null;
$filter   = $filter   ?? null;
$hedLevel = $hedLevel ?? 2;
$hedLevel = max(1, min(6, (int)$hedLevel));

if ($limit) {
  $source = $source->limit($limit);
}
if ($filter) {
  if (isset($filter['template'])) {
    $source = $source->filterBy('template', $filter['template']);
  }
  if (isset($filter['field']) && isset($filter['value'])) {
    $source = $source->filterBy($filter['field'], $filter['value']);
  }
}
?>
<div class="case-studies">
  <?php foreach($source as $article): ?>
    <article class="h-entry card case-study">
      <?php if($coverImage = $article->cover()->toFile()): ?>
        <img src="<?= $coverImage->url() ?>" alt="">
      <?php endif ?>
      <h<?= $hedLevel ?> class="p-name hed">
        <a href="<?= $article->url() ?>"><?= $article->hed()->html() ?></a>
      </h<?= $hedLevel ?>>
      <p class="dek"><?= $article->dek()->html() ?></p>
      <p class="info">
        <?php if($article->client()->isNotEmpty()): ?>
          <span class="client with-icon"><?= asset('assets/svg/icons/building.svg')->read() ?><?= $article->client()->html() ?></span>
        <?php endif ?>
        <?php if($article->principal()->isNotEmpty()): ?>
          <span class="principal with-icon"><?= asset('assets/svg/icons/parent-child.svg')->read() ?><?= $article->principal()->html() ?></span>
        <?php endif ?>
        <?php if($article->dateFrom()->isNotEmpty()): ?>
          <span class="time with-icon">
            <?= asset('assets/svg/icons/time.svg')->read() ?>
            <time class="dt-start dtstart" datetime="<?= $article->dateFrom() ?>" itemprop="startDate">
              <?= $article->dateFrom()->toDate('Y') ?>
            </time>
            <?php if($article->dateTo()->isNotEmpty()): ?>
              <?php if($article->dateFrom()->toDate('Y') !== $article->dateTo()->toDate('Y')): ?>
                – <time class="dt-end dtend" datetime="<?= $article->dateTo() ?>" itemprop="endDate">
                    <?= $article->dateTo()->toDate('Y') ?>
                  </time>
              <?php endif ?>
            <?php else : ?>
              – now
            <?php endif ?>
          </span>
        <?php endif ?>
      </p>
    </article>
  <?php endforeach ?>
</div>