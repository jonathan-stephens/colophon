<?php snippet('site-header') ?>
  <div class="wrapper flow">
    <header class="page-header">
      <h1><?= $page->title() ?></h1>
      <?php if($page->intro()->isNotEmpty()): ?>
        <div class="intro">
          <?= $page->intro()->kt() ?>
        </div>
      <?php endif ?>
    </header>

    <?php if($reviews = $page->children()->listed()): ?>
        <?php foreach($reviews as $review): ?>
          <article class="review">
            <?= $review->headshot() ?>
            <div class="content">
              <?= $review->paraphrase()->kt() ?>
              <div class="byline">
                <h2 class="reviewer"><?= $review->hed()->html() ?></h2>
                <p>
                  <?php if($review->role()->isNotEmpty()): ?>
                    <span class="role"><?= $review->role()->html() ?>; </span>
                  <?php endif ?>
                  <?php if($review->context()->isNotEmpty()): ?>
                    <span class="context"><?= $review->context()->html() ?></span>
                  <?php endif ?>
                  <?php if($review->relationship()->isNotEmpty()): ?>
                    <span class="relationship">| <?= $review->relationshipLabel() ?></span>
                  <?php endif ?>
                </p>
              </div>
            </div>
          </article>
        <?php endforeach ?>
    <?php else: ?>
      <p class="no-reviews">No reviews have been added yet.</p>
    <?php endif ?>
  </div>

<?php snippet('site-footer') ?>
