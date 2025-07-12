<?php snippet('head') ?>

<body class="<?= $page->slug() ?> tmpl-<?= $page->template() ?>" data-color-mode="system" data-theme="design">
  <a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <header role="banner">
    <div class="wrapper">
      <?php snippet('components/breadcrumb') ?>
      <?php snippet('components/theme-picker') ?>
    </div>
  </header>

  <main role="main">
