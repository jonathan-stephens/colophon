<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('components/breadcrumb') ?>

    <div class="splash">
      <?= $page->text()->kirbytext() ?>
      <div class="cluster">
        <a class="button" href="https://linkedin.com/in/elnatnal">
          Connect on LinkedIn
        </a>
        <a class="button" href="https://jonathanstephens.substack.com/">
          Subscribe on Substack
        </a>
        <a class="button"  href="https://cal.com/jonathanstephens/book">
          Book time on my calendar
        </a>
      <div>
    </div>
  </section>
<?php snippet('footer') ?>
