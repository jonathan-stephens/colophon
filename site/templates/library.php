<?php
  $books = $page->children()->listed()->filterBy('template', 'book');
?>

<?php snippet('site-header') ?>
  <header class="wrapper">
    <form class="category-filters cluster" id="categoryFilters" role="radiogroup" aria-label="Filter books by category">
      <input type="radio"
             id="filter-all"
             name="category-filter"
             value="all"
             class="filter-radio"
             checked>
      <label for="filter-all" class="p-category button">All</label>

      <?php
        // Get all unique categories and their counts
        $categories = [];
        $categoryCounts = [];
        foreach ($books as $book) {
          $bookCategories = $book->category()->split(',');
          foreach ($bookCategories as $cat) {
              $cat = trim($cat);
              if ($cat) {
                  if (!in_array($cat, $categories)) {
                      $categories[] = $cat;
                      $categoryCounts[$cat] = 1;
                  } else {
                      $categoryCounts[$cat]++;
                  }
              }
          }
      }
        sort($categories);
        foreach ($categories as $category):
          $radioId = 'filter-' . Str::slug($category);
      ?>
          <input type="radio"
                 id="<?= $radioId ?>"
                 name="category-filter"
                 value="<?= esc($category) ?>"
                 class="filter-radio">
          <label for="<?= $radioId ?>" class="p-category button">
              <span class="count"><?= $categoryCounts[$category] ?></span>
              <?= esc($category) ?>
          </label>
      <?php endforeach; ?>
    </form>
    <p class="books-count" id="booksCount">
      Showing <span class="count-number"><?= $books->count() ?></span> of <?= $books->count() ?> books
    </p>
  </header>

    <div class="books-container wrapper" id="booksContainer">
    <?php foreach($books as $book): ?>
      <article class="book"
               data-categories="<?= esc($book->category()) ?>"
               itemscope
               itemtype="https://schema.org/Book">
        <?php
        $cats = $book->category()->split(', ');

        if (!empty($cats)): ?>
          <header class="eyebrow">
            <?php
            $visibleTags = array_slice($cats, 0, 5);
            $totalVisible = count($visibleTags);
            ?>
            <?php foreach ($visibleTags as $i => $cat): ?>
              <span rel="tag" class="p-category" itemprop="genre">
                <?= trim($cat) ?><?php if ($i < $totalVisible - 1): ?>,<?php endif ?>
              </span>
            <?php endforeach ?>
            <?php if (count($cats) > 5): ?>
              <span class="tag-more">+<?= count($cats) - 5 ?> more</span>
            <?php endif ?>
          </header>
        <?php endif ?>
        <div class="content">
          <h3 class="hed" itemprop="name"><?= $book->hed()->html() ?></h3>

          <?php if($book->dek()->isNotEmpty()): ?>
            <p class="dek" itemprop="alternateName"><?= $book->dek()->html() ?></p>
          <?php endif ?>

          <?php
          $authors = $book->authors()->toStructure();
          if ($authors->isNotEmpty()): ?>
            <div class="authors">
              <?php
              $authorLinks = [];
              foreach ($authors as $authorData):
                $authorNames = $authorData->author()->split(',');
                $authorRole = $authorData->authorRole()->split(',');
                $authorURL = $authorData->authorURL();

                foreach ($authorNames as $name):
                  $name = trim($name);
                  if ($authorURL->isNotEmpty()):
                    $authorLinks[] = '<a href="' . $authorURL . '" target="_blank" rel="noopener" itemprop="author">' . $name . '</a>';
                  else:
                    $authorLinks[] = '<span itemprop="author">' . $name . '</span>';
                  endif;
                endforeach;
              endforeach;
              echo implode(', ', $authorLinks);
              ?>
            </div>
          <?php endif ?>
        </div>
          <?php
          $affiliates = $book->affiliates()->toStructure();
          if ($affiliates->isNotEmpty()): ?>
            <footer class="outlinks">
              <?php foreach ($affiliates->limit(5) as $affiliateData): ?>
                <?php
                $affiliateNames = $affiliateData->affiliate()->split(',');
                $affiliateURL = $affiliateData->url();
                if ($affiliateURL->isNotEmpty()): ?>
                  <a href="<?= $affiliateURL ?>"
                     target="_blank"
                     rel="noopener nofollow"
                     class="button with-icon"
                     data-button-variant="ghost"
                     itemprop="offers"
                     itemscope
                     itemtype="https://schema.org/Offer">
                    <span itemprop="seller"><?= !empty($affiliateNames) ? trim($affiliateNames[0]) : 'Buy' ?></span>
                    <?= asset('assets/svg/icons/launch.svg')->read() ?>
                  </a>
                <?php endif ?>
              <?php endforeach ?>
              <?php if ($affiliates->count() > 5): ?>
                <a href="<?= $book->url() ?>" class="more-options">+<?= $affiliates->count() - 5 ?> more</a>
              <?php endif ?>
            </footer>
          <?php endif ?>
      </article>
    <?php endforeach ?>
    </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('categoryFilters');
    const radioButtons = filterForm.querySelectorAll('.filter-radio');
    const bookArticles = document.querySelectorAll('.book');
    const booksCount = document.getElementById('booksCount');
    const totalBooks = <?= $books->count() ?>;

    let lastChecked = null;

    // Initialize from URL params
    function initFromURL() {
      const urlParams = new URLSearchParams(window.location.search);
      const filter = urlParams.get('filter');

      if (filter) {
        const radio = filterForm.querySelector(`input[value="${filter}"]`);
        if (radio) {
          radio.checked = true;
          lastChecked = radio;
          filterBooks();
        }
      } else {
        // Default to "all" being checked
        const allRadio = filterForm.querySelector('input[value="all"]');
        if (allRadio) {
          lastChecked = allRadio;
        }
      }
    }

    // Update URL without page reload
    function updateURL(category) {
      const url = new URL(window.location);
      if (category !== 'all') {
        url.searchParams.set('filter', category);
      } else {
        url.searchParams.delete('filter');
      }
      window.history.replaceState({}, '', url);
    }

    // Filter books based on selected category
    function filterBooks() {
      const checkedRadio = filterForm.querySelector('input[name="category-filter"]:checked');
      const currentCategory = checkedRadio ? checkedRadio.value : 'all';
      let visibleCount = 0;

      bookArticles.forEach((article, index) => {
        const articleCategories = article.dataset.categories.split(',').map(c => c.trim());

        let shouldShow = false;
        if (currentCategory === 'all') {
          shouldShow = true;
        } else {
          shouldShow = articleCategories.includes(currentCategory);
        }

        if (shouldShow) {
          // Show with staggered animation
          article.classList.remove('filtering-out', 'collapsed');
          article.classList.add('filtering-in');

          // Remove animation class after it completes
          setTimeout(() => {
            article.classList.remove('filtering-in');
          }, 600);

          visibleCount++;
        } else {
          // Hide with two-stage process
          article.classList.remove('filtering-in');
          article.classList.add('filtering-out');

          // After fade out, collapse the space
          setTimeout(() => {
            if (article.classList.contains('filtering-out')) {
              article.classList.add('collapsed');
            }
          }, 250); // Halfway through the transition
        }
      });

      // Update count
      booksCount.innerHTML = `
        Showing <span class="count-number">${visibleCount}</span> of ${totalBooks} books
      `;
    }

    // Handle radio button clicks (for deselect functionality)
    radioButtons.forEach(radio => {
      radio.addEventListener('click', function(e) {
        // If clicking the already checked radio (and it's not "All")
        if (this === lastChecked && this.value !== 'all') {
          // Deselect it and select "All" instead
          this.checked = false;
          const allRadio = filterForm.querySelector('input[value="all"]');
          if (allRadio) {
            allRadio.checked = true;
            lastChecked = allRadio;
          }
          filterBooks();
          updateURL('all');
        } else {
          // Normal selection
          lastChecked = this;
          filterBooks();
          updateURL(this.value);
        }
      });

      // Also handle change events for keyboard navigation
      radio.addEventListener('change', function() {
        if (this.checked && this !== lastChecked) {
          lastChecked = this;
          filterBooks();
          updateURL(this.value);
        }
      });
    });

    // Initialize
    initFromURL();
  });
    </script>
<?php snippet('site-footer') ?>
