<nav class="breadcrumb" aria-label="breadcrumb">
  <ol role="list" class="cluster">
    <?php
    // Home with logo
    echo '<li><a href="' . $site->url() . '" class="logo" aria-label="Go to homepage">' . asset('assets/svg/brandmark.svg')->read() . '</a></li>';

    $specialSections = ['journal', 'notes', 'links'];
    $currentSection = null;
    foreach ($specialSections as $section) {
      if ($page->parents()->has($site->find($section))) {
        $currentSection = $section;
        break;
      }
    }

    // Define slugs where links should be removed for current page
    $noLinkSlugs = ['locked']; // Add more slugs here as needed
    $shouldRemoveLink = in_array($page->slug(), $noLinkSlugs);

    $breadcrumbItems = $site->breadcrumb();
    // Remove home from breadcrumb items since we handle it separately
    $breadcrumbItems = $breadcrumbItems->filter(function($item) use ($site) {
      return $item->id() !== $site->homePage()->id();
    });

    // Build breadcrumb text to check length
    $breadcrumbText = '';
    foreach($breadcrumbItems as $crumb) {
      if ($currentSection && $crumb->isActive()) {
        switch ($currentSection) {
          case 'journal':
            $year = $page->date()->toDate('Y');
            $monthDay = $page->date()->toDate('F j, Y');
            $breadcrumbText .= $year . ' ' . $monthDay;
            break;
          case 'notes':
          case 'links':
            $breadcrumbText .= $crumb->title();
            break;
        }
        break;
      } else {
        $breadcrumbText .= $crumb->title() . ' ';
      }
    }

    // Check if concatenation is needed
    $needsConcatenation = Str::length($breadcrumbText) > 60;

    foreach($breadcrumbItems as $crumb):
      // Special handling for specific sections
      if ($currentSection && $crumb->isActive()) {
        switch ($currentSection) {
          case 'journal':
            $year = $page->date()->toDate('Y');
            $monthDay = $page->date()->toDate('F j, Y');
            $yearPage = $site->find($currentSection . '/' . $year);
            if ($yearPage) {
              echo '<li><a href="' . $yearPage->url() . '">' . $year . '</a></li>';
            }
            echo '<li><a aria-current="page">' . $monthDay . '</a></li>';
            break;
          case 'notes':
          case 'links':
            $title = $needsConcatenation ? Str::short($crumb->title(), 30) : $crumb->title();
            // Check if link should be removed for current page
            if ($shouldRemoveLink && $crumb->isActive()) {
              echo '<li><span aria-current="page">' . html($title) . '</span></li>';
            } else {
              echo '<li><a aria-current="page">' . html($title) . '</a></li>';
            }
            break;
        }
        break;
      } else {
        // Default breadcrumb rendering
        $ariaCurrent = $crumb->isActive() ? ' aria-current="page"' : '';
        $title = $needsConcatenation ? Str::short($crumb->title(), 20) : $crumb->title();

        // Check if link should be removed for current page
        if ($shouldRemoveLink && $crumb->isActive()) {
          echo '<li><span aria-current="page">' . html($title) . '</span></li>';
        } else {
          echo '<li><a href="' . $crumb->url() . '"' . $ariaCurrent . '>' .
               html($title) . '</a></li>';
        }
      }
    endforeach ?>
  </ol>
</nav>
