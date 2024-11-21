<html dir="ltr" lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1" />


  <title>
    <?= $site->title()->esc() ?> | <?php if($page->isHomePage()): ?>Home<?php else: ?><?= $page->title()->esc() ?><?php endif ?>

  </title>

  <!-- webmention -->
  <link rel="pingback" href="https://webmention.io/jonathanstephens.us/xmlrpc" />
  <link rel="webmention" href="https://webmention.io/jonathanstephens.us/webmention" />
  <link rel="micropub" href="https://jonathanstephens.us/micropub">
  <link rel="authorization_endpoint" href="https://indieauth.com/auth">
  <link href="https://github.com/jonathan-stephens" rel="me">


  <?php snippet('webmention-endpoint'); ?>


  <?= css([
    'assets/css/main.css',
  ]) ?>

  <noscript>
    <style>
      #theme-picker {
        display: none;
      }
    </style>
  </noscript>
  <?php
    $period = TimeKeeper::getCurrentPeriod();
    $season = TimeKeeper::getCurrentSeason();
    ?>
</head>

<body class="<?= $page->slug() ?>">
  <header role="banner">
    <div class="wrapper">
      <?php snippet('components/breadcrumb') ?>
      <?php snippet('components/theme-picker') ?>
    </div>
  </header>

  <main role="main">
