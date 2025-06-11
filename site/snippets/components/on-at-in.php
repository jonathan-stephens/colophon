<div class="meta on-at-in">
  <time class="dt-published" datetime="<?= $page->date()->toDate('F d Y') ?><?= $page->time()->toDate('H:i') ?>" itemprop="dateCreated pubdate datePublished">
    <span class="with-icon">
      <svg class="icon" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M15 17C16.1046 17 17 16.1046 17 15C17 13.8954 16.1046 13 15 13C13.8954 13 13 13.8954 13 15C13 16.1046 13.8954 17 15 17Z"
          fill="currentColor"/>
        <path
          fill-rule="evenodd"
          clip-rule="evenodd"
          d="M6 3C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6ZM5 18V7H19V18C19 18.5523 18.5523 19 18 19H6C5.44772 19 5 18.5523 5 18Z"
          fill="currentColor"
        />
      </svg>
      <?= $page->date()->toDate('l, M d Y') ?></span> at <?= $page->time()->toDate('H:i') ?>
    </span>
  </time>
  <?php if(!$page->addressLocality()->isEmpty() || !$page->addressRegion()->isEmpty() || !$page->addressCountry()->isEmpty()): ?>
  <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress" class="with-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
    <title>location</title>
    <path d="M16,18a5,5,0,1,1,5-5A5.0057,5.0057,0,0,1,16,18Zm0-8a3,3,0,1,0,3,3A3.0033,3.0033,0,0,0,16,10Z"/>
    <path d="M16,30,7.5645,20.0513c-.0479-.0571-.3482-.4515-.3482-.4515A10.8888,10.8888,0,0,1,5,13a11,11,0,0,1,22,0,10.8844,10.8844,0,0,1-2.2148,6.5973l-.0015.0025s-.3.3944-.3447.4474ZM8.8125,18.395c.001.0007.2334.3082.2866.3744L16,26.9079l6.91-8.15c.0439-.0552.2783-.3649.2788-.3657A8.901,8.901,0,0,0,25,13,9,9,0,1,0,7,13a8.9054,8.9054,0,0,0,1.8125,5.395Z"/>
  </svg>

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
