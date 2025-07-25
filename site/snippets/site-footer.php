  <a href="https://brid.gy/publish/bluesky"></a>
  </main>

  <footer role="contentinfo">

    <?php $menuItems = $site->footer_nav()->toStructure(); ?>
      <?php if ($menuItems->isNotEmpty()): ?>
      <section class="navigation">
        <div class="wrapper">
          <h2>Navigation</h2>
          <nav>
            <ul>
              <?php foreach ($menuItems as $menuItem): ?>
                <li><a <?= ($p = $menuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $menuItem->link()->toUrl() ?>"><?= $menuItem->title()->or($menuItem->link()->html()) ?></a></li>
              <?php endforeach ?>
            </ul>
          </nav>
        </div>
      </section>
    <?php endif ?>

    <section class="subscribe">
      <div class="wrapper">
        <div class="newsletter">
          <div class="description">
            <span class="with-icon">
              <?= asset('assets/svg/icons/bullhorn.svg')->read() ?>
              <h2 id="newsletter">
                Join my mailing list
              </h2>
            </span>

            <p>Every week or few, I send out an email newsletter with links and resources gathered in my internet wanderings—from my own work and by other humans on Earth.</p>
            <p>Subscribe to my newsletter: <em>Craft & Practice</em></p>
          </div>

          <form
            action="https://buttondown.com/api/emails/embed-subscribe/jonathanstephens"
            method="post"
            target="popupwindow"
            onsubmit="window.open('https://buttondown.com/jonathanstephens', 'popupwindow')"
            class="embeddable-buttondown-form"
          >
            <input type="email" name="email" id="bd-email" aria-label="Email Address" placeholder="Email Address" />

            <button type="submit" data-element="submit" class="button">
              <span>Submit</span>
            </button>
          </form>
        </div>
        <div class="feeds">
          <?php $feedItems = $site->footer_feeds()->toStructure(); ?>
          <?php if ($feedItems->isNotEmpty()): ?>
            <div class="description">
              <span class="with-icon">
                <?= asset('assets/svg/icons/rss.svg')->read() ?>
                <h2>Feeds</h2>
              </span>
              <p>Get my latest content in your favorite RSS reader. <a href="https://aboutfeeds.com/">What is RSS?</a></p>
            </div>
            <ul role="list">
              <?php foreach ($feedItems as $feedItem): ?>
                <li>
                  <strong><?= $feedItem->title() ?>:</strong>
                  <span> <?= $feedItem->description() ?></span>
                  <a href="<?= $feedItem->flavorRSS()->toUrl() ?>">RSS</a> |
                  <a href="<?= $feedItem->flavorJSON()->toUrl() ?>">JSON</a>
                </li>
              <?php endforeach ?>
            </ul>
          <?php endif ?>
        </div>
      </div>
    </section>

    <?php $socialLinks = $site->footer_social()->toStructure(); ?>
    <?php if ($socialLinks->isNotEmpty()): ?>
      <section class="socials">
          <div class="wrapper">
              <h2>Elsewhere</h2>
              <ul role="list">
                <?php foreach($socialLinks as $link): ?>
                  <li><a rel="me" href="<?= $link->url() ?>" title="<?= $link->platform() ?>" class=""><?php if($link->icon()->isNotEmpty() && $link->icon()-toFile()): ?><img src="<?= $link->icon()->toFile()->url() ?>" alt="<?= $link->platform() ?> icon" class=""><?php else: ?><?= $link->platform() ?><?php endif ?></a></li>
                <?php endforeach ?>
              </ul>
            </div>
      </section>
    <?php endif ?>

    <section class="final-info">
      <div class="wrapper">
        <?php snippet('components/addenda') ?>
        <div class="carbon">
            <?= asset('assets/svg/icons/sprout.svg')->read() ?>
            This site is <a href="https://www.websitecarbon.com/website/jonathanstephens-us/">climate-friendly</a>, cleaner than 97% of pages tested at 0.02g  CO<sub>2</sub>/view.
        </div>
        <?php snippet('components/last-updated') ?>
        <?php snippet('copyright') ?>
      </div>
    </section>

  </footer>

<!-- Fathom - beautiful, simple website analytics -->
<script src="https://cdn.usefathom.com/script.js" data-site="FCIAGYSD" defer></script>
<!-- / Fathom -->

<?= js([
  'assets/js/theme-picker.js',
  'assets/js/prism.js',
  'assets/js/header.js'
]) ?>


</body>
</html>
