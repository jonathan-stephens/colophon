<div class="e-content prose" itemprop="articleBody">
  <section class="overview">
    <div class="summary">
      <h2>Overview</h2>
      <p><?= $page->overview()->html() ?></p>
    </div>
    <div class="contribution">
      <h3>Contribution</h3>
      <p><?= $page->contribution()->html() ?></p>
    </div>
    <div class="client">
      <h3>Client</h3>
      <p><?= $page->aboutClient()->html() ?></p>
    </div>
    <aside>
      <ul>
        <?php if(!$page->role()->isEmpty()): ?>
        <li>
          <span class="label">Role</span>
          <span class="content"><?= $page->role()->html() ?></span>
        </li>
        <?php endif ?>
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
        <?php if(!$page->stakeholders()->isEmpty()): ?>
        <li>
          <span class="label">Key Stakeholders</span>
          <span class="content"><?= $page->stakeholders()->html() ?></span>
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

  <section>
    <div class="objectives">
      <h2>Objectives</h2>
      <p>
        <?= $page->stakeholders()->html() ?>
      </p>
    </div>
    <div class="results">
      <h2>Results</h2>
      <p>
        <?= $page->stakeholders()->html() ?>
      </p>
    </div>
  </section>

<?php if ($details = $page->children()->findBy('slug', 'details')): ?>
  <div class="cta flow">
    <h3>Read the full case study</h3>
    <p>I have a full case study of this, if you'd like, with more details and such. But, you gotta ask for it.</p>
    <a href="<?= $details->url() ?>" class="button case-details">Read the full case study</a>
  </div>
<?php endif ?>

  <section class="artifacts">
    <?= $page->text()->footnotes() ?>
  </section>
</div>
