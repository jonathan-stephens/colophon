<?php
/**
 * Stream template — Phase 1
 *
 * Plain semantic list of all stream entries.
 * Data attributes are set on each article now so filter
 * logic in later phases can target them without touching this markup.
 */
$stream     = $kirby->collection('stream');
$isLoggedIn = $kirby->user() !== null;
?>
<?php snippet('site-header') ?>

<header class="wrapper">

  <h1 class="p-name" itemprop="name headline">
    <?= $page->hed()->isNotEmpty() ? $page->hed()->html() : $page->title()->html() ?>
  </h1>

  <?php if ($page->dek()->isNotEmpty()): ?>
    <p class="dek"><?= $page->dek()->html() ?></p>
  <?php endif ?>

  <p class="stream-count" id="streamCount">
    <span class="count-number"><?= $stream->count() ?></span> entries
  </p>

</header>

<div class="stream wrapper" id="streamContainer">

  <?php foreach ($stream as $entry): ?>
    <?php
      // Resolve these once per entry for clarity
      $blueprintTitle = $entry->blueprint()->title();
      $templateName   = $entry->intendedTemplate()->name();
      $status         = $entry->status(); // listed | unlisted | draft

      // Category: comma-separated field, may be absent
      $cats = $entry->category()->isNotEmpty()
        ? array_map('trim', $entry->category()->split(','))
        : [];

      // Effective display title
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
            <?= $entry->growthStatus()->html() ?>
          </span>
        <?php endif ?>

        <?php
        // Show page status badge to logged-in users for non-listed pages
        if ($isLoggedIn && $status !== 'listed'): ?>
          <span class="page-status page-status--<?= esc($status) ?>" aria-label="Status: <?= esc($status) ?>">
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

<?php snippet('site-footer') ?>
