<?php
  $books = $page->children()->listed()->filterBy('template', 'book');
?>

<?php snippet('site-header') ?>
  <header class="wrapper">
    <div class="category-filters cluster" id="categoryFilters">
      <button class="p-category button" data-category="all">All</button>
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
          foreach ($categories as $category): ?>
            <button class="p-category button" data-category="<?= esc($category) ?>">
                <span class="count"><?= $categoryCounts[$category] ?></span><?= esc($category) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <p class="books-count" id="booksCount">
      Showing <span class="count-number"><?= $books->count() ?> of <?= $books->count() ?> books</span>
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
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.p-category.button');
    const bookArticles = document.querySelectorAll('.book');
    const booksCount = document.getElementById('booksCount');
    const totalBooks = <?= $books->count() ?>;

    let selectedCategories = new Set();

    // Initialize from URL params
    function initFromURL() {
      const urlParams = new URLSearchParams(window.location.search);
      const filters = urlParams.get('filters');

      if (filters) {
        selectedCategories = new Set(filters.split(',').filter(f => f !== 'all' && f));
        updateUI();
        filterBooks();
      }
    }

    // Update URL without page reload
    function updateURL() {
      const url = new URL(window.location);
      if (selectedCategories.size > 0) {
        url.searchParams.set('filters', Array.from(selectedCategories).join(','));
      } else {
        url.searchParams.delete('filters');
      }
      window.history.replaceState({}, '', url);
    }

    // Update button states
    function updateUI() {
      // Update button states
      filterBtns.forEach(btn => {
        const category = btn.dataset.category;
        if (category === 'all') {
          btn.classList.toggle('active', selectedCategories.size === 0);
        } else {
          btn.classList.toggle('active', selectedCategories.has(category));
        }
      });
    }

    // Filter books based on selected categories
    function filterBooks() {
      let visibleCount = 0;

      bookArticles.forEach(article => {
        const articleCategories = article.dataset.categories.split(',').map(c => c.trim());

        let shouldShow = false;
        if (selectedCategories.size === 0) {
          shouldShow = true;
        } else {
          // ANY logic - show if book has any of the selected categories
          shouldShow = Array.from(selectedCategories).some(cat =>
            articleCategories.includes(cat)
          );
        }

        if (shouldShow) {
          article.classList.remove('filtering-out', 'hidden');
          visibleCount++;
        } else {
          article.classList.add('filtering-out');
          setTimeout(() => {
            if (article.classList.contains('filtering-out')) {
              article.classList.add('hidden');
            }
          }, 300);
        }
      });

      // Update count
      booksCount.innerHTML = `
        Showing <span class="count-number">${visibleCount}</span> of ${totalBooks} books
      `;
    }

    // Handle filter button clicks
    filterBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const category = this.dataset.category;

        if (category === 'all') {
          selectedCategories.clear();
        } else {
          if (selectedCategories.has(category)) {
            selectedCategories.delete(category);
          } else {
            selectedCategories.add(category);
          }
        }

        updateUI();
        filterBooks();
        updateURL();
      });
    });

    // Initialize
    initFromURL();
  });
  </script>
<?php snippet('site-footer') ?>
