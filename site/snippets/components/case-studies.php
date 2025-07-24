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
$defaultSource = site()->find('case-studies') ? site()->find('case-studies')->children()->listed() : site()->pages()->listed();

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
    <a href="<?= $article->url() ?>" class="case-study card">
      <article class="h-entry">
        <header>
          <p>
            <span class="client"><?= $article->client()->html() ?></span> •
            <span class="context"><?= $article->context()->html() ?></span>
          </p>
          <h<?= $hedLevel ?> class="p-name hed"><?= $article->hed()->html() ?></h<?= $hedLevel ?>>
          <p class="dek"><?= $article->dek()->html() ?></p>
        </header>
        <ul class="meta">
          <li class="role">
            <span>Role</span>
            <span><?= $article->role()->html() ?></span>
          </li>
          <li class="industries">
            <span>Industries</span>
            <span><?php foreach ($article->industry()->split() as $industry): ?>
              <span><?= $industry ?></span>
            <?php endforeach ?></span>
          </li>
          <li class="organization">
            <span>Organization</span>
            <span>
              <span class="business-model"><?= $article->businessModel()->html() ?></span>
              <span class="working-model"><?= $article->workingModel()->html() ?></span>
              <span class="company-size"><?= $article->companySize()->html() ?></span>
            </span>
          </li>
        </ul>
      </article>
    </a>
  <?php endforeach ?>
</div>
