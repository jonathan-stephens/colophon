<?php
/*
  USAGE EXAMPLES:

  For /links:
    <?php snippet('components/pagination', ['pagination' => $articles->pagination()])

  For /wherever-I-want-pagination:
    <?php $items = $page->children()->paginate(10);
    snippet('components/pagination', ['pagination' => $items->pagination()]); ?>
*/

if (!isset($pagination) || !$pagination->hasPages()) return;

// Calculate the range of items shown on current page
$currentPage = $pagination->page();
$perPage = $pagination->limit();
$total = $pagination->total();

$startItem = (($currentPage - 1) * $perPage) + 1;
$endItem = min($currentPage * $perPage, $total);
?>
<nav class="pagination" aria-label="Page navigation">
  <p class="range">
    <?= $startItem ?>–<?= $endItem ?> of <?= $total ?>
  </p>

  <ul class="pagination-list">
    <!-- Previous Page -->
    <?php if ($pagination->hasPrevPage()): ?>
    <li class="pagination-item">
      <a href="<?= $pagination->prevPageURL() ?>" class="pagination-link" aria-label="Go to previous page">
        <span>← </span> Prev<span>ious</span></a>
    </li>
    <?php else: ?>
    <li class="pagination-item">
      <span class="pagination-link disabled" aria-label="Previous page (unavailable)"><span>← </span> Prev<span>ious</span></span>
    </li>
    <?php endif ?>


    <?php
    $totalPages = $pagination->pages();
    $pagesToShow = [];

    if ($totalPages <= 7) {
        // Show all pages if 7 or less
        for ($i = 1; $i <= $totalPages; $i++) {
            $pagesToShow[] = $i;
        }
    } else {
        // For more than 7 pages: 1...current-1 current current+1 current+2...middle 100%

        // Always start with page 1
        $pagesToShow[] = 1;

        // Add ellipsis if there's a gap after 1
        if ($currentPage > 3) {
            $pagesToShow[] = '...';
        }

        // Determine how many pages we need for current context to ensure minimum 7 total
        $minPagesNeeded = 7;
        $currentContextStart = max(2, $currentPage - 1);
        $currentContextEnd = min($totalPages - 1, $currentPage + 2);

        // If we're near the end, expand the context to ensure we have enough pages
        $contextSize = $currentContextEnd - $currentContextStart + 1;
        $otherPages = 2; // page 1 and last page
        if ($currentPage > 3) $otherPages++; // ellipsis after 1

        // Calculate middle page
        $middlePage = (int) round(($currentContextEnd + $totalPages) / 2);
        if ($middlePage > $currentContextEnd && $middlePage < $totalPages) {
            $otherPages += 2; // ellipsis and middle page
        }

        // If we don't have enough pages, expand the current context backwards
        $totalCurrentPages = $contextSize + $otherPages;
        if ($totalCurrentPages < $minPagesNeeded) {
            $needMore = $minPagesNeeded - $totalCurrentPages;
            $currentContextStart = max(2, $currentContextStart - $needMore);
        }

        // Add current page context
        for ($i = $currentContextStart; $i <= $currentContextEnd; $i++) {
            if (!in_array($i, $pagesToShow)) {
                $pagesToShow[] = $i;
            }
        }

        // Recalculate middle page based on potentially expanded context
        $middlePage = (int) round(($currentContextEnd + $totalPages) / 2);

        // Add ellipsis and middle page if there's ANY gap
        if ($middlePage > $currentContextEnd && $middlePage < $totalPages) {
            $pagesToShow[] = '...';
            $pagesToShow[] = $middlePage;
        }

        // Always end with last page (no ellipsis before it)
        if (!in_array($totalPages, $pagesToShow)) {
            $pagesToShow[] = $totalPages;
        }
    }

    // Display the pages
    foreach ($pagesToShow as $r):
        if ($r === '...'): ?>
            <li class="pagination-item">
                <span class="pagination-ellipsis">...</span>
            </li>
        <?php else: ?>
            <li class="pagination-item">
                <a<?= $currentPage === $r ? ' aria-current="page" class="pagination-link pagination-current"' : ' class="pagination-link"' ?> href="<?= $pagination->pageURL($r) ?>" aria-label="Go to page <?= $r ?>">
                    <?= $r ?>
                </a>
            </li>
        <?php endif;
    endforeach ?>


    <!-- Next Page -->
    <?php if ($pagination->hasNextPage()): ?>
    <li class="pagination-item">
      <a href="<?= $pagination->nextPageURL() ?>" class="pagination-link" aria-label="Go to next page">Next <span> →</span></a>
    </li>
    <?php else: ?>
    <li class="pagination-item">
      <span class="pagination-link disabled" aria-label="Next page (unavailable)">Next <span> →</span></span>
    </li>
    <?php endif ?>
  </ul>
  <div class="per-page">
      <label for="per-page-select">Per Page</label>
      <select id="per-page-select" onchange="changePerPage(this.value)">
          <option value="16"<?= $perPage == 16 ? ' selected' : '' ?>>16</option>
          <option value="56"<?= $perPage == 56 ? ' selected' : '' ?>>56</option>
          <option value="121"<?= $perPage == 121 ? ' selected' : '' ?>>121</option>
          <option value="211"<?= $perPage == 211 ? ' selected' : '' ?>>211</option>
          <option value="326"<?= $perPage == 326 ? ' selected' : '' ?>>326</option>
          <option value="<?= $total ?>"<?= $perPage >= $total ? ' selected' : '' ?>>All'o'them</option>
        </select>
        <!-- Introducing Pagination: https://jonathanstephens.us/journal/introducing-pagination

        The answer is: The Lazy Caterer's Sequence -->
    </div>
  <script>
  function changePerPage(newLimit) {
    const url = new URL(window.location);
    url.searchParams.set('limit', newLimit);
    url.searchParams.delete('page'); // Reset to first page when changing limit
    window.location.href = url.toString();
  }
  </script>
</nav>
