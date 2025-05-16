
  </main>

  <footer role="flow contentinfo">

    <?php $menuItems = $site->footer_nav()->toStructure(); ?>
      <?php if ($menuItems->isNotEmpty()): ?>
      <section class="navigation">
        <div class="wrapper">
          <h2>Navigation</h2>
          <nav role="navigation">
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
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 32 32" width="100%" height="auto"><title>bullhorn</title><path class="cls-1" d="M26,6V8.17L5.64,11.87a2,2,0,0,0-1.64,2v4.34a2,2,0,0,0,1.64,2L8,20.56V24a2,2,0,0,0,2,2h8a2,2,0,0,0,2-2V22.74l6,1.09V26h2V6ZM18,24H10V20.93l8,1.45ZM6,18.17V13.83L26,10.2V21.8Z"/></svg>
            <h2 id="newsletter">
              Join my mailing list</h2>
            <p>Every week or few, I send out an email newsletter with links and resources gathered in my internet wanderings—from my own work and by other humans on Earth.</p>
            <p><em>Subscribe to my newsletter—</em>Craft & Practice</p>
          </div>

          <form
            action="https://buttondown.com/api/emails/embed-subscribe/jonathanstephens"
            method="post"
            target="popupwindow"
            onsubmit="window.open('https://buttondown.com/jonathanstephens', 'popupwindow')"
            class="embeddable-buttondown-form"
          >
            <input type="email" name="email" id="bd-email" aria-label="Email Address" placeholder="Email Address" />

            <button type="submit" data-element="submit">
              <span>Submit</span>
            </button>
          </form>
        </div>
        <div class="feeds">
          <?php $feedItems = $site->footer_feeds()->toStructure(); ?>
            <?php if ($feedItems->isNotEmpty()): ?>
              <div class="description">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"
                	 viewBox="0 0 32 32" width="100%" height="auto"><title>RSS icon</title>
                  <path d="M8,18c-3.3,0-6,2.7-6,6s2.7,6,6,6s6-2.7,6-6C14,20.7,11.3,18,8,18z M8,28c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4
                  	C12,26.2,10.2,28,8,28z"/>
                  <path d="M30,24h-2C28,13,19,4,8,4V2C20.1,2,30,11.9,30,24z"/>
                  <path d="M22,24h-2c0-6.6-5.4-12-12-12v-2C15.7,10,22,16.3,22,24z"/>
                </svg>

                  <h2>Feeds</h2>
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

    <div class="final-info">
      <div class="wrapper">
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
