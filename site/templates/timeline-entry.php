<?php
/**
 * Template: Timeline Entry
 * Location: site/templates/timeline-entry.php
 *
 * Individual timeline entries typically aren't linked to directly,
 * but this template provides a view if needed.
 */
?>
<?php snippet('site-header') ?>

<article>
  <header>
    <h1><?= $page->title() ?></h1>
    <?php if ($page->date()->isNotEmpty()): ?>
      <time datetime="<?= $page->date()->toDate('Y-m-d') ?>">
        <?= $page->date()->toDate('F j, Y') ?>
      </time>
    <?php endif ?>
  </header>

  <div class="timeline-entry-content">
    <?= $page->description()->kt() ?>
  </div>

  <?php if ($page->parent()->intendedTemplate()->value() === 'timeline'): ?>
    <footer>
      <p><a href="<?= $page->parent()->url() ?>">← Back to <?= $page->parent()->title() ?></a></p>
    </footer>
  <?php endif ?>
</article>

<?php snippet('site-footer') ?>
