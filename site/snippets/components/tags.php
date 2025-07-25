<?php if ($page->template() == 'project-container'): ?>
  <?php foreach ($reference->tags()->split() as $tag): ?>
    <span rel="tag" class="p-category">
      <?= $tag?>
    </span>
  <?php endforeach ?>
<?php else: ?>
  <p class="tags cluster">
    <span class="with-icon">
      <?= asset('assets/svg/icons/tag.svg')->read() ?>
      Tags:
    </span>
      <?php foreach ($reference->tags()->split() as $tag): ?>
        <a rel="tag" class="p-category button" href="<?= url('tags/' . urlencode($tag)) ?>">
          <?= $tag ?>
        </a>
      <?php endforeach ?>
  </p>
<?php endif ?>
