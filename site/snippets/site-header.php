<?php snippet('head') ?>

<body class="<?= $page->slug() ?> tmpl-<?= $page->template() ?> <?php if ($kirby->user()): ?>logged-in<?php endif ?>" data-color-mode="system" data-theme="design" data-user-email="<?= $kirby->user()?->email() ?? '' ?>">
  <a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <header class="site-header" role="banner"  <?php if ($kirby->user()): ?>style="top:var(--admin-bar--height)"<?php endif ?>>
    <div class="inner">
      <?php snippet('components/breadcrumb') ?>
      <div class="buttons">
        <button class="button" id="nav-toggle" aria-expanded="false" aria-controls="nav-panel" aria-label="Open navigation menu">
          <span id="nav-toggle-text">Open Navigation</span>
          <?= asset('assets/svg/icons/panel-right---to-open.svg')->read() ?>
        </button>
          <button class="theme-toggle footer" id="theme-toggle-footer" aria-label="Toggle between light and dark theme">
            <span class="theme-icon"><?= asset('assets/svg/icons/theme-dark.svg')->read() ?></span>
            <span class="theme-icon"><?= asset('assets/svg/icons/theme-light.svg')->read() ?></span>
          </button>

      </div>
    </div>
  </header>
  <div id="nav-panel" class="panel nav-panel">
    <?php $delay = 0; ?>

    <nav role="navigation" aria-label="Main navigation" aria-hidden="true">
      <ul class="nav-list" role="list">
          <?php $menuItems = $site->primary_nav()->toStructure(); ?>
            <?php foreach ($menuItems as $menuItem): ?>
              <?php $delay += 0.15; ?>
              <li class="nav-item" role="listitem" style="--item-delay:<?= $delay ?>s;">
                <a <?= ($p = $menuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $menuItem->link()->toUrl() ?>" class="nav-link"><?= $menuItem->title()->or($menuItem->link()->html()) ?></a></li>
            <?php endforeach ?>
        </ul>
    </nav>
  </div>

  <main role="main" <?php if($page->isHomePage()): ?>style="box-sizing:unset;"<?php endif ?>>
