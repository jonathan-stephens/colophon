<?php
/**
 * Reading Time Snippet
 *
 * Displays the estimated reading time for a page with a range
 * for fast/slow readers.
 *
 * Usage:
 * <?php snippet('tag-garden/reading-time', ['page' => $page]) ?>
 *
 * Options:
 * - page: Page object (required)
 * - format: 'short' (default) or 'long'
 * - showIcon: true/false (default: true)
 *
 * @version 1.0.1
 */

// Get parameters
$page = $page ?? null;
$format = $format ?? 'short';
$showIcon = $showIcon ?? true;

// Validate page exists
if (!$page) {
    return;
}

// Try to get word count - if it fails, the page doesn't have the method
try {
    $wordCount = $page->wordCount();
} catch (Exception $e) {
    return;
}

// Don't show for very short content
if ($wordCount < 50) {
    return;
}

// Get reading time data
$formatted = $page->readingTimeFormatted();

?>

<span class="reading-time" title="<?= $wordCount ?> words">
    <?php if ($showIcon): ?>
        <svg class="reading-time-icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zm0 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm.5 2v5.5l3.5 2.1-.8 1.3L7 9V3h1.5z"/>
        </svg>
    <?php endif ?>

    <?php if ($format === 'long'): ?>
        <span class="reading-time-text">
            Estimated reading time: <?= $formatted ?>
        </span>
    <?php else: ?>
        <span class="reading-time-text"><?= $formatted ?></span>
    <?php endif ?>
</span>
