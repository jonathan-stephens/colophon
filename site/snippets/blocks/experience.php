<div class="p-experience h-event vevent gig vevent flow" itemprop="worksFor" itemscope="" itemtype="https://schema.org/Organization https://schema.org/Role">
  <header class="h-card vcard">
    <h3>
      <span class="p-job-title" itemprop="roleName">
        <?= $block->title() ?>
      </span>
      at
      <a class="p-name org u-url url"
      href="
      <?= $block->organizationUrl() ?>
      " itemprop="url">
        <span itemprop="name">
          <?= $block->organization() ?>
        </span>
      </a>
    </h3>
  </header>
  <div class="p-summary summary p-description description" itemprop="description">
    <?= $block->descriptionSummary()->kt() ?>
    <?php
/*
    if($block->descriptionListed()->isNotEmpty()): ?>
      <details>
        <summary>
          <?php if($block->descriptionSummary()->isNotEmpty()): ?>
            <?= $block->descriptionSummary() ?>
          <?php else : ?>
            View responsibilities
          <?php endif ?>
        </summary>
        <?= $block->descriptionListed() ?>
      </details>
    <?php else : ?>
      <p>
        <?= $block->descriptionSummary() ?>
      </p>
    <?php endif */ ?>
  </div>
  <div class="meta">
    <div class="time with-icon">
      <?= asset('assets/svg/icons/date.svg')->read() ?>
      <time class="dt-start dtstart" datetime="<?= $block->startDate() ?>" itemprop="startDate"><?= $block->startDate()->toDate('M Y') ?></time> – <?php if($block->endDate()->isNotEmpty()): ?><time class="dt-end dtend" datetime="<?= $block->endDate() ?>" itemprop="endDate"><?= $block->endDate()->toDate('M Y') ?></time><?php else : ?>present<?php endif ?>
    </div>
    <div class="p-location location with-icon" itemprop="workLocation">
      <?= asset('assets/svg/icons/location.svg')->read() ?>
      <?= $block->location() ?>
    </div>
    <div class="model with-icon">
      <?= asset('assets/svg/icons/compass.svg')->read() ?>
      <?= $block->workModel()->html() ?>
    </div>
  </div>

</div>
