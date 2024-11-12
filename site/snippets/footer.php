
</main>

<footer>
  <?php
  // Using footer_social instead of social_links
  $socialLinks = $site->footer_social()->toStructure();
  ?>

<div class="wrapper">
      <div class="social-links cluster">
          <?php foreach($socialLinks as $link): ?>
              <a href="<?= $link->url() ?>"
                 title="<?= $link->platform() ?>"
                 class=""
                 rel="me">
                  <?php if($link->icon()->isNotEmpty() && $link->icon()->toFile()): ?>
                      <img src="<?= $link->icon()->toFile()->url() ?>"
                           alt="<?= $link->platform() ?> icon"
                           class="w-6 h-6">
                  <?php else: ?>
                      <?= $link->platform() ?>
                  <?php endif ?>
              </a>
          <?php endforeach ?>
      </div>

  <?php snippet('components/theme-picker') ?>

  <p class="h-card">
    &copy; 2008&thinsp;&ndash;&thinsp;2024
      <a class="p-name u-url" href="https://jonathanstephens.us">Jonathan Stephens</a>.
  </p>
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
