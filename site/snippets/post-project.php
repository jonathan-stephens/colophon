<?php $endorsements = $page->endorsements()->toPages(); ?>

<div class="e-content prose" itemprop="articleBody">
  <section class="overview">
    <div class="main-column">
      <div class="summary">
        <h2>Overview</h2>
        <?= $page->overview()->kt() ?>
      </div>
      <?= snippet('toc', [
          'exclude' => ['.reviewer'],
          'minLevel' => 2,
          'maxLevel' => 2,
      ]) ?>
    </div>
    <aside>
      <ul>
        <?php if(!$page->client()->isEmpty()): ?>
        <li>
          <span class="label">Client</span>
          <span class="content"><?= $page->client()->html() ?></span>
        </li>
        <?php endif ?>
        <?php if(!$page->principal()->isEmpty()): ?>
        <li>
          <span class="label">Principal</span>
          <span class="content"><?= $page->principal()->html() ?></span>
        </li>
        <?php endif ?>
        <?php if(!$page->role()->isEmpty()): ?>
        <li>
          <span class="label">Role</span>
          <span class="content"><?= $page->role()->html() ?></span>
        </li>
        <?php endif ?>
        <?php if(!$page->industry()->isEmpty()): ?>
        <li>
          <span class="label">Industries</span>
          <span class="content"><?= implode(', ', $page->industry()->split()) ?></span>
        </li>
        <?php endif ?>
        <li>
          <span class="label">Organization</span>
          <span class="content"><?= $page->businessModel()->html() ?>, <?= $page->workingModel()->html() ?>, <?= $page->companySize()->html() ?></span>
        </li>

        <?php if(!$page->dateFrom()->isEmpty()): ?>
        <li>
          <span class="label">Dates</span>
          <span class="content">
            <time class="dt-start dtstart" datetime="<?= $page->dateFrom() ?>" itemprop="startDate">
                <?= $page->dateFrom()->toDate('M Y') ?>
            </time>
            <?php if($page->dateTo()->isNotEmpty()): ?>
                <?php if($page->dateFrom()->toDate('M Y') !== $page->dateTo()->toDate('M Y')): ?>
                    – <time class="dt-end dtend" datetime="<?= $page->dateTo() ?>" itemprop="endDate">
                        <?= $page->dateTo()->toDate('M Y') ?>
                    </time>
                <?php endif ?>
            <?php else : ?>
                – now
            <?php endif ?>          </span>
        </li>
        <?php endif ?>
        <?php if(!$page->skills()->isEmpty()): ?>
        <li>
          <span class="label">Skills</span>
          <span class="skills">
            <?php  $skills = $page->skills()->split(); foreach ($skills as $key => $skill): ?><span rel="tag" class="p-category"><?= $skill ?></span><?php if ($key < count($skills) - 1): ?>, <?php endif ?><?php endforeach ?>
          </span>
        </li>
      <?php endif ?>
    </ul>
    </aside>
  </section>

  <section class="contribution">
    <h2>Contribution</h3>
    <?= $page->contribution()->kt() ?>
    <h3>Context</h3>
    <?= $page->aboutClient()->kt() ?>
  </section>


  <section class="artifacts">
    <?= $page->text()->footnotes() ?>
  </section>


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
