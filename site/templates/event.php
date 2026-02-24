<?php
/**
 * templates/event.php
 * Single event page with h-event microformat + schema.org JSON-LD markup.
 */

$start   = $page->start()->isNotEmpty()  ? $page->start()->toDate()  : null;
$end     = $page->end()->isNotEmpty()    ? $page->end()->toDate()    : null;
$role    = $page->attendance_role()->value();
$related = $page->related()->toPages();

$roleLabel = [
    'attendee'  => '🎟 Attendee',
    'speaker'   => '🎤 Speaker',
    'volunteer' => '🙋 Volunteer',
    'organizer' => '📋 Organizer',
][$role] ?? $role;

// ─────────────────────────────────────────────────────────────
// schema.org JSON-LD
// https://schema.org/Event
// ─────────────────────────────────────────────────────────────
$schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Event',

    // Core identity
    'name'         => $page->name()->value(),
    'url'          => $page->event_url()->isNotEmpty() ? $page->event_url()->value() : $page->url(),

    // Dates — ISO 8601 format required by schema.org
    // eventStatus defaults to EventScheduled; change as needed.
    'eventStatus'  => 'https://schema.org/EventScheduled',

    // eventAttendanceMode: set to MixedEventAttendanceMode if unsure.
    // Options: OfflineEventAttendanceMode | OnlineEventAttendanceMode | MixedEventAttendanceMode
    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
];

if ($start) {
    $schema['startDate'] = date('c', $start);
}

if ($end) {
    $schema['endDate'] = date('c', $end);
}

// ISO 8601 duration — stored directly as-is (e.g. PT2H30M)
if ($page->duration()->isNotEmpty()) {
    $schema['duration'] = $page->duration()->value();
}

// description: prefer rich description, fall back to summary
if ($page->description()->isNotEmpty()) {
    // Strip HTML tags for the plain-text schema.org description
    $schema['description'] = strip_tags($page->description()->value());
} elseif ($page->summary()->isNotEmpty()) {
    $schema['description'] = $page->summary()->value();
}

// Location — mapped to schema.org Place
if ($page->location_name()->isNotEmpty()) {
    $place = [
        '@type' => 'Place',
        'name'  => $page->location_name()->value(),
    ];

    if ($page->location_url()->isNotEmpty()) {
        $place['url'] = $page->location_url()->value();
    }

    if ($page->location_address()->isNotEmpty()) {
        $place['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $page->location_address()->value(),
        ];
    }

    $schema['location'] = $place;

    // If the location name suggests online, update attendanceMode
    if (stripos($page->location_name()->value(), 'online') !== false) {
        $schema['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
    }
}

// keywords from category tags
if ($page->category()->isNotEmpty()) {
    $schema['keywords'] = implode(', ', array_map('trim', $page->category()->split(',')));
}

// Role-specific schema.org mappings:
// - 'speaker'   → performer (schema.org supports this directly on Event)
// - 'organizer' → organizer
// - 'attendee' / 'volunteer' → no direct schema.org equivalent on Event;
//   captured in h-event p-status instead.
$selfCard = [
    '@type' => 'Person',
    'url'   => $site->url(),
    'name'  => $site->title()->value(), // replace with your name if preferred
];

if ($role === 'speaker') {
    $schema['performer'] = $selfCard;
} elseif ($role === 'organizer') {
    $schema['organizer'] = $selfCard;
}
?>
<?php snippet('site-header') ?>

  <!--
    h-event root element.
    All microformat properties live inside this element.
  -->
  <article class="h-event wrapper">
    <!-- ── Header ──────────────────────────── -->
    <header class="event-header">
      <p class="back">
        <a href="<?= $page->parent()->url() ?>">← All Events</a>
      </p>
      <h1 class="p-name title">
        <?= $page->hed()->isNotEmpty()
            ? $page->hed()->html()
            : $page->title()->html() ?>
      </h1>

      <?php if ($page->event_url()->isNotEmpty()): ?>
        <a class="u-url ext-link" href="<?= $page->event_url()->escape() ?>" rel="noopener noreferrer" target="_blank">
           Official event site
        </a>
      <?php endif ?>
    </header>

    <!-- ── Meta grid ───────────────────────── -->
    <aside class="event-meta">
      <?php if ($start): ?>
        <div class="item">
          <span class="label">Start</span>
          <time class="dt-start" datetime="<?= date('c', $start) ?>">
            <?= date('j F Y', $start) ?>
            <?php if ($page->start()->value() && strpos($page->start()->value(), ' ') !== false): ?>
              at <?= date('H:i', $start) ?>
            <?php endif ?>
          </time>
        </div>
      <?php endif ?>

      <?php if ($end): ?>
        <div class="item">
          <span class="label">End</span>
          <time class="dt-end" datetime="<?= date('c', $end) ?>">
            <?= date('j F Y', $end) ?>
            <?php if ($page->end()->value() && strpos($page->end()->value(), ' ') !== false): ?> at <?= date('H:i', $end) ?>
            <?php endif ?>
          </time>
        </div>
      <?php endif ?>

      <?php if ($page->duration()->isNotEmpty()): ?>
        <div class="item">
          <span class="label">Duration</span>
          <!--
            dt-duration uses the ISO 8601 value directly.
            The human-readable text is supplementary.
          -->
          <abbr class="dt-duration" title="<?= $page->duration()->escape() ?>">
            <?= $page->duration()->escape() ?>
          </abbr>
        </div>
      <?php endif ?>

      <?php if ($page->location_name()->isNotEmpty()): ?>
        <div class="item">
          <span class="label">Location</span>
          <?php if ($page->location_url()->isNotEmpty()): ?>
            <a class="p-location h-card" href="<?= $page->location_url()->escape() ?>" rel="noopener noreferrer" target="_blank">
              <?= $page->location_name()->escape() ?>
            </a>
          <?php else: ?>
            <span class="p-location">
              <?= $page->location_name()->escape() ?>
            </span>
          <?php endif ?>
          <?php if ($page->location_address()->isNotEmpty()): ?>
            <address class="address">
              <?= $page->location_address()->escape() ?>
            </address>
          <?php endif ?>
        </div>
      <?php endif ?>

      <?php if ($page->category()->isNotEmpty()): ?>
        <div class="item">
          <span class="label">Category</span>
          <span class="value">
            <?php foreach ($page->category()->split(',') as $cat): ?>
              <span class="p-category event-tag">
                <?= trim(esc($cat)) ?>
              </span>
            <?php endforeach ?>
          </span>
        </div>
      <?php endif ?>

      <?php if ($role): ?>
        <div class="item">
          <span class="label">My Role</span>
          <!-- p-status repurposed for attendance role -->
          <span class="p-status event-role event-role--<?= $role ?>">
            <?= $roleLabel ?>
          </span>
        </div>
      <?php endif ?>
    </aside>

    <!-- ── Summary ─────────────────────────── -->
    <?php if ($page->summary()->isNotEmpty()): ?>
      <p class="p-summary">
        <?= $page->summary()->escape() ?>
      </p>
    <?php endif ?>

    <!-- ── Description ─────────────────────── -->
    <?php if ($page->description()->isNotEmpty()): ?>
      <div class="e-content event-description">
        <?= $page->description() ?>
      </div>
    <?php endif ?>

    <!-- ── Related Pages ───────────────────── -->
    <?php if ($related->isNotEmpty()): ?>
      <section class="event-related">
        <h2>Related</h2>
        <ul>
          <?php foreach ($related as $rel): ?>
            <li class="item">
              <a href="<?= $rel->url() ?>">
                <?= $rel->title()->escape() ?>
              </a>
              <span class="path">
                <?= '/' . $rel->uri() ?>
              </span>
            </li>
          <?php endforeach ?>
        </ul>
      </section>
    <?php endif ?>
</article>
<?php snippet('site-footer') ?>
