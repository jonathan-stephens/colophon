<html dir="ltr" lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1" />


  <title>
    <?= $site->title()->esc() ?> |
    <?php if($page->isHomePage()): ?>
      Home
    <?php else: ?>
     <?= $page->title()->esc() ?>
   <?php endif ?>
  </title>

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
</head>

<body class="<?= $page->slug() ?>">
  <header class="header">

  </header>

  <main class="main">
