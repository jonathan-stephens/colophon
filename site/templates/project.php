<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('components/breadcrumb') ?>


    <div class="splash">
    </div>

      <article>
        <h1 class="hed"><?= $page->hed()->kirbytext() ?></h1>
        <div class="meta">
          <p class="dek"><?= $page->dek()->kirbytext() ?></p>
          <p class="role"><?= $page->role()->kirbytext() ?></p>
          <p class="date-start"><?= $page->date_start()->kirbytext() ?></p>
          <p class="date-end"><?= $page->date_end()->kirbytext() ?></p>
          <p class="company"><?= $page->company()->name()->kirbytext() ?></p>
          <p class="perspective"><?= $page->perspective()->kirbytext() ?></p>


        <p class="lede"><?= $page->lede()->kirbytext() ?></p>
        <p class="nutgraf"><?= $page->nutgraf()->kirbytext() ?></p>
        <p class="skills"><?= $page->skills()->kirbytext() ?></p>
        <p class="services"><?= $page->services()->kirbytext() ?></p>
        <p class="tools"><?= $page->tools()->kirbytext() ?></p>
        <div>
          <?= $page->prose()->kirbytext() ?>
        </div>
      </article>

  </section>
<?php snippet('footer') ?>
