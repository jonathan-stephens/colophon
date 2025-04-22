<?php
// Helper function to recursively render TOC items
function renderTocItems($items) {
  if (empty($items)) return '';

  $html = '';
  foreach ($items as $item) {
    $html .= '<li><a href="' . htmlspecialchars($item['url']) . '">' .
             htmlspecialchars($item['text']) . '</a>';

    // Render children if exist
    if (!empty($item['children'])) {
      $html .= '<ol class="toc-item level-' . $item['level'] . '">' .
               renderTocItems($item['children']) . '</ol>';
    }

    $html .= '</li>';
  }
  return $html;
}
?>

<?php if (!empty($headlines)): ?>
<nav class="toc">
  <h2>Table of Contents</h2>
  <ol>
    <?= renderTocItems($headlines) ?>
  </ol>
</nav>
<?php endif ?>

I want to get to this here.

<aside class="TableOfContents">
  <h5>Table of contents</h5>
  <a class="H2" href="#linear-gradient-kaleidoscope">Linear gradient kaleidoscope</a>
  <a class="H2" href="#radial-gradient-kaleidoscope">Radial gradient kaleidoscope</a>
  <a class="H2" href="#conic-gradient-kaleidoscope">Conic gradient kaleidoscope</a>
  <a href="#comments">(1) Comments</a>
</aside>

as just a list without an li. tag. It's the smeantic bit that's there. It's not technically a list...from a an <ol> or <ul> or <dl> sense...but a navigation...even then...

  Same sort of thing with the tags.
