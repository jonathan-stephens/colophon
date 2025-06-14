<?php snippet('site-header') ?>
  <?php
      // Get the link number
      $number = $page->num();
      // Format it with leading zeros if desired
      $formattedNumber = sprintf("%03d", $number);
  ?>
  <?php snippet('layout/default.php') ?>
<?php snippet('site-footer') ?>
