<?php snippet('head') ?>

<body class="<?= $page->slug() ?>">
  <a href="#main" class="skip-link sr-only">Skip to main content</a>

  <header role="banner">
    <div class="wrapper">
      <?php snippet('components/breadcrumb') ?>
      <?php snippet('components/theme-picker') ?>
    </div>
  </header>

  <main role="main">
