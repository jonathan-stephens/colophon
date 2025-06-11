<?php snippet('header') ?>

  <?php if($page->isHomePage()): ?>
    <section class="splash wrapper flow">
      <h2><?= $page->dek()->html() ?></h2>
      <h1><?= $page->hed()->html() ?></h1>
      <?= $page->text()->kirbytext() ?>
      <div class="cluster">
        <a class="button" href="https://linkedin.com/in/elnatnal">
          Connect on LinkedIn
        </a>
        <a class="button" href="https://https://bsky.app/profile/jonathanstephens.us">
          Say hi on Bluesky
        </a>
        <a class="button"  href="https://cal.com/jonathanstephens/book">
          Book time on my calendar
        </a>
      <div>
    </section>
  <?php else: ?>
    <article class="default wrapper">
      <header>
        <h1 class="p-name" itemprop="name headline"><?= $page->hed()->html() ?></h1>
        <p><?= $page->dek()->html() ?><p>
        <?php snippet('/components/on-at-in') ?>
      </header>

      <div class="e-content prose" itemprop="articleBody">
        <?= $page->text()->footnotes() ?>
      </div>

      <footer class="meta">
        <?php snippet('/components/tags', ['reference' => $page]) ?>
      </footer>
    </article>
  <?php endif ?>
<?php snippet('footer') ?>
