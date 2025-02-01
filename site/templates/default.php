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
    <article class="default wrapper">
      <header>
        <h1 class="p-name" itemprop="name headline"><?= $page->title() ?></h1>
      </header>

      <div class="e-content prose" itemprop="articleBody">
        <?= $page->text()->footnotes() ?>
      </div>
    </article>
  <?php endif ?>
<?php snippet('footer') ?>
