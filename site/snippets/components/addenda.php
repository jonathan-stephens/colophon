<?php $addendaItems = $site->footer_addenda()->toStructure(); ?>
<?php if ($addendaItems->isNotEmpty()): ?>
  <section class="addenda">
    <?php foreach ($addendaItems as $addendum): ?>
      <a href="<?= $addendum->link()->toUrl() ?>"><?= $addendum->title()->or($addendum->link()->html()) ?></a>
    <?php endforeach ?>
  </section>
<?php endif ?>
