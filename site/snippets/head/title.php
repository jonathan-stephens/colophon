<title>
      <?= $site->title()->esc() ?> | <?php if($page->isHomePage()): ?>Home<?php else: ?><?= $page->title()->esc() ?><?php endif ?>

    </title>
