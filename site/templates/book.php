<?php snippet('header') ?>

<article class="book-detail wrapper">
      <h1 class="book-title"><?= $page->hed()->html() ?></h1>

      <?php if($page->dek()->isNotEmpty()): ?>
        <h2 class="book-subtitle"><?= $page->dek()->html() ?></h2>
      <?php endif ?>

      <?php if($page->authors()->isNotEmpty()): ?>
        <p class="book-authors">
          <?php
          $authors = $page->authors()->toStructure();
          $totalAuthors = $authors->count();
          foreach($authors as $index => $author): ?>
              <span class="author-name">
                  <?php if($author->website()->isNotEmpty()): ?>
                    <a href="<?= $author->website() ?>"><?= $author->name()->html() ?></a>
                  <?php else: ?>
                    <?= $author->name()->html() ?>
                  <?php endif ?>
                  <?php if($index < $totalAuthors - 1): ?>, <?php endif ?>
              </span>
          <?php endforeach ?>
        </p>
      <?php endif ?>
    </div>

    <?php if ($cover = $page->coverImage()): ?>
      <div class="book-cover">
        <img src="<?= $cover->url() ?>" alt="<?= $cover->alt()->esc() ?>">
      </div>
    <?php endif ?>

  <?php if($page->description()->isNotEmpty()): ?>
    <div class="book-description">
      <?= $page->description()->kirbytext() ?>
    </div>
  <?php endif ?>

  <?php if($page->purchaseLink()->isNotEmpty()): ?>
    <div class="book-purchase">
      <a href="<?= $page->purchaseLink() ?>" class="button">Buy this book on Bookshop.org</a>
    </div>
  <?php endif ?>

  <!-- Quotes Section -->
  <?php if($quotes = $page->quotes()): ?>
    <section class="book-quotes">
      <h2>Notable Quotes</h2>

      <?php if($quotes->count()): ?>
        <div class="quotes-list">
          <?php foreach($quotes as $quote): ?>
            <blockquote class="quote">
              <?= $quote->quoteText()->kirbytext() ?>

              <?php if($quote->pageReference()->isNotEmpty()): ?>
                <footer class="quote-source">
                  <cite>p. <?= $quote->pageReference()->html() ?></cite>
                </footer>
              <?php endif ?>
            </blockquote>
          <?php endforeach ?>
        </div>
      <?php else: ?>
        <p>No quotes have been added yet.</p>
      <?php endif ?>
    </section>
  <?php endif ?>

  <div class="book-nav">
    <a href="<?= $site->find('library')->url() ?>" class="back-link">← Back to Library</a>
  </div>
</article>

<?php snippet('footer') ?>
