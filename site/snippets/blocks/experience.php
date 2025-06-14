<div class="p-experience h-event vevent experience vevent" itemprop="worksFor" itemscope="" itemtype="https://schema.org/Organization https://schema.org/Role">
  <div class="h-card vcard">
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
  </div>
  <div class="time">
    <time class="dt-start dtstart" datetime="<?= $block->startDate() ?>" itemprop="startDate"><?= $block->startDate()->toDate('M Y') ?></time>
    –
    <?php if($block->endDate()->isNotEmpty()): ?>
    <time class="dt-end dtend" datetime="
      <?= $block->endDate() ?>
    " itemprop="endDate"><?= $block->endDate()->toDate('M Y') ?></time>
  <?php else : ?>
    present
  <?php endif ?>
    <span class="p-location location" itemprop="workLocation">
      <?= $block->location() ?> (<?= $block->workModel()->html() ?>)
    </span>
  </div>
  <div class="p-summary summary p-description description" itemprop="description">
    <?php if($block->descriptionListed()->isNotEmpty()): ?>
      <details>
        <summary>
          <?= $block->descriptionSummary() ?>
        </summary>
        <?= $block->descriptionListed() ?>
      </details>
    <?php else : ?>
      <p>
        <?= $block->descriptionSummary() ?>
      </p>
    <?php endif ?>
  </div>
</div>
