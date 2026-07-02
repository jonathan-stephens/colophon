<?php
/**
 * Template: Timeline
 * Location: site/templates/timeline.php
 *
 * Displays timeline entries either from:
 * 1. This page's children (e.g., ~/work/project/timeline)
 * 2. All timeline entries site-wide (e.g., ~/timeline as main aggregator)
 */
?>
<?php snippet('site-header') ?>

<article class="timeline-page">
  <header>
    <h1><?= $page->title() ?></h1>
    <?php if ($page->intro()->isNotEmpty()): ?>
      <div class="intro">
        <?= $page->intro()->kt() ?>
      </div>
    <?php endif ?>
  </header>

  <?php
  // Determine if this is the main timeline or a sub-timeline
  $isMainTimeline = $page->uri() === 'timeline';

  if ($isMainTimeline) {
    // Main timeline: collect ALL timeline entries from across the site
    $entries = site()
      ->index()
      ->filterBy('intendedTemplate', 'timeline-entry');
  } else {
    // Sub-timeline: only show this page's children
    $entries = $page->children()->filterBy('intendedTemplate', 'timeline-entry');
  }

  // Sort by date
  $sortOrder = $page->sort_order()->or('desc');
  $entries = $entries->sortBy('date', $sortOrder);
  ?>

  <?php if ($entries->count() > 0): ?>
    <table class="timeline">
      <thead>
        <tr>
          <th scope="col" class="sr-only">Date</th>
          <th scope="col" class="sr-only">What happened</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($entries as $entry): ?>
          <tr>
            <?php if ($entry->hide_date()->toBool() === true): ?>
              <th class="sr-only" scope="row">
                <time datetime="<?= $entry->date()->toDate('Y-m-d') ?>">
                  <?= $entry->date()->toDate('F j, Y') ?>
                </time>
              </th>
            <?php else: ?>
              <th scope="row">
                <time datetime="<?= $entry->date()->toDate('Y-m-d') ?>">
                  <?= $entry->date()->toDate('F j, Y') ?>
                </time>
              </th>
            <?php endif ?>
            <td><?= $entry->description()->kt() ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No timeline entries yet.</p>
  <?php endif ?>
</article>

<style>
table, th, td {
  border: none;
  padding: 0;
  border-collapse: collapse;
}

table {
  margin: 3.5em 0;
  max-width: 40em;
  width: 100%;
  font-family: var(--theme-font-sans);
  border-collapse: collapse;
  font-size: 0.9em;
}

th, td {
  text-align: start;
  vertical-align: top;
}

th {
  padding-inline-end: 1.5em;
  text-align: end;
  font-weight: 500;
  text-align: right;
  white-space: nowrap;
}

td {
  padding-block-end: 1em;
  padding-inline-start: 1.5em;
  position: relative;
}

td::before {
  border: 3px solid black;
  background-color: white;
  border-radius: 50%;
  content: "";
  position: absolute;
  left: -6px;
  top: 0.35em;
  width: 6px;
  height: 6px;
  z-index: 1;
}

tr:not(:last-child) td::after {
  background-color: black;
  content: "";
  position: absolute;
  left: -3px;
  top: 0.4em;
  bottom: -0.4em;
  width: 6px;
}

.sr-only:not(:focus):not(:active) {
  clip: rect(0 0 0 0);
  clip-path: inset(100%);
  height: 1px;
  overflow: hidden;
  position: absolute;
  white-space: nowrap;
  width: 1px;
}
</style>

<?php snippet('site-footer') ?>
