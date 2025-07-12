<?php snippet('site-header') ?>
  <div class="wrapper">
    <?php if($library = $page->children()->listed()): ?>
        <?php foreach($library as $book): ?>
          <article class="book">
            <h2 class="title"><?= $book->hed()->html() ?></h2>

            <?php if ($cover = $book->coverImage()): ?>
              <div class="book-cover">
                <img src="<?= $cover->url() ?>" alt="<?= $cover->alt()->esc() ?>">
              </div>
            <?php else: ?>
              <p class="no-cover">No cover image available</p>
            <?php endif ?>


            <?php if($book->dek()->isNotEmpty()): ?>
              <p class="dek"><?= $book->dek()->html() ?></p>
            <?php endif ?>

                <?php if($book->authors()->isNotEmpty()): ?>
                  <p class="author"><strong>Author<?= $book->authors()->toStructure()->count() > 1 ? 's' : '' ?> </strong></p>

                    <?php
                    $authors = $book->authors()->toStructure();
                    $totalAuthors = $authors->count();
                    foreach($authors as $index => $author): ?>
                        <span class="author-name">
                            <?= $author->name()->html() ?><?php if($index < $totalAuthors - 1): ?>, <?php endif ?>
                        </span>
                    <?php endforeach ?>
                  </p>
                <?php endif ?>

                <footer>
                  <?php if($book->purchaseLink()->isNotEmpty()): ?>
                    <a href="<?= $book->purchaseLink() ?>">Buy via Bookshop.org <em>(affiliate link)</em></a>
                  <?php endif ?>

                  <?php if($book->children()->listed()): ?>
                    <a href="<?= $book->url() ?>">Some favorite quotes</a>
                  <?php endif ?>
                </footer>
              </article>
        <?php endforeach ?>
    <?php else: ?>
      <p class="no-books">No books have been added yet.</p>
    <?php endif ?>
  </div>

<?php snippet('site-footer') ?>
