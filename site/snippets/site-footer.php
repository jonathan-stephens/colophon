  <a href="https://brid.gy/publish/bluesky"></a>
  </main>

  <footer role="contentinfo">
    <section class="navigation">
        <div class="wrapper">
          <?php $aboutMenuItems = $site->footerAbout_nav()->toStructure(); ?>
          <?php $gardenMenuItems = $site->footerGarden_nav()->toStructure(); ?>
          <?php $soilMenuItems = $site->footerSoil_nav()->toStructure(); ?>
          <?php $workMenuItems = $site->footerWork_nav()->toStructure(); ?>

          <?php if ($aboutMenuItems->isNotEmpty()): ?>
            <div class="about">
              <h2>About</h2>
              <nav>
                <ul>
                  <?php foreach ($aboutMenuItems as $amenuItem): ?>
                    <li><a <?= ($p = $amenuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $amenuItem->link()->toUrl() ?>"><?= $amenuItem->title()->or($amenuItem->link()->html()) ?></a></li>
                  <?php endforeach ?>
                </ul>
              </nav>
            </div>
          <?php endif ?>
          <?php if ($workMenuItems->isNotEmpty()): ?>
            <div class="labor">
              <h2>Labor</h2>
              <nav>
                <ul>
                  <?php foreach ($workMenuItems as $wmenuItem): ?>
                    <li><a <?= ($p = $wmenuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $wmenuItem->link()->toUrl() ?>"><?= $wmenuItem->title()->or($wmenuItem->link()->html()) ?></a></li>
                  <?php endforeach ?>
                </ul>
              </nav>
            </div>
          <?php endif ?>
          <?php if ($gardenMenuItems->isNotEmpty()): ?>
              <div class="garden">
                <h2>Garden</h2>
                <nav>
                  <ul>
                    <?php foreach ($gardenMenuItems as $gmenuItem): ?>
                      <li><a <?= ($p = $gmenuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $gmenuItem->link()->toUrl() ?>"><?= $gmenuItem->title()->or($gmenuItem->link()->html()) ?></a></li>
                    <?php endforeach ?>
                  </ul>
                </nav>
              </div>
            <?php endif ?>
            <?php if ($soilMenuItems->isNotEmpty()): ?>
              <div class="soil">
                <h2>Soil</h2>
                <nav>
                  <ul>
                    <?php foreach ($soilMenuItems as $smenuItem): ?>
                      <li><a <?= ($p = $smenuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $smenuItem->link()->toUrl() ?>"><?= $smenuItem->title()->or($smenuItem->link()->html()) ?></a></li>
                    <?php endforeach ?>
                  </ul>
                </nav>
              </div>
            <?php endif ?>
          </div>
      </section>
    <section class="subscribe">
      <div class="wrapper">
        <div class="newsletter">
          <header>
            <span class="with-icon">
              <?= asset('assets/svg/icons/bullhorn.svg')->read() ?>
              <h2 id="newsletter">
                Newsletter: <em>Craft & Practice</em>
              </h2>
            </span>
          </header>
          <div class="description">
            <p>Every fortnight or few, I send out an email newsletter with links and resources gathered in my internet wanderings—from my own work and by other humans on Earth.</p>
            <p>Feel free to <a href="https://buttondown.com/jonathanstephens/archive/">browse the archives</a>...before subscribing ( • ᴗ - ).</p>
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
              <span>Subscribe to my newsletter</span>
            </button>
          </form>
        </div>
        <div class="feeds">
          <?php $feedItems = $site->footer_feeds()->toStructure(); ?>
          <?php if ($feedItems->isNotEmpty()): ?>
            <header>
              <span class="with-icon">
                <?= asset('assets/svg/icons/rss.svg')->read() ?>
                <h2>Feeds</h2>
              </span>
            </header>
            <div class="description">
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
      <div class="floating-controls">
        <button class="scroll-to-top floating" id="scroll-to-top-floating" aria-label="Scroll to top of page">
          <?= asset('assets/svg/icons/arrow-up.svg')->read() ?>
        </button>
      </div>
  </footer>

<!-- Fathom - beautiful, simple website analytics -->
<script src="https://cdn.usefathom.com/script.js" data-site="FCIAGYSD" defer></script>
<!-- / Fathom -->

<?= js([
  'assets/js/theme-picker-min.js',
  'assets/js/toc-min.js',
  'assets/js/prism-min.js',
  'assets/js/header-min.js',
]) ?>
</body>
</html>
