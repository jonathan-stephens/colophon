<?php snippet('head') ?>

<body class="<?= $page->slug() ?> tmpl-<?= $page->template() ?>" data-color-mode="system" data-theme="design">
  <a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <?php if ($kirby->user()): ?>
    <header class="site-header" role="banner" style="top:48px;">
        <button
          class="button"
          id="nav-toggle"
          aria-expanded="false"
          aria-controls="nav-panel"
          aria-label="Open navigation menu">
          <?= asset('assets/svg/icons/panel-left---to-open.svg')->read() ?>
          <span id="nav-toggle-text">Open Navigation</span>
        </button>
        <a href="#" class="logo" aria-label="Go to homepage">
          <?= asset('assets/svg/brandmark.svg')->read() ?>
        </a>

        <?php snippet('components/theme-picker') ?>

      <nav
        class="panel nav-panel"
        id="nav-panel"
        role="navigation"
        aria-label="Main navigation"
        aria-hidden="true"
        <?php if ($kirby->user()): ?>
        style="padding-top:68px;"
        <?php endif ?>
          >
        <ul class="nav-list" role="list">
          <?php $delay = 0; ?>
          <?php $menuItems = $site->primary_nav()->toStructure(); ?>
            <?php foreach ($menuItems as $menuItem): ?>
              <?php $delay += 0.2; ?>
              <li class="nav-item" role="listitem" style="--item-delay:<?= $delay ?>s;">
                <a <?= ($p = $menuItem->link()->toPage()) && $p->isOpen() ? 'aria-current="page"' : '' ?> href="<?= $menuItem->link()->toUrl() ?>" class="nav-link"><?= $menuItem->title()->or($menuItem->link()->html()) ?></a></li>
            <?php endforeach ?>
        </ul>
      </nav>
    </header>
    <div class="overlay" id="overlay" aria-hidden="true"></div>
  <?php else: ?>
    <header role="banner">
      <div class="wrapper">
        <?php snippet('components/breadcrumb') ?>
        <?php snippet('components/theme-picker') ?>
      </div>
    </header>
  <?php endif ?>


  <main role="main" <?php if ($kirby->user()): ?>style="padding-top:20em;"<?php endif ?>>
