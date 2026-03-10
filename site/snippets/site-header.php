<?php snippet('head') ?>

<body class="<?= $page->slug() ?> tmpl-<?= $page->template() ?> <?php if ($kirby->user()): ?>logged-in<?php endif ?>" data-color-mode="system" data-theme="design" data-user-email="<?= $kirby->user()?->email() ?? '' ?>">
  <a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <header class="site-header" role="banner" <?php if ($kirby->user()): ?>style="top:var(--admin-bar--height)"<?php endif ?>>
    <div class="inner">
      <?php snippet('components/breadcrumb') ?>
      <div class="buttons">

        <button class="button" id="nav-toggle"
          aria-expanded="false"
          aria-controls="nav-panel"
          aria-label="Open navigation menu">
          <span id="nav-toggle-text">Open Menu</span>
          <!--
            Both icons are inlined here at render time.
            CSS toggles visibility via #nav-toggle.is-open —
            no JS fetch, no DOM creation, no innerHTML parsing.
          -->
          <span class="icon icon--open"><?= asset('assets/svg/icons/panel-right---to-open.svg')->read() ?></span>
          <span class="icon icon--close"><?= asset('assets/svg/icons/panel-right---to-close.svg')->read() ?></span>
        </button>

        <button class="theme-toggle footer" id="theme-toggle-footer" aria-label="Toggle between light and dark theme">
          <span class="theme-icon"><?= asset('assets/svg/icons/theme-dark.svg')->read() ?></span>
          <span class="theme-icon"><?= asset('assets/svg/icons/theme-light.svg')->read() ?></span>
        </button>

      </div>
    </div>
  </header>

  <div id="nav-panel" class="panel nav-panel" aria-hidden="true">
    <?php $delay = 0; ?>

    <nav role="navigation" aria-label="Main navigation">
      <ul class="nav-list" role="list">
        <?php foreach ($site->primary_nav()->toStructure() as $menuItem): ?>
          <?php $delay += 0.15; ?>
          <li class="nav-item" role="listitem" style="--item-delay: <?= $delay ?>s;">
            <a
              <?= ($p = $menuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?>
              href="<?= $menuItem->link()->toUrl() ?>"
              class="nav-link"
            ><?= $menuItem->title()->or($menuItem->link()->html()) ?></a>
          </li>
        <?php endforeach ?>
      </ul>
    </nav>
  </div>

  <main role="main" <?php if ($page->isHomePage()): ?>style="box-sizing: unset;"<?php endif ?>>