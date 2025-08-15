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

// Default to getting case studies from a 'case-studies' page if it exists
$defaultSource = page('work') ? page('work')->children()->filterBy('template', 'project') : site()->pages()->listed();

// Get parameters passed to the snippet
$source = $source ?? $defaultSource;
$limit = $limit ?? null;
$filter = $filter ?? null;
$hedLevel = $hedLevel ?? 2; // Default to h2

// Validate heading level (should be between 1-6)
$hedLevel = max(1, min(6, (int)$hedLevel));

// Apply limit if specified
if ($limit) {
  $source = $source->limit($limit);
}

// Apply filter if specified (you can extend this logic later)
if ($filter) {
  // Example: filter by template
  if (isset($filter['template'])) {
    $source = $source->filterBy('template', $filter['template']);
  }
  // Example: filter by field value
  if (isset($filter['field']) && isset($filter['value'])) {
    $source = $source->filterBy($filter['field'], $filter['value']);
    }
}
?>

<div class="case-studies">
  <?php foreach($source as $article): ?>
    <a href="<?= $article->url() ?>" class="case-study">
      <article class="h-entry card">
        <img src="<?= $article->image()->url() ?>" style="--transition-image: url('<?= $article->image()->url() ?>)'">
          <h<?= $hedLevel ?> class="p-name hed"><?= $article->hed()->html() ?></h<?= $hedLevel ?>>
          <p class="dek"><?= $article->dek()->html() ?></p>
          <p class="info">
            <span class="client with-icon"><?= asset('assets/svg/icons/building.svg')->read() ?><?= $article->client()->html() ?></span>
            <span class="principal with-icon"><?= asset('assets/svg/icons/parent-child.svg')->read() ?><?= $article->principal()->html() ?></span>
            <span class="time with-icon">
              <?= asset('assets/svg/icons/time.svg')->read() ?>
                <time class="dt-start dtstart" datetime="<?= $article->dateFrom() ?>" itemprop="startDate">
                    <?= $article->dateFrom()->toDate('Y') ?> 
                </time>
                <?php if($article->dateTo()->isNotEmpty()): ?>
                    <?php if($article->dateFrom()->toDate('Y') !== $article->dateTo()->toDate('Y')): ?>
                        – <time class="dt-end dtend" datetime="<?= $article->dateTo() ?>" itemprop="endDate">
                            <?= $article->dateTo()->toDate('Y') ?>
                        </time>
                    <?php endif ?>
                <?php else : ?>
                   – now
                <?php endif ?>
            </span>
          </p>
      </article>
    </a>
  <?php endforeach ?>
</div>
