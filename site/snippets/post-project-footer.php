<?php
// Get sibling pages for pagination
$siblings = $page->siblings()->listed();
$currentIndex = $siblings->indexOf($page);

// Looping pagination - wraps around when reaching first/last page
$prev = $currentIndex > 0 ? $siblings->nth($currentIndex - 1) : $siblings->last();
$next = $currentIndex < $siblings->count() - 1 ? $siblings->nth($currentIndex + 1) : $siblings->first();
?>
  <footer>
    <nav class="pagination" aria-label="Page navigation">
      <a href="<?= $prev->url() ?>" class="prev" rel="prev">
        <small class="direction">
          ← Previous
        </small>
        <span class="label">
          <strong><?= $prev->title()->html() ?></strong>
          <span><?= $prev->dek()->html() ?></span>
        </span>
      </a>
      <a href="<?= $next->url() ?>" class="next" rel="next">
        <small class="direction">Next →</small>
        <span class="label">
          <strong><?= $next->title()->html() ?></strong>
          <span><?= $next->dek()->html() ?></span>
        </span>
      </a>
    </nav>
  </footer>
</article>
