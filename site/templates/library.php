<?php
  $books = $page->children()->listed()->filterBy('template', 'book');
?>

<?php snippet('site-header') ?>
  <div class="wrapper">

    <?php foreach($books as $book): ?>
      <article class="book">
        <?php
        $cats = $book->category()->split(', ');

        if (!empty($cats)): ?>
          <header class="eyebrow">
            <?php
            $visibleTags = array_slice($cats, 0, 5);
            $totalVisible = count($visibleTags);
            ?>
            <?php foreach ($visibleTags as $i => $cat): ?>
              <span rel="tag" class="p-category">
                <?= trim($cat) ?><?php if ($i < $totalVisible - 1): ?>,<?php endif ?>
              </span>
            <?php endforeach ?>
            <?php if (count($cats) > 5): ?>
              <span class="tag-more">+<?= count($cats) - 5 ?> more</span>
            <?php endif ?>
          </header>
        <?php endif ?>
        <div class="content">
          <h3 class="hed"><?= $book->hed()->html() ?></h3>

          <?php if($book->dek()->isNotEmpty()): ?>
            <p class="dek"><?= $book->dek()->html() ?></p>
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
                    $authorLinks[] = '<a href="' . $authorURL . '" target="_blank" rel="noopener">' . $name . '</a>';
                  else:
                    $authorLinks[] = $name;
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
                  <a href="<?= $affiliateURL ?>" target="_blank" rel="noopener nofollow" class="button with-icon" data-button-variant="ghost">
                    <?= !empty($affiliateNames) ? trim($affiliateNames[0]) : 'Buy' ?>
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
  // Auto-submit form when filters change
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('libraryFilters');
    const selects = form.querySelectorAll('select');

    selects.forEach(select => {
      select.addEventListener('change', function() {
        // Auto-submit when any dropdown changes
        form.submit();
      });
    });
  });
  </script>
<?php snippet('site-footer') ?>
