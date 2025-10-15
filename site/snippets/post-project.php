<?php $endorsements = $page->endorsements()->toPages(); ?>
<div class="e-content prose" itemprop="articleBody">
  <section class="overview">
    <div class="main-column">
      <?php if($page->overview()->isNotEmpty()): ?>
      <div class="summary">
        <h2>Overview</h2>
        <?= $page->overview()->kt() ?>
      </div>
      <?php endif ?>
      <?= snippet('toc', [
          'exclude' => ['.reviewer'],
          'minLevel' => 2,
          'maxLevel' => 2,
      ]) ?>
    </div>
    <aside>
      <ul>
        <?php if($page->client()->isNotEmpty()): ?>
        <li>
          <span class="label">Client</span>
          <span class="content"><?= $page->client()->html() ?></span>
        </li>
        <?php endif ?>
        <?php if($page->principal()->isNotEmpty()): ?>
        <li>
          <span class="label">Principal</span>
          <span class="content"><?= $page->principal()->html() ?></span>
        </li>
        <?php endif ?>
        <?php if($page->role()->isNotEmpty()): ?>
        <li>
          <span class="label">Role</span>
          <span class="content"><?= $page->role()->html() ?></span>
        </li>
        <?php endif ?>
        <?php if($page->industry()->isNotEmpty()): ?>
        <li>
          <span class="label">Industries</span>
          <span class="content"><?= implode(', ', $page->industry()->split()) ?></span>
        </li>
        <?php endif ?>
        <?php if($page->businessModel()->isNotEmpty() || $page->workingModel()->isNotEmpty() || $page->companySize()->isNotEmpty()): ?>
        <li>
          <span class="label">Organization</span>
          <span class="content">
            <?php
            $orgParts = [];
            if($page->businessModel()->isNotEmpty()) $orgParts[] = $page->businessModel()->html();
            if($page->workingModel()->isNotEmpty()) $orgParts[] = $page->workingModel()->html();
            if($page->companySize()->isNotEmpty()) $orgParts[] = $page->companySize()->html();
            echo implode(', ', $orgParts);
            ?>
          </span>
        </li>
        <?php endif ?>
        <?php if($page->dateFrom()->isNotEmpty()): ?>
        <li>
          <span class="label">Dates</span>
          <span class="content">
            <time class="dt-start dtstart" datetime="<?= $page->dateFrom() ?>" itemprop="startDate">
                <?= $page->dateFrom()->toDate('M Y') ?>
            </time>
            <?php if($page->dateTo()->isNotEmpty()): ?>
                <?php if($page->dateFrom()->toDate('M Y') !== $page->dateTo()->toDate('M Y')): ?>
                    – <time class="dt-end dtend" datetime="<?= $page->dateTo() ?>" itemprop="endDate">
                        <?= $page->dateTo()->toDate('M Y') ?>
                    </time>
                <?php endif ?>
            <?php else : ?>
                – now
            <?php endif ?>
          </span>
        </li>
        <?php endif ?>
        <?php if($page->skills()->isNotEmpty()): ?>
        <li>
          <span class="label">Skills</span>
          <span class="skills">
            <?php $skills = $page->skills()->split(); foreach ($skills as $key => $skill): ?><span rel="tag" class="p-category"><?= $skill ?></span><?php if ($key < count($skills) - 1): ?>, <?php endif ?><?php endforeach ?>
          </span>
        </li>
        <?php endif ?>
      </ul>
    </aside>
  </section>
  <?php if($page->contribution()->isNotEmpty() || $page->aboutClient()->isNotEmpty()): ?>
  <section class="contribution">
    <?php if($page->contribution()->isNotEmpty()): ?>
    <h2>Contribution</h2>
    <?= $page->contribution()->kt() ?>
    <?php endif ?>
    <?php if($page->aboutClient()->isNotEmpty()): ?>
    <h3>Context</h3>
    <?= $page->aboutClient()->kt() ?>
    <?php endif ?>
  </section>
  <?php endif ?>
  <?php if($page->text()->isNotEmpty()): ?>
  <section class="artifacts">
    <?= $page->text()->footnotes() ?>
  </section>
  <?php endif ?>
  <?php if ($endorsements->count() > 0): ?>
  <section class="endorsements">
    <h2>Endorsements</h2>
    <?php snippet('/components/endorsements') ?>
  </section>
  <?php endif ?>
  <?php if ($details = $page->children()->findBy('slug', 'details')): ?>
  <div class="cta flow">
    <h3>Want more details?</h3>
    <a href="<?= $details->url() ?>" class="button case-details">Read the full case study</a>
  </div>
  <?php endif ?>
</div>
