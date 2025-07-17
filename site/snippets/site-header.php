<?php snippet('head') ?>

<body class="<?= $page->slug() ?> tmpl-<?= $page->template() ?>" data-color-mode="system" data-theme="design">
  <a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <?php if ($kirby->user()): ?>
    <header class="site-header" role="banner" style="top:48px;">
      <div>
        <button
            class="button"
            id="nav-toggle"
            aria-expanded="false"
            aria-controls="nav-panel"
            aria-label="Open navigation menu"
        >
          <?= asset('assets/svg/icons/panel-left---to-open.svg')->read() ?>
          <span id="nav-toggle-text"><em>Open </em>Navigation</span>
        </button>

        <a href="#" class="brandmark" aria-label="Go to homepage">
          <?= asset('assets/svg/brandmark.svg')->read() ?>
        </a>
<!--
        <button
            class="button"
            id="preferences-toggle"
            aria-expanded="false"
            aria-controls="preferences-panel"
            aria-label="Open preferences menu"
        >
            <span id="preferences-toggle-text"><em>Open</em>Preferences</span>
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
              <path class="cls-2" d="m2,6v20c0,1.1045.8955,2,2,2h24c1.1045,0,2-.8955,2-2V6c0-1.1045-.8955-2-2-2H4c-1.1045,0-2,.8955-2,2Zm2,0h16v9h-10.1699s3.5798-3.5898,3.5798-3.5898l-1.4099-1.4102-6,6,6,6,1.4099-1.4102-3.5798-3.5898h10.1699v9H4V6Z"/>
            </svg>
          </button>
-->
      </div>
      <?php snippet('components/theme-picker') ?>
    </header>

    <div class="overlay" id="overlay" aria-hidden="true"></div>
    <nav
      class="panel nav-panel"
      id="nav-panel"
      role="navigation"
      aria-label="Main navigation"
      aria-hidden="true">

      <div class="panel-header">
        <button
          class="button"
          id="nav-close"
          aria-expanded="true"
          aria-controls="nav-panel"
          aria-label="Close navigation menu">

          <?= asset('assets/svg/icons/panel-left---to-open.svg')->read() ?>
          <span class="sr-only">Close Navigation</span>
        </button>
          <a href="#" class="brandmark" aria-label="Go to homepage">
            <?= asset('assets/svg/brandmark.svg')->read() ?>
          </a>
        </div>
        <ul class="nav-list" role="list">
            <li class="nav-item" role="listitem" style="--item-delay:.5s;">
                <a href="#" class="nav-link">Home</a>
            </li>
            <li class="nav-item" role="listitem" style="--item-delay:1.5s;">
                <a href="#" class="nav-link">About</a>
            </li>
            <li class="nav-item" role="listitem" style="--item-delay:2s;">
                <a href="#" class="nav-link">Services</a>
            </li>
            <li class="nav-item" role="listitem" style="--item-delay:2.5s;">
                <a href="#" class="nav-link">Portfolio</a>
            </li>
            <li class="nav-item" role="listitem" style="--item-delay:3s;">
                <a href="#" class="nav-link">Blog</a>
            </li>
            <li class="nav-item" role="listitem" style="--item-delay:3.5s;">
                <a href="#" class="nav-link">Contact</a>
            </li>
        </ul>
    </nav>

<!--
    <aside
        class="panel preferences-panel"
        id="preferences-panel"
        role="region"
        aria-label="Site preferences"
        aria-hidden="true"
    >
        <div class="panel-header">
          <a href="#" class="brandmark" aria-label="Go to homepage">
           asset('assets/svg/brandmark.svg')->read()
          </a>
            <button
                class="button"
                id="preferences-close"
                aria-expanded="true"
                aria-controls="preferences-panel"
                aria-label="Close preferences menu"
            >
              <span class="sr-only">Close Preferences</span>
            </button>
        </div>

        <div class="preference-section">
            <h2 class="preference-title">Color Mode</h2>
            <div class="preference-options" role="radiogroup" aria-label="Color mode selection">
                <div class="preference-option">
                    <input type="radio" id="color-system" name="color-mode" value="system" checked>
                    <label for="color-system">System Preference</label>
                </div>
                <div class="preference-option">
                    <input type="radio" id="color-light" name="color-mode" value="light">
                    <label for="color-light">Light Mode</label>
                </div>
                <div class="preference-option">
                    <input type="radio" id="color-dark" name="color-mode" value="dark">
                    <label for="color-dark">Dark Mode</label>
                </div>
            </div>
        </div>

        <div class="preference-section">
            <h2 class="preference-title">Theme</h2>
            <div class="preference-options" role="radiogroup" aria-label="Theme selection">
                <div class="preference-option">
                    <input type="radio" id="theme-by-design" name="theme" value="by-design" checked>
                    <label for="theme-by-design">By Design</label>
                </div>
                <div class="preference-option">
                    <input type="radio" id="theme-mono" name="theme" value="mono">
                    <label for="theme-mono">Mono</label>
                </div>
                <div class="preference-option">
                    <input type="radio" id="theme-forest" name="theme" value="forest">
                    <label for="theme-forest">Forest</label>
                </div>
                <div class="preference-option">
                    <input type="radio" id="theme-ocean" name="theme" value="ocean">
                    <label for="theme-ocean">Ocean</label>
                </div>
            </div>
        </div>
    </aside>
  -->
  <?php else: ?>
    <header role="banner">
      <div class="wrapper">
        <?php snippet('components/breadcrumb') ?>
        <?php snippet('components/theme-picker') ?>
      </div>
    </header>
  <?php endif ?>


  <main role="main" <?php if ($kirby->user()): ?>style="padding-top:20em;"<?php endif ?>>
