<div class="meta on-at-in">
  <time class="dt-published" datetime="<?= $page->date()->toDate('F d Y') ?><?= $page->time()->toDate('H:i') ?>" itemprop="dateCreated pubdate datePublished">
    <span class="with-icon">
      <?= asset('assets/svg/icons/date.svg')->read() ?>
      <?= $page->date()->toDate('l, M d Y') ?></span> at <?= $page->time()->toDate('H:i') ?>
    </span>
  </time>
  <?php if(!$page->addressLocality()->isEmpty() || !$page->addressRegion()->isEmpty() || !$page->addressCountry()->isEmpty()): ?>
  <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress" class="with-icon"><?= asset('assets/svg/icons/location.svg')->read() ?>

    <?php if(!$page->addressLocality()->isEmpty()): ?>
      <span itemprop="addressLocality"><?= $page->addressLocality()->html() ?></span>,&ensp;
    <?php endif ?>
    <?php if(!$page->addressRegion()->isEmpty()): ?>
      <span itemprop="addressRegion"><?= $page->addressRegion()->html() ?></span>,&ensp;
    <?php endif ?>
    <?php if(!$page->addressCountry()->isEmpty()): ?>
      <span itemprop="addressCountry"><?= $page->addressCountry()->html() ?></span>
    <?php endif ?>
  </div>
  <?php endif ?>
</div>
