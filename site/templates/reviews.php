<?php snippet('header') ?>
  <div class="wrapper">
    <header class="page-header">
      <h1><?= $page->title() ?></h1>
      <?php if($page->intro()->isNotEmpty()): ?>
        <div class="intro">
          <?= $page->intro()->kt() ?>
        </div>
      <?php endif ?>
    </header>

    <?php if($reviews = $page->children()->listed()): ?>
      <div class="reviews-grid">
        <?php foreach($reviews as $review): ?>
          <article class="review">
            <a href="<?= $review->url() ?>" class="review-link">
              <?php if($image = $review->headshot()->toFile()): ?>
                <div class="review-image">

                  <img src="<?= $review->headshot()->url() ?>"
                       alt="Photo of <?= $review->hed()->html() ?>"
                       width="<?= $review->headshot()->width() ?>"                    
                       loading="lazy">
                </div>
              <?php endif ?>

              <div>
                <h2 class="reviewer"><?= $review->hed()->html() ?></h2>

                <?php if($review->role()->isNotEmpty()): ?>
                  <p class="role"><?= $review->role()->html() ?></p>
                <?php endif ?>

                <?php if($review->lede()->isNotEmpty()): ?>
                  <p class="lede"><?= $review->lede()->html() ?></p>
                <?php endif ?>

                <div class="meta">
                  <span class="date"><?= $review->reviewDate()->toDate('F Y') ?></span>
                  <?php if($review->relationship()->isNotEmpty()): ?>
                    <span class="relationship"><?= $review->relationshipLabel() ?></span>
                  <?php endif ?>
                </div>
              </div>
            </a>
          </article>
        <?php endforeach ?>
      </div>
    <?php else: ?>
      <p class="no-reviews">No reviews have been added yet.</p>
    <?php endif ?>
  </div>

<?php snippet('footer') ?>
