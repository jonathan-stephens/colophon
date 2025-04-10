<?php snippet('header') ?>

<main id="content" class="review-detail">
  <div class="wrapper">
    <article class="review">
      <header class="">
        <h1 class="p-name"><?= $page->hed()->html() ?></h1>

        <div class="meta">
          <?php if($page->role()->isNotEmpty()): ?>
            <p class="role"><?= $page->role()->html() ?></p>
          <?php endif ?>

          <div class="details">
            <?php if($page->reviewDate()->isNotEmpty()): ?>
              <span class="date">
                <span class="sr-only">Review date:</span>
                <?= $page->reviewDate()->toDate('F Y') ?>
              </span>
            <?php endif ?>

            <?php if($page->relationship()->isNotEmpty()): ?>
              <span class="relationship">
                <span class="sr-only">Relationship:</span>
                <?= $page->relationshipLabel() ?>
              </span>
            <?php endif ?>

            <?php if($page->context()->isNotEmpty()): ?>
              <span class="context">
                <span class="sr-only">Context:</span>
                <?= $page->context() ?>
              </span>
            <?php endif ?>


          </div>
        </div>
      </header>

      <div class="e-content">
        <?php if($image = $page->headshot()->toFile()): ?>
          <div class="profile">
            <img src="<?= $page->headshot()->url() ?>"
                 alt="Photo of <?= $page->hed()->html() ?>"
                 width="<?= $image->resize(400)->width() ?>"
                 height="<?= $image->resize(400)->height() ?>">
          </div>
        <?php endif ?>

        <?php if($page->lede()->isNotEmpty()): ?>
          <div class="lede">
            <p><strong><?= $page->lede()->html() ?></strong></p>
          </div>
        <?php endif ?>

        <?php if($page->review()->isNotEmpty()): ?>
          <div class="prose">
            <?= $page->review()->kt() ?>
          </div>
        <?php endif ?>

      </div>

      <footer class="review-footer">
        <?php snippet('/components/tags', ['reference' => $page]) ?>

        <a href="<?= $page->parent()->url() ?>" class="back-link">
          <span aria-hidden="true">←</span> Back to all reviews
        </a>

      </footer>
    </article>
  </div>
</main>

<?php snippet('footer') ?>
