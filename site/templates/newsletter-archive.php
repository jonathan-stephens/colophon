<?php snippet('site-header') ?>
<div class="wrapper">
  <?php snippet('layout/container/header') ?>
  <div class="content">
    <?php
      $allChildren = getVisibleChildren($page);
      $limit = getPaginationLimit($allChildren->count());
      $articles = $allChildren->flip()->paginate($limit);
      $pagination = $articles->pagination();

      snippet('layout/container/post', [
          'articles' => $articles,
          'layout' => 'default'
      ]);
    ?>
  </div>
  <footer>
    <?php snippet('components/pagination', ['pagination' => $pagination]) ?>
  </footer>
</div>
<?php snippet('site-footer') ?>
