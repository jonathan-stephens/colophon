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
                <p class="domain-description"><?= $skill->description()->html() ?></p>
              <?php endif ?>
            </div>

            <!-- Level 2 -->
            <?php
            $level2Items = $skill->level2()->toStructure();
            if ($level2Items && $level2Items->isNotEmpty()):
            ?>
              <div class="areas flow" itemprop="description" itemscope itemtype="https://schema.org/ItemList">
                <?php foreach ($level2Items as $i2 => $skill2): ?>
                  <article class="area flow skill p-skill"
                      itemscope itemtype="https://schema.org/ListItem"
                      itemprop="itemListElement">
                    <meta itemprop="position" content="<?= $i2 + 1 ?>">

                    <h3 class="area-title" itemprop="name"><?= $skill2->name() ?></h3>
                    <?php if ($skill2->description()->isNotEmpty()): ?>
                      <p class="area-description" itemprop="description"><?= $skill2->description()->html() ?></p>
                    <?php endif ?>

                    <!-- Level 3 -->
                    <?php
                    $level3Items = $skill2->level3()->toStructure();
                    if ($level3Items && $level3Items->isNotEmpty()):
                    ?>
                    <div class="breakout">
                      <ul class="competencies overflow overflow-grid" itemscope itemtype="https://schema.org/ItemList" style="--count:<?php echo $level3Items->count(); ?>">
                        <?php foreach ($level3Items as $i3 => $skill3): ?>
                          <li class="competency skill p-skill card"
                                   itemscope itemtype="https://schema.org/ListItem"
                                   itemprop="itemListElement">
                            <meta itemprop="position" content="<?= $i3 + 1 ?>">

                            <h4 class="competency-name" itemprop="name">
                              <?= $skill3->name() ?>
                            </h4>

                            <?php if ($skill3->description()->isNotEmpty()): ?>
                              <div class="competency-content" itemprop="description">
                                  <p class="competency-description"><?= $skill3->description()->html() ?></p>
                            <?php endif ?>

                              <!-- Level 4 -->
                              <?php
                              $level4Items = $skill3->level4()->toStructure();
                              if ($level4Items && $level4Items->isNotEmpty()):
                              ?>
                                <h5 class="behaviors-title">Behaviors</h4>
                                <div class="behavior" itemscope itemtype="https://schema.org/ItemList">
                                    <?php foreach ($level4Items as $i4 => $skill4): ?>
                                      <details class="behavior skill p-skill"
                                          itemscope itemtype="https://schema.org/ListItem"
                                          itemprop="itemListElement">
                                        <meta itemprop="position" content="<?= $i4 + 1 ?>">

                                        <summary class="behavior-name" itemprop="name">
                                          <?= $skill4->name() ?>
                                        </summary>
                                        <?php if ($skill4->description()->isNotEmpty()): ?>
                                          <span class="behavior-description" itemprop="description">
                                            <?= $skill4->description()->html() ?>
                                          </span>
                                        <?php endif ?>
                                      </details>
                                    <?php endforeach ?>
                                </div>
                              <?php endif ?>
                            </div>
                          </details>
                        </li>
                        <?php endforeach ?>

                      </ul>
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
