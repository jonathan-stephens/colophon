<?php snippet('head') ?>

<body class="<?= $page->slug() ?> tmpl-<?= $page->template() ?> <?php if ($kirby->user()): ?>logged-in<?php endif ?>" data-color-mode="system" data-theme="design">
  <a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <header class="site-header" role="banner"  <?php if ($kirby->user()): ?>style="top:var(--admin-bar--height)"<?php endif ?>>
    <?php snippet('components/breadcrumb') ?>
    <button class="button" id="nav-toggle" aria-expanded="false" aria-controls="nav-panel" aria-label="Open navigation menu">
      <span id="nav-toggle-text">Open Navigation</span>
      <?= asset('assets/svg/icons/panel-right---to-open.svg')->read() ?>
    </button>
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
      <section class="theme-picker" style="--item-delay:<?= $delay + 0.15 ?>s;">
        <h2>Theme</h2>
        <?php snippet('components/theme-picker') ?>
      </section>
    </div>
  </header>

  <main role="main">
