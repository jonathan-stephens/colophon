<?php
/**
 * Stream template — Phase 3
 *
 * Server-side filtering with faceted counts.
 * Degrades gracefully without JS (submit button visible).
 * Progressively enhanced with JS (auto-navigate on change, deselect on re-click).
 *
 * URL params:
 *   type    — template name (e.g. essay, book, note)
 *   stage   — growthStatus value (e.g. evergreen, seedling)
 *   listed  — page status: listed | unlisted | draft  (logged-in users only)
 *   sort    — tended_desc | tended_asc | date_desc | date_asc
 */

$isLoggedIn = $kirby->user() !== null;

// ------------------------------------------------------------------
// Sanitize incoming params
// ------------------------------------------------------------------
$validSorts = ['tended_desc', 'tended_asc', 'date_desc', 'date_asc'];
$activeSort = in_array(get('sort'), $validSorts, true) ? get('sort') : 'tended_desc';

$activeFilters = [
    'type'   => trim((string) get('type',   '')),
    'stage'  => trim((string) get('stage',  '')),
    // Status filter is silently ignored for logged-out users
    'listed' => $isLoggedIn ? trim((string) get('listed', '')) : '',
];

// ------------------------------------------------------------------
// Data
// ------------------------------------------------------------------

// Full unfiltered pool — used for facet computation
$pool = $kirby->collection('stream');

// Facets — cross-filtered counts per dimension
$facets = streamFacets($pool, $activeFilters);

// Final display set: filtered + sorted per user selections
$stream = streamSort(streamApplyFilters($pool, $activeFilters), $activeSort);

$totalCount    = $pool->count();
$filteredCount = $stream->count();
$isFiltered    = !empty(array_filter($activeFilters)) || $activeSort !== 'tended_desc';

// ------------------------------------------------------------------
// Labels
// ------------------------------------------------------------------
$sortLabels = [
    'tended_desc' => 'Tended ↓',
    'tended_asc'  => 'Tended ↑',
    'date_desc'   => 'Published ↓',
    'date_asc'    => 'Published ↑',
];
?>
<?php snippet('site-header') ?>

<header class="wrapper">

  <h1 class="p-name" itemprop="name headline">
    <?= $page->hed()->isNotEmpty() ? $page->hed()->html() : $page->title()->html() ?>
  </h1>

  <?php if ($page->dek()->isNotEmpty()): ?>
    <p class="dek"><?= $page->dek()->html() ?></p>
  <?php endif ?>

</header>

<div class="wrapper">

  <form method="get"
        action="<?= $page->url() ?>"
        id="streamFilters"
        class="stream-filters">

    <?php
    /*
     * Helper: render one filter fieldset.
     *
     * $name      — param name (type | stage | listed)
     * $legend    — visible group label
     * $options   — from $facets[$name]: ['value' => ['label' => ..., 'count' => ...]]
     * $active    — currently active value for this dimension
     */
    $renderFilterGroup = function (
        string $name,
        string $legend,
        array  $options,
        string $active
    ) use ($page): void {
        if (empty($options)) return;
        $allId = $name . '-all';
        ?>
        <fieldset class="filter-group filter-group--<?= esc($name) ?>"
                  role="radiogroup"
                  aria-label="Filter by <?= esc($legend) ?>">
          <legend><?= esc($legend) ?></legend>

          <input type="radio"
                 name="<?= esc($name) ?>"
                 id="<?= esc($allId) ?>"
                 value=""
                 class="filter-radio"
                 <?= empty($active) ? 'checked' : '' ?>>
          <label for="<?= esc($allId) ?>" class="button">All</label>

          <?php foreach ($options as $val => $data):
            $id       = $name . '-' . Str::slug($val);
            $disabled = $data['count'] === 0;
          ?>
            <input type="radio"
                   name="<?= esc($name) ?>"
                   id="<?= esc($id) ?>"
                   value="<?= esc($val) ?>"
                   class="filter-radio"
                   <?= $active === $val ? 'checked' : '' ?>
                   <?= $disabled ? 'disabled aria-disabled="true"' : '' ?>>
            <label for="<?= esc($id) ?>"
                   class="button<?= $disabled ? ' is-disabled' : '' ?>">
              <span class="count" aria-hidden="true"><?= $data['count'] ?></span>
              <span class="sr-only">(<?= $data['count'] ?> entries)</span>
              <?= esc($data['label']) ?>
            </label>
          <?php endforeach ?>

        </fieldset>
        <?php
    };
    ?>

    <!-- Sort — no "All" option, no deselect, always one value active -->
    <fieldset class="filter-group filter-group--sort"
              role="radiogroup"
              aria-label="Sort order">
      <legend>Sort</legend>
      <?php foreach ($sortLabels as $val => $label): ?>
        <input type="radio"
               name="sort"
               id="sort-<?= esc($val) ?>"
               value="<?= esc($val) ?>"
               class="filter-radio"
               <?= $activeSort === $val ? 'checked' : '' ?>>
        <label for="sort-<?= esc($val) ?>" class="button"><?= esc($label) ?></label>
      <?php endforeach ?>
    </fieldset>

    <!-- Type -->
    <?php $renderFilterGroup('type', 'Type', $facets['type'] ?? [], $activeFilters['type']) ?>

    <!-- Stage -->
    <?php $renderFilterGroup('stage', 'Stage', $facets['stage'] ?? [], $activeFilters['stage']) ?>

    <!-- Status — only rendered for logged-in users -->
    <?php if ($isLoggedIn): ?>
      <?php $renderFilterGroup('listed', 'Status', $facets['listed'] ?? [], $activeFilters['listed']) ?>
    <?php endif ?>

    <!-- Shown without JS; hidden by JS once enhancement kicks in -->
    <button type="submit" class="button filter-submit" id="filterSubmit">
      Apply filters
    </button>

    <?php if ($isFiltered): ?>
      <a href="<?= $page->url() ?>" class="filter-reset">Clear all</a>
    <?php endif ?>

  </form>

  <p class="stream-count" id="streamCount" aria-live="polite">
    Showing
    <span class="count-number"><?= $filteredCount ?></span>
    <?php if ($isFiltered): ?>of <?= $totalCount ?><?php endif ?>
    <?= $filteredCount === 1 ? 'entry' : 'entries' ?>
  </p>

</div>

<div class="stream wrapper" id="streamContainer">

  <?php if ($filteredCount === 0): ?>
    <p class="stream-empty">No entries match the current filters.</p>
  <?php endif ?>

  <?php foreach ($stream as $entry): ?>
    <?php
    $blueprintTitle = $entry->blueprint()->title();
    $templateName   = $entry->intendedTemplate()->name();
    $status         = $entry->status();

    $cats = $entry->category()->isNotEmpty()
      ? array_map('trim', $entry->category()->split(','))
      : [];

    $displayTitle = $entry->hed()->isNotEmpty()
      ? $entry->hed()->html()
      : $entry->title()->html();
    ?>
    <article
      class="stream-entry"
      data-template="<?= esc($templateName ?? '') ?>"
      data-status="<?= esc($status ?? '') ?>"
      data-categories="<?= esc((string) $entry->category()) ?>"
      data-growth-status="<?= esc((string) $entry->growthStatus()) ?>"
      itemscope
      itemtype="https://schema.org/CreativeWork">

      <header class="entry-meta">

        <span class="entry-type"><?= esc($blueprintTitle) ?></span>

        <?php if (!empty($cats)): ?>
          <span class="entry-categories">
            <?php foreach ($cats as $i => $cat): ?>
              <span class="p-category" itemprop="genre"><?= esc($cat) ?></span><?php
              if ($i < count($cats) - 1): ?>,<?php endif ?>
            <?php endforeach ?>
          </span>
        <?php endif ?>

        <?php if ($entry->growthStatus()->isNotEmpty()): ?>
          <span class="growth-status"
                data-growth-status="<?= esc((string) $entry->growthStatus()) ?>">
            <?= esc((string) $entry->growthStatus()) ?>
          </span>
        <?php endif ?>

        <?php if ($isLoggedIn && $status !== 'listed'): ?>
          <span class="page-status page-status--<?= esc($status) ?>"
                aria-label="Status: <?= esc($status) ?>">
            <?= esc($status) ?>
          </span>
        <?php endif ?>

      </header>

      <h2 class="entry-title" itemprop="name">
        <a href="<?= $entry->url() ?>" itemprop="url"><?= $displayTitle ?></a>
      </h2>

      <?php if ($entry->dek()->isNotEmpty()): ?>
        <p class="entry-dek" itemprop="alternativeHeadline"><?= $entry->dek()->html() ?></p>
      <?php endif ?>

      <footer class="entry-dates">

        <?php if ($entry->date()->isNotEmpty()): ?>
          <time datetime="<?= $entry->date()->toDate('Y-m-d') ?>"
                class="date-published"
                itemprop="datePublished">
            <?= $entry->date()->toDate('M j, Y') ?>
          </time>
        <?php endif ?>

        <?php if ($entry->tended()->isNotEmpty()): ?>
          <time datetime="<?= $entry->tended()->toDate('Y-m-d') ?>"
                class="date-tended"
                itemprop="dateModified">
            Tended <?= $entry->tended()->toDate('M j, Y') ?>
          </time>
        <?php endif ?>

      </footer>

    </article>
  <?php endforeach ?>

</div>

<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('streamFilters');
    if (!form) return;

    // Progressive enhancement: hide the no-JS submit button
    var submitBtn = document.getElementById('filterSubmit');
    if (submitBtn) submitBtn.hidden = true;

    var radios      = form.querySelectorAll('input[type="radio"]');
    var lastChecked = {};

    // Snapshot the initial checked state
    radios.forEach(function (radio) {
      if (radio.checked) lastChecked[radio.name] = radio;
    });

    radios.forEach(function (radio) {
      radio.addEventListener('click', function () {
        var name   = this.name;
        var isSort = (name === 'sort');

        // Deselect: clicking the already-active filter radio resets to "All"
        // Sort group is excluded — always requires one active value
        if (!isSort && lastChecked[name] === this && this.value !== '') {
          this.checked = false;
          var allRadio = form.querySelector('input[name="' + name + '"][value=""]');
          if (allRadio) {
            allRadio.checked  = true;
            lastChecked[name] = allRadio;
          }
        } else {
          lastChecked[name] = this;
        }

        // Build clean URL: start fresh, set only non-empty non-default values
        var url = new URL(window.location.href);

        ['type', 'stage', 'listed', 'sort'].forEach(function (p) {
          url.searchParams.delete(p);
        });

        form.querySelectorAll('input[type="radio"]:checked').forEach(function (r) {
          // Omit "All" values (empty string) and the default sort from the URL
          if (r.value === '') return;
          if (r.name === 'sort' && r.value === 'tended_desc') return;
          url.searchParams.set(r.name, r.value);
        });

        window.location.href = url.toString();
      });
    });
  });
}());
</script>

<?php snippet('site-footer') ?>
