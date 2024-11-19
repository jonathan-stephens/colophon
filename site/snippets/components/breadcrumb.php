
    <nav class="breadcrumb" aria-label="breadcrumb">
      <ol role="list" class="cluster">
        <?php
        $specialSections = ['journal', 'notes', 'links'];
        $currentSection = null;

        foreach ($specialSections as $section) {
          if ($page->parents()->has($site->find($section))) {
            $currentSection = $section;
            break;
          }
        }

        $breadcrumbItems = $site->breadcrumb();

        foreach($breadcrumbItems as $crumb):
          // Special handling for specific sections
          if ($currentSection && $crumb->isActive()) {
            switch ($currentSection) {
              case 'journal':
                $year = $page->metadata()->date()->toDate('Y');
                $monthDay = $page->metadata()->date()->toDate('F j, Y');

                $yearPage = $site->find('journal/' . $year);
                if ($yearPage) {
                  echo '<li><a href="' . $yearPage->url() . '">' . $year . '</a></li>';
                }

                echo '<li><a aria-current="page">' . $monthDay . '</a></li>';
                break;

              case 'notes':
                // Add notes-specific breadcrumb logic here
                echo '<li><a aria-current="page">' . html($page->title()) . '</a></li>';
                break;

              case 'links':
                // Add links-specific breadcrumb logic here
                echo '<li><a aria-current="page">' . html($page->title()) . '</a></li>';
                break;
            }
            break;
          } else {
            // Default breadcrumb rendering
            $ariaCurrent = $crumb->isActive() ? ' aria-current="page"' : '';
            echo '<li><a href="' . $crumb->url() . '"' . $ariaCurrent . '>' .
                 html($crumb->title()) . '</a></li>';
          }
        endforeach ?>
      </ol>
    </nav>
