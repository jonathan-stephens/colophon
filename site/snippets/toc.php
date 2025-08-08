<?php
/**
 * Table of Contents Snippet for Kirby 4
 * Usage: <?= snippet('toc', [
 *     'exclude' => ['.toc-title', '#skip-this', 'h2.no-toc'],
 *     'minLevel' => 2,
 *     'maxLevel' => 6,
 *     'minSubheadings' => 2,
 *     'mainSelector' => 'main'
 * ]) ?>
 *
 * Scans the actual DOM for headings in <main> after page render
 * Only shows TOC if there are enough sub-headings to justify it
 */

// Get configuration options with defaults
$config = [
    'exclude' => array_merge(['.toc-title'], $exclude ?? []), // Always include default, merge with user exclusions
    'minLevel' => $minLevel ?? 2, // Minimum heading level (h2)
    'maxLevel' => $maxLevel ?? 6, // Maximum heading level (h6)
    'minSubheadings' => $minSubheadings ?? 0, // Min subheadings needed to show TOC (changed from 2 to 1)
    'mainSelector' => $mainSelector ?? 'main', // Container to scan for headings
    'tocTitle' => $tocTitle ?? 'Table of Contents', // TOC title text
    'hideTitle' => $hideTitle ?? false, // Hide the TOC title completely
];

// Generate unique ID for this TOC instance
$tocId = 'toc-' . uniqid();
?>

<nav class="table-of-contents"
     aria-label="<?= $config['tocTitle'] ?>"
     id="<?= $tocId ?>"
     data-toc-config="<?= htmlspecialchars(json_encode($config)) ?>"
     style="display: none;">
     <?php if (!$config['hideTitle']): ?>
        <details open><summary>
<span class="toc-title"><span class="with-icon"><?= asset('assets/svg/icons/panel-expansion.svg')->read() ?><?= $config['tocTitle'] ?></span><?= asset('assets/svg/icons/chevron-down.svg')->read() ?></span></summary></details>
    <?php endif; ?>
    <div class="content">
      <ol class="toc-list" id="<?= $tocId ?>-content">
          <!-- TOC will be populated by JavaScript -->
      </ol>
    </div>
</nav>
