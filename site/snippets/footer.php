
  </main>

  <footer>
    <?php snippet('timekeeper/theme-switcher') ?>

    <?php
    // Using footer_social instead of social_links
    $socialLinks = $site->footer_social()->toStructure();
    ?><div class="wrapper">
      <div class="social-links">
        <ul role="list" class="cluster">
        <?php foreach($socialLinks as $link): ?>
<li>
  <a rel="me" href="<?= $link->url() ?>" title="<?= $link->platform() ?>" class=""> <?php if($link->icon()->isNotEmpty() && $link->icon()-toFile()): ?><img src="<?= $link->icon()->toFile()->url() ?>" alt="<?= $link->platform() ?> icon" class=""><?php else: ?><?= $link->platform() ?>
<?php endif ?></a>
</li>
        <?php endforeach ?>

      </div>

      <?php snippet('components/theme-picker') ?>

      <div class="final-info">
        <?php snippet('components/last-updated') ?>

        <p class="h-card">
          2008&thinsp;&ndash;&thinsp;2024
            <a class="p-name u-url" href="https://jonathanstephens.us">Jonathan Stephens</a> | <a class="u-email" href="mailto://hello@jonathanstephens.us">hello@jonathanstephens.us</a>
        </p>

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
