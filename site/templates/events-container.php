<?php
/**
 * templates/events.php
 * Listing of all events, grouped by year, sorted newest first.
 * Includes schema.org ItemList + individual Event JSON-LD blocks.
 */

// Fetch all event children, sorted by start date descending
$events = $page->children()
    ->filterBy('template', 'event')
    ->sortBy('start', 'desc');

// Group events by year
$byYear = [];
foreach ($events as $event) {
    $year = $event->start()->toDate('Y');
    $byYear[$year][] = $event;
}

// Sort years descending
krsort($byYear);

// ─────────────────────────────────────────────────────────────
// schema.org JSON-LD — ItemList of Events
// https://schema.org/ItemList
// ─────────────────────────────────────────────────────────────
$listItems = [];
$position  = 1;

foreach ($events as $event) {
    $start = $event->start()->isNotEmpty() ? $event->start()->toDate() : null;
    $end   = $event->end()->isNotEmpty()   ? $event->end()->toDate()   : null;

    $eventSchema = [
        '@type'       => 'Event',
        'name'        => $event->name()->value(),
        'url'         => $event->url(),
        'eventStatus' => 'https://schema.org/EventScheduled',
    ];

    if ($start) {
        $eventSchema['startDate'] = date('c', $start);
    }

    if ($end) {
        $eventSchema['endDate'] = date('c', $end);
    }

    if ($event->location_name()->isNotEmpty()) {
        $eventSchema['location'] = [
            '@type' => 'Place',
            'name'  => $event->location_name()->value(),
        ];
    }

    if ($event->summary()->isNotEmpty()) {
        $eventSchema['description'] = $event->summary()->value();
    }

    $listItems[] = [
        '@type'    => 'ListItem',
        'position' => $position++,
        'item'     => $eventSchema,
    ];
}

$schema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => $page->title()->value() . ' — ' . $site->title()->value(),
    'url'             => $page->url(),
    'numberOfItems'   => count($listItems),
    'itemListElement' => $listItems,
];
?>

<?php snippet('site-header') ?>
<header class="page-header wrapper">
  <h1><?= $page->title() ?></h1>
  <?php if ($page->description()->isNotEmpty()): ?>
    <p class="dek"><?= $page->description()->escape() ?></p>
  <?php endif ?>
</header>

<div class="wrapper">
<?php if ($events->isEmpty()): ?>
  <p class="no-events">No events yet.</p>
<?php else: ?>

  <?php foreach ($byYear as $year => $yearEvents): ?>
    <section class="events-year" id="year-<?= $year ?>">
      <h2><?= $year ?></h2>
      <ul>
        <?php foreach ($yearEvents as $event): ?>
          <?php
            $start    = $event->start()->toDate();
            $end      = $event->end()->isNotEmpty() ? $event->end()->toDate() : null;
            $role     = $event->attendance_role()->value();
            $roleLabel = [
              'attendee'  => 'Attendee',
              'speaker'   => 'Speaker',
              'volunteer' => 'Volunteer',
              'organizer' => 'Organizer',]
            [$role] ?? $role;
          ?>
          <li class="item h-event">
            <time class="dt-start" datetime="<?= date('c', $start) ?>">
              <?= date('j M', $start) ?>
            </time>
            <div class="events-list__body">
              <a class="u-url p-name" href="<?= $event->url() ?>">
                <h3><?= $event->title()->escape() ?></h3>
              </a>
              <?php if ($event->location_name()->isNotEmpty()): ?>
                <span class="p-location">
                  <?= $event->location_name()->escape() ?>
                </span>
              <?php endif ?>
            </div>
            <span class="role--<?= $role ?>">
              <?= $roleLabel ?>
            </span>
          </li>
        <?php endforeach ?>
      </ul>
    </section>
  <?php endforeach ?>
<?php endif ?>
</div>
<?php snippet('site-footer') ?>
