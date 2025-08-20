<?php
// snippets/components/pub-status.php

// Check if user is logged in
$isLoggedIn = $kirby->user();

// Get the page/article from passed variables, fallback to current page
$page = $page ?? $site->page();

// Only show for logged-in users
if ($isLoggedIn):
  // Determine page status and display appropriate icon
  $status = $page->status();
  $statusClass = '';
  $statusTitle = '';

  switch($status) {
    case 'listed':
      $statusClass = 'status-listed';
      $statusTitle = 'Published';
      break;
    case 'unlisted':
      $statusClass = 'status-unlisted';
      $statusTitle = 'Unlisted';
      break;
    case 'draft':
    default:
      $statusClass = 'status-draft';
      $statusTitle = 'Draft';
      break;
  }
?>
  <?php
    switch($status) {
      case 'listed':
        echo asset('assets/svg/icons/status-listed.svg')->read();
        break;
      case 'unlisted':
        echo asset('assets/svg/icons/status-unlisted.svg')->read();
        break;
      case 'draft':
        default:
        echo asset('assets/svg/icons/status-draft.svg')->read();
        break;
    }
  ?>
<?php endif ?>
