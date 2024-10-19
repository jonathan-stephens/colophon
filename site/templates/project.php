<?php snippet('header') ?>
  <section class="wrapper">
    <?php snippet('components/breadcrumb') ?>


    <article>
      <h1><?= $page->hed() ?></h1>
      <p class="subtitle"><?= $page->dek() ?></p>
      <p class="summary"><?= $page->lede() ?></p>
      <p class="paraphrase"><?= $page->nutgraf() ?></p>

      <h2>Skills</h2>
      <ul>
        <?php foreach($page->skills()->split() as $skill): ?>
          <li><?= $skill ?></li>
        <?php endforeach ?>
      </ul>

      <h2>Services</h2>
      <ul>
        <?php foreach($page->services()->split() as $service): ?>
          <li><?= $service ?></li>
        <?php endforeach ?>
      </ul>

      <h2>Organization</h2>
      <?php foreach($page->organization()->toStructure() as $org): ?>
        <h3><?= $org->team() ?></h3>
        <p>People: <?= $org->people() ?></p>
        <p>Roles: <?= $org->roles() ?></p>
      <?php endforeach ?>

      <h2>Description</h2>
      <?= $page->prose()->kirbytext() ?>

      <p>Duration: <?= $page->date_start() ?> to <?= $page->date_end() ?></p>

      <p>Company: <a href="<?= $page->company()->toPage()->url() ?>"><?= $page->company()->toPage()->title() ?></a></p>
    </article>
<?php snippet('footer') ?>
