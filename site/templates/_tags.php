<?php
// Check if we're filtering by specific tags
$filterTags = $filterTags ?? null;

if($filterTags) {
  // This is a tag filter page
  // Find all pages with these tags
  $taggedPages = site()->index()->filterBy('tags', 'in', $filterTags, ',');

  // Group pages by section/template
  $sections = [];
  foreach ($taggedPages as $p) {
    $section = $p->parent()->uri();
    if (!isset($sections[$section])) {
      $sections[$section] = [];
    }
    $sections[$section][] = $p;
  }

  // Sort sections by key
  ksort($sections);

  // Use the tag template
  snippet('site-header');
  ?>

  <div class="wrapper">
    <h1>
      <?php if (count($filterTags) > 1): ?>
        Posts tagged with: <?= implode(', ', $filterTags) ?>
      <?php else: ?>
        Posts tagged with: <?= $filterTags[0] ?>
      <?php endif ?>
    </h1>

    <p>Found <?= $taggedPages->count() ?> posts with this tag combination.</p>

    <a href="<?= url('tags') ?>">&larr; All tags</a>

    <?php foreach($sections as $section => $pages): ?>
      <section>
        <h2><?= ucfirst($section) ?></h2>
        <ul>
          <?php foreach($pages as $p): ?>
            <li>
              <a href="<?= $p->url() ?>"><?= $p->title() ?></a>
              <?php if($p->date()->isNotEmpty()): ?>
                <span class="date"><?= $p->date()->toDate('Y-m-d') ?></span>
              <?php endif ?>
            </li>
          <?php endforeach ?>
        </ul>
      </section>
    <?php endforeach ?>
  </div>

  <?php
  snippet('site-footer');

} else {
  // This is the tags index page showing all tags
  // Get all pages with tags
  $taggedPages = site()->index()->filterBy('tags', '!=', null);

  // Extract all unique tags with counts
  $tags = [];
  foreach($taggedPages as $p) {
    $pageTags = $p->tags()->split(',');
    foreach($pageTags as $tag) {
      $tag = trim($tag);
      if(!empty($tag)) {
        if(!isset($tags[$tag])) {
          $tags[$tag] = 0;
        }
        $tags[$tag]++;
      }
    }
  }

  // Sort tags by count (descending)
  arsort($tags);

  snippet('site-header');
  ?>

  <div class="wrapper">
    <h1><?= $page->title()->html() ?></h1>

    <?php if($page->intro()->isNotEmpty()): ?>
      <div class="intro">
        <?= $page->intro()->kirbytext() ?>
      </div>
    <?php endif ?>

    <div class="tags-cloud">
      <ul>
        <?php foreach($tags as $tag => $count): ?>
          <li>
            <a href="<?= url('tags/' . urlencode($tag)) ?>" class="p-category button">
              <span class="count"><?= $count ?></span>

              <?= html($tag) ?>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>

  <?php
  snippet('site-footer');
}
?>
