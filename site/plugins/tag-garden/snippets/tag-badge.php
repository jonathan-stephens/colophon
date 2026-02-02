<?php
/**
 * Tag Badge Snippet
 *
 * Renders a single tag as a clickable badge with optional count.
 *
 * Usage:
 * <?php snippet('tag-garden/badge', ['tag' => 'design', 'count' => 5]) ?>
 *
 * Options:
 * - tag: Tag name (required)
 * - count: Number of uses (optional)
 * - active: Whether this tag is active/selected (default: false)
 * - showCount: Whether to show count (default: true if count provided)
 * - rel: Link rel attribute (default: 'tag')
 * - size: 'small', 'medium' (default), or 'large'
 * - theme: Optional theme key for color coding
 *
 * @version 1.0.0
 */

use Yourusername\TagGarden\Helpers;

// Get parameters
$tag = $tag ?? null;
$count = $count ?? null;
$active = $active ?? false;
$showCount = $showCount ?? ($count !== null);
$rel = $rel ?? 'tag';
$size = $size ?? 'medium';
$theme = $theme ?? null;

// Validate required parameters
if (!$tag || empty(trim($tag))) {
    return;
}

$tag = trim($tag);

// Generate tag URL
$tagUrl = url('tags/' . Helpers::tagsToUrl([$tag]));

// Build CSS classes
$classes = ['tag-badge', 'tag-badge-' . $size];
if ($active) {
    $classes[] = 'active';
}
if ($theme) {
    $classes[] = 'tag-theme-' . $theme;
}

// Get theme color if specified
$themeColor = null;
if ($theme) {
    $themeDef = Helpers::getThemeDefinition($theme);
    if ($themeDef && isset($themeDef['color'])) {
        $themeColor = $themeDef['color'];
    }
}

?>

<a href="<?= $tagUrl ?>"
   rel="<?= $rel ?>"
   class="<?= implode(' ', $classes) ?>"
   <?php if ($themeColor): ?>
       style="--tag-color: <?= $themeColor ?>"
   <?php endif ?>
   title="View all content tagged with '<?= html($tag) ?>'">
    <span class="tag-name"><?= html($tag) ?></span>
    <?php if ($showCount && $count !== null): ?>
        <span class="tag-count"><?= $count ?></span>
    <?php endif ?>
</a>
