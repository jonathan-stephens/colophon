<?php
  $books = $page->children()->listed()->filterBy('template', 'book')->shuffle();
?>

<?php snippet('site-header') ?>
  <header class="wrapper">
    <?php if($page->hed()->isNotEmpty()): ?>
      <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
    <?php else: ?>
      <h1 class="p-name" itemprop="name headline"><?= $page->title() ?></h1>
    <?php endif ?>

    <?php if($page->dek()->isNotEmpty()): ?>
      <?= $page->dek()->kt() ?>
    <?php endif?>

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
        // Sort categories by count (descending), then alphabetically
        arsort($categoryCounts);
        $categories = array_keys($categoryCounts);
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
    let isFiltering = false;

    // Initialize from URL params
    function initFromURL() {
      const urlParams = new URLSearchParams(window.location.search);
      const filter = urlParams.get('filter');

      if (filter) {
        const radio = filterForm.querySelector(`input[value="${filter}"]`);
        if (radio) {
          radio.checked = true;
          lastChecked = radio;
          filterBooks(false); // No animation on initial load
        }
      } else {
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

    // Filter books with smooth animations
    function filterBooks(animate = true) {
      if (isFiltering) return;
      isFiltering = true;

      const checkedRadio = filterForm.querySelector('input[name="category-filter"]:checked');
      const currentCategory = checkedRadio ? checkedRadio.value : 'all';

      // Determine which books should be visible
      const toHide = [];
      const visibilityMap = new Map();
      let visibleCount = 0;

      bookArticles.forEach(article => {
        const articleCategories = article.dataset.categories.split(',').map(c => c.trim());
        const shouldShow = currentCategory === 'all' || articleCategories.includes(currentCategory);
        const isCurrentlyVisible = !article.classList.contains('hidden');

        visibilityMap.set(article, shouldShow);

        if (shouldShow) {
          visibleCount++;
        } else if (isCurrentlyVisible) {
          toHide.push(article);
        }
      });

      if (animate) {
        // Phase 1: Fade out cards that should be hidden
        toHide.forEach(article => {
          article.classList.add('exiting');
        });

        // Phase 2: After exit animation completes
        setTimeout(() => {
          // Hide the exited cards
          toHide.forEach(article => {
            article.classList.add('hidden');
            article.classList.remove('exiting');
          });

          // Show all cards that should be visible (but keep them invisible)
          bookArticles.forEach(article => {
            if (visibilityMap.get(article)) {
              article.classList.remove('hidden');
              article.classList.add('entering');
            }
          });

          // Phase 3: Wait for layout to settle, then animate in sequentially
          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              // Get only the visible cards in DOM order
              const visibleCards = Array.from(bookArticles).filter(article =>
                visibilityMap.get(article)
              );

              // Animate each card in sequence
              visibleCards.forEach((article, index) => {
                setTimeout(() => {
                  article.classList.remove('entering');
                  article.classList.add('animate-in');

                  // Clean up animation class after it completes
                  setTimeout(() => {
                    article.classList.remove('animate-in');
                  }, 1000);
                }, index * 150); // 60ms stagger between each card
              });

              // Mark filtering as complete after all animations start
              setTimeout(() => {
                isFiltering = false;
              }, visibleCards.length * 60 + 100);
            });
          });

        }, 600); // Match CSS exit transition duration

      } else {
        // No animation - instant filter
        bookArticles.forEach(article => {
          const shouldShow = visibilityMap.get(article);

          if (shouldShow) {
            article.classList.remove('hidden', 'exiting', 'entering', 'animate-in');
          } else {
            article.classList.add('hidden');
            article.classList.remove('exiting', 'entering', 'animate-in');
          }
        });
        isFiltering = false;
      }

      // Update count
      booksCount.innerHTML = `
        Showing <span class="count-number">${visibleCount}</span> of ${totalBooks} books
      `;
    }

    // Handle radio button clicks
    radioButtons.forEach(radio => {
      radio.addEventListener('click', function(e) {
        if (isFiltering) return;

        if (this === lastChecked && this.value !== 'all') {
          this.checked = false;
          const allRadio = filterForm.querySelector('input[value="all"]');
          if (allRadio) {
            allRadio.checked = true;
            lastChecked = allRadio;
          }
          filterBooks();
          updateURL('all');
        } else {
          lastChecked = this;
          filterBooks();
          updateURL(this.value);
        }
      });

      radio.addEventListener('change', function() {
        if (this.checked && this !== lastChecked && !isFiltering) {
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
