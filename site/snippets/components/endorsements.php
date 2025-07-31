<?php
    $endorsements = $page->endorsements()->toPages();
  ?>
  <?php if ($endorsements->count() > 0): ?>
    <div class="inner">
    <?php foreach ($endorsements as $endorsement): ?>
        <article class="endorsement card">
          <div class="content">
            <?= $endorsement->lede()->kt() ?>
            <div class="byline">
              <h3 class="reviewer"><?= $endorsement->hed()->html() ?></h3>
              <p>
                <?php if($endorsement->role()->isNotEmpty()): ?>
                  <span class="role"><?= $endorsement->role()->html() ?> </span>
                <?php endif ?>
                <?php if($endorsement->relationship()->isNotEmpty()): ?>
                  <span class="relationship">| <?= $endorsement->relationshipLabel() ?></span>
                <?php endif ?>
              </p>
            </div>
          </div>
        </article>
        <?php endforeach ?>
      </div>
  <?php endif ?>
