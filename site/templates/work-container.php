<?php snippet('site-header') ?>
  <?php snippet('post-header') ?>
  <?php snippet('post-prose') ?>

  <h1><?php $pages->find('level1/level2/target-page') ?></h1>

  <section class="case-studies">
    <h2>Case Studies</h2>
    <p>Some recent work as an independent consultant, contributing strategically and individually.</p>asdfasdf
    <?php snippet('/components/case-studies', ['hedLevel' => 3]) ?>
  </section>
  
  <?php snippet('post-footer') ?>
<?php snippet('site-footer') ?>
