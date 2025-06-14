<?php snippet('site-header') ?>
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
      <div class="cluster">
        <?php foreach($reviews as $review): ?>
          <article class="review">
            <a href="<?= $review->url() ?>" class="review-link">
              <box-l>
                <p class="meta">
                  <?php if($review->relationship()->isNotEmpty()): ?>
                    <span class="relationship"><?= $review->relationshipLabel() ?></span>
                  <?php endif ?>
                  <?php if($review->reviewDate()->isNotEmpty()): ?>
                    <span class="role"><?= $review->reviewDate()->toDate('F Y') ?> </span>
                  <?php endif ?>
                </p>

                  <h2 class="reviewer"><?= $review->hed()->html() ?></h2>
                  <p>
                    <?php if($review->role()->isNotEmpty()): ?>
                      <span class="role"><?= $review->role()->html() ?>, </span>
                    <?php endif ?>
                    <?php if($review->context()->isNotEmpty()): ?>
                      <span class="context"><?= $review->context()->html() ?></span>
                    <?php endif ?>
                  </p>
              </box-l>
            </a>
          </article>
        <?php endforeach ?>
      </div>
    <?php else: ?>
      <p class="no-reviews">No reviews have been added yet.</p>
    <?php endif ?>
  </div>

<?php snippet('site-footer') ?>
