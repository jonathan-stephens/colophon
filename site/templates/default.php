<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('components/breadcrumb') ?>

    <div class="splash">
      <?= $page->text()->kirbytext() ?>

      <div class="cta">
        <ul>
          <li class="connect-linkedin">
            <a href="https://linkedin.com/in/elnatnal">
              Connect on LinkedIn
            </span>
            </a>
          </li>
          <li class="subscribe-substack">
            <a href="https://jonathanstephens.substack.com/">
              Subscribe on Substack
            </a>
          </li>
          <li class="book-calendar">
            <a href="https://cal.com/jonathanstephens/book">
              Book time on my calendar
            </a>
          </li>
        </ul>
      </footer>
    </div>
  </section>
<?php snippet('footer') ?>
