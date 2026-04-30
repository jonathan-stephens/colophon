<header>
  <h1 class="p-name" itemprop="name headline">
    <?= $page->hed()->isNotEmpty()
          ? $page->hed()->html()
          : $page->title()->html() ?></h1>
  <?php if($page->dek()->isNotEmpty()): ?>
    <p><?= $page->dek()->html() ?>
    
    <!-- Adding tag count to /tags in the header -->
    <?php if (str_contains($page->uri(), 'tags')): ?>
      of <?= $totalTags ?>
      <?= $totalTags === 1 ? 'tag' : 'tags' ?>
    <?php endif ?>
    </p>
  <?php endif ?>  
</header>
