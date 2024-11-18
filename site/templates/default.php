<?php snippet('header') ?>

  <?php if($page->isHomePage()): ?>
    <section class="splash wrapper">
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
    </section>
  <?php else: ?>

    <?= $page->text()->kirbytext() ?>



  <?php endif ?>
<?php snippet('footer') ?>
