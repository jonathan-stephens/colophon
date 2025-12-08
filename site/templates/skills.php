<?php snippet('site-header') ?>

<div class="skills-page wrapper">
  <h1><?= $page->title()->html() ?></h1>

  <article class="h-resume" itemscope itemtype="https://schema.org/ItemList">
    <?php
    $skills = $page->skills()->toStructure();
    if ($skills && $skills->isNotEmpty()):
    ?>
      <div class="skills-container" itemprop="itemListElement">
        <?php foreach ($skills as $index => $skill): ?>
          <!-- Level 1 -->
          <section class="domain skill p-skill"
                   itemscope itemtype="https://schema.org/ListItem"
                   itemprop="itemListElement">
            <meta itemprop="position" content="<?= $index + 1 ?>">
            <div class="domain-header">
              <h2 class="domain-title" itemprop="name"><?= $skill->name() ?></h2>
              <?php if ($skill->description()->isNotEmpty()): ?>
                <p class="domain-description"><?= $skill->description()->kt() ?></p>
              <?php endif ?>
            </div>

            <!-- Level 2 -->
            <?php
            $level2Items = $skill->level2()->toStructure();
            if ($level2Items && $level2Items->isNotEmpty()):
            ?>
              <div class="areas" itemprop="description" itemscope itemtype="https://schema.org/ItemList">
                <?php foreach ($level2Items as $i2 => $skill2): ?>
                  <article class="area skill p-skill"
                      itemscope itemtype="https://schema.org/ListItem"
                      itemprop="itemListElement">
                    <meta itemprop="position" content="<?= $i2 + 1 ?>">

                    <h3 class="area-title" itemprop="name"><?= $skill2->name() ?></h3>
                    <?php if ($skill2->description()->isNotEmpty()): ?>
                      <p class="area-description" itemprop="description"><?= $skill2->description()->kt() ?></p>
                    <?php endif ?>

                    <!-- Level 3 -->
                    <?php
                    $level3Items = $skill2->level3()->toStructure();
                    if ($level3Items && $level3Items->isNotEmpty()):
                    ?>
                      <div class="competencies" itemscope itemtype="https://schema.org/ItemList">
                        <?php foreach ($level3Items as $i3 => $skill3): ?>
                          <details class="competency skill p-skill"
                                   itemscope itemtype="https://schema.org/ListItem"
                                   itemprop="itemListElement">
                            <meta itemprop="position" content="<?= $i3 + 1 ?>">

                            <summary class="competency-name" itemprop="name">
                              <span class="competency-title"><?= $skill3->name() ?></span>
                            </summary>

                            <div class="competency-content" itemprop="description">
                              <?php if ($skill3->description()->isNotEmpty()): ?>
                                <p class="competency-description"><?= $skill3->description()->kt() ?></p>
                              <?php endif ?>

                              <!-- Level 4 -->
                              <?php
                              $level4Items = $skill3->level4()->toStructure();
                              if ($level4Items && $level4Items->isNotEmpty()):
                              ?>
                                <h4 class="behaviors-title">Behaviors</h4>
                                <div class="behavior" itemscope itemtype="https://schema.org/ItemList">
                                  <ul>
                                    <?php foreach ($level4Items as $i4 => $skill4): ?>
                                      <li class="behavior skill p-skill"
                                          itemscope itemtype="https://schema.org/ListItem"
                                          itemprop="itemListElement">
                                        <meta itemprop="position" content="<?= $i4 + 1 ?>">

                                        <strong class="behavior-name" itemprop="name">
                                          <?= $skill4->name() ?>
                                        </strong>
                                        <?php if ($skill4->description()->isNotEmpty()): ?>
                                          <span class="behavior-description" itemprop="description">
                                            <?= $skill4->description()->kt() ?>
                                          </span>
                                        <?php endif ?>
                                      </li>
                                    <?php endforeach ?>
                                  </ul>
                                </div>
                              <?php endif ?>
                            </div>
                          </details>
                        <?php endforeach ?>
                      </div>
                    <?php endif ?>

                  </article>
                <?php endforeach ?>
              </div>
            <?php endif ?>
          </section>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </article>
</div>

<?php snippet('site-footer') ?>
