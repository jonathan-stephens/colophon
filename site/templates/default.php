<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('components/breadcrumb') ?>

    <div class="splash">
      <?= $page->text()->kirbytext() ?>
    </div>
  </section>
<?php snippet('footer') ?>
