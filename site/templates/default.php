<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('breadcrumb') ?>

    <div class="splash">
      <?= $page->text()->kirbytext() ?>
    </div>
  </section>
<?php snippet('footer') ?>
