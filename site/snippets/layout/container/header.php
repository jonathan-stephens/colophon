<header>
  <h1 class="p-name" itemprop="name headline">
    <?= $page->hed()->isNotEmpty()
          ? $page->hed()->html()
          : $page->title()->html() ?></h1>
  <?php if($page->dek()->isNotEmpty()): ?>
    <p><?= $page->dek()->html() ?></p>
  <?php endif ?>
  
  <?php if (str_contains($page->uri(), 'tags/')): ?>
    <div class="tags-stats">
      <span class="stat">
          <strong><?= $totalTags ?></strong>
          <?= $totalTags === 1 ? 'tag' : 'tags' ?>
      </span>
      <span class="stat-separator">·</span>
      <span class="stat">
          <strong><?= $totalTaggedPages ?></strong>
          <?= $totalTaggedPages === 1 ? 'page' : 'pages' ?>
      </span>
    </div>
  <?php endif ?>
</header>
