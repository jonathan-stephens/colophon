<?php snippet('site-header') ?>

  <div class="wrapper case-studies">
    <?php foreach($page->children()->listed()->flip() as $article): ?>
      <a href="<?= $article->url() ?>" class="case-study">
        <article class="h-entry">
          <p>
            <span class="client"><?= $article->client()->html() ?></span> •
            <span class="context"><?= $article->context()->html() ?></span>
          </p>

          <h2 class="p-name hed"><?= $article->hed()->html() ?></h2>
          <p class="dek"><?= $article->dek()->html() ?></p>

            <ul class="meta">
              <li class="role">
                <span>Role</span>
                <span><?= $article->role()->html() ?></span>
              </li>
              <li class="industries">
                <span>Industries</span>
                <span><?php foreach ($article->industry()->split() as $industry): ?>
                    <span><?= $industry ?></span>
                <?php endforeach ?>
              </li>
              <li class="organization">
                <span>Organization</span>
                <span>
                  <span class="business-model"><?= $article->businessModel()->html() ?></span>
                  <span class="working-model"><?= $article->workingModel()->html() ?></span>
                  <span class="company-size"><?= $article->companySize()->html() ?></span>
                </span>
              </li>
            </ul>
        </article>
      </a>
    <?php endforeach ?>
  </div>
<?php snippet('site-footer') ?>
