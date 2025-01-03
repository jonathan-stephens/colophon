
  </main>

  <footer role="contentinfo">
    <?php $menuItems = $site->footer_nav()->toStructure(); ?>
      <?php if ($menuItems->isNotEmpty()): ?>
      <nav role="navigation" class="wrapper">
        <ul>
          <li>Navigation</li>
          <?php foreach ($menuItems as $menuItem): ?>
            <li><a <?= ($p = $menuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $menuItem->link()->toUrl() ?>"><?= $menuItem->linkTitle()->or($menuItem->link()->html()) ?></a></li>
          <?php endforeach ?>
        </ul>
      </nav>
    <?php endif ?>

    <?php
    // Using footer_social instead of social_links
    $socialLinks = $site->footer_social()->toStructure();
    ?><div class="wrapper">
      <div class="social-links">
        <ul role="list" class="cluster">
          <li>Elsewhere</li>
        <?php foreach($socialLinks as $link): ?>
<li>
  <a rel="me" href="<?= $link->url() ?>" title="<?= $link->platform() ?>" class=""><?php if($link->icon()->isNotEmpty() && $link->icon()-toFile()): ?><img src="<?= $link->icon()->toFile()->url() ?>" alt="<?= $link->platform() ?> icon" class=""><?php else: ?><?= $link->platform() ?>
<?php endif ?></a>
</li>
        <?php endforeach ?>

      </div>

      <div class="final-info">
        <?php snippet('components/last-updated') ?>
        <?php snippet('copyright') ?>
      </div>
    </div>
  </footer>

<!-- Fathom - beautiful, simple website analytics -->
<script src="https://cdn.usefathom.com/script.js" data-site="FCIAGYSD" defer></script>
<!-- / Fathom -->

<?= js([
  'assets/js/theme-picker.js',
]) ?>


</body>
</html>
