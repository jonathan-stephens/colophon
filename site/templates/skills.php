<?php snippet('site-header') ?>

<div class="skills-page">
  <h1>Save Bookmark</h1>

  <article class="h-resume" itemscope itemtype="https://schema.org/ItemList">

    <?php if ($skills = $page->skills()->toStructure()): ?>
      <div class="skills-container" itemprop="itemListElement">

        <?php foreach ($skills as $index => $skill): ?>

          <!-- Level 1: Open by default -->
          <details class="skill-level-1 skill p-skill" open
                   itemscope itemtype="https://schema.org/ListItem"
                   itemprop="itemListElement">
            <meta itemprop="position" content="<?= $index + 1 ?>">

            <summary class="skill-name" itemprop="name">
              <span class="skill-title"><?= $skill->name() ?></span>
            </summary>

            <div class="skill-content" itemprop="description">
              <?php if ($skill->description()->isNotEmpty()): ?>
                <p class="skill-description"><?= $skill->description()->kt() ?></p>
              <?php endif ?>

              <?php if ($level2Items = $skill->level2()->toStructure()): ?>
                <div class="skills-level-2" itemscope itemtype="https://schema.org/ItemList">

                  <?php foreach ($level2Items as $i2 => $skill2): ?>

                    <!-- Level 2 -->
                    <details class="skill-level-2 skill p-skill"
                             itemscope itemtype="https://schema.org/ListItem"
                             itemprop="itemListElement">
                      <meta itemprop="position" content="<?= $i2 + 1 ?>">

                      <summary class="skill-name" itemprop="name">
                        <span class="skill-title"><?= $skill2->name() ?></span>
                      </summary>

                      <div class="skill-content" itemprop="description">
                        <?php if ($skill2->description()->isNotEmpty()): ?>
                          <p class="skill-description"><?= $skill2->description()->kt() ?></p>
                        <?php endif ?>

                        <?php if ($level3Items = $skill2->level3()->toStructure()): ?>
                          <div class="skills-level-3" itemscope itemtype="https://schema.org/ItemList">

                            <?php foreach ($level3Items as $i3 => $skill3): ?>

                              <!-- Level 3 -->
                              <details class="skill-level-3 skill p-skill"
                                       itemscope itemtype="https://schema.org/ListItem"
                                       itemprop="itemListElement">
                                <meta itemprop="position" content="<?= $i3 + 1 ?>">

                                <summary class="skill-name" itemprop="name">
                                  <span class="skill-title"><?= $skill3->name() ?></span>
                                </summary>

                                <div class="skill-content" itemprop="description">
                                  <?php if ($skill3->description()->isNotEmpty()): ?>
                                    <p class="skill-description"><?= $skill3->description()->kt() ?></p>
                                  <?php endif ?>

                                  <?php if ($level4Items = $skill3->level4()->toStructure()): ?>
                                    <div class="skills-level-4" itemscope itemtype="https://schema.org/ItemList">

                                      <?php foreach ($level4Items as $i4 => $skill4): ?>

                                        <!-- Level 4 -->
                                        <details class="skill-level-4 skill p-skill"
                                                 itemscope itemtype="https://schema.org/ListItem"
                                                 itemprop="itemListElement">
                                          <meta itemprop="position" content="<?= $i4 + 1 ?>">

                                          <summary class="skill-name" itemprop="name">
                                            <span class="skill-title"><?= $skill4->name() ?></span>
                                          </summary>

                                          <div class="skill-content" itemprop="description">
                                            <?php if ($skill4->description()->isNotEmpty()): ?>
                                              <p class="skill-description"><?= $skill4->description()->kt() ?></p>
                                            <?php endif ?>
                                          </div>
                                        </details>

                                      <?php endforeach ?>
                                    </div>
                                  <?php endif ?>

                                </div>
                              </details>

                            <?php endforeach ?>
                          </div>
                        <?php endif ?>

                      </div>
                    </details>

                  <?php endforeach ?>
                </div>
              <?php endif ?>

            </div>
          </details>

        <?php endforeach ?>

      </div>
    <?php endif ?>

  </article>
</div>

<?php snippet('site-footer') ?>
