<?php
/**
 * Tags Section Snippet
 *
 * Embeddable section for displaying a page's tags with related content
 * and tag exploration features. Use at the bottom of article/content pages.
 *
 * Usage:
 * <?php snippet('tag-garden/section', ['page' => $page]) ?>
 *
 * Options:
 * - page: Page object (required)
 * - showRelated: Show related content (default: true)
 * - showDrillDown: Show related tags for drilling down (default: true)
 * - relatedLimit: Max related pages to show (default: 5)
 * - relatedTagsLimit: Max related tags to show (default: 8)
 * - layout: 'full' (default) or 'compact'
 *
 * @version 1.0.0
 */

use TagGarden\Helpers;

// Get parameters
$page = $page ?? null;
$showRelated = $showRelated ?? true;
$showDrillDown = $showDrillDown ?? true;
$relatedLimit = $relatedLimit ?? 5;
$relatedTagsLimit = $relatedTagsLimit ?? 8;
$layout = $layout ?? 'full';

// Validate page exists and has tags
if (!$page || $page->tags()->isEmpty()) {
    return;
}

// Get page tags
$pageTags = $page->tags()->split(',');
$pageTags = array_filter(array_map('trim', $pageTags));

if (empty($pageTags)) {
    return;
}

// Get related pages if enabled
$relatedPages = $showRelated ? $page->relatedPages($relatedLimit) : null;

// Get related tags for drill-down if enabled
$relatedTags = $showDrillDown ? $page->relatedTags() : [];
if (!empty($relatedTags)) {
    // Get counts for each related tag
    $relatedTagsWithCounts = [];
    foreach (array_slice($relatedTags, 0, $relatedTagsLimit) as $tag) {
        $count = count(Helpers::getPagesByTags($tag));
        $relatedTagsWithCounts[$tag] = $count;
    }
    $relatedTags = $relatedTagsWithCounts;
}

?>

<section class="tags-section tags-section-<?= $layout ?>" data-component="tags-section">

    <!-- Current Page Tags -->
    <div class="tags-section-header">
        <h2>Tagged with</h2>
        <ul class="current-tags">
            <?php foreach ($pageTags as $tag): ?>
                <li>
                    <?php snippet('tag-garden/badge', [
                        'tag' => $tag,
                        'showCount' => false,
                        'size' => 'large'
                    ]) ?>
                </li>
            <?php endforeach ?>
        </ul>
    </div>

    <?php if ($layout === 'full'): ?>
        <div class="tags-section-content">

            <!-- Related Content -->
            <?php if ($showRelated && $relatedPages && $relatedPages->count() > 0): ?>
                <div class="related-content">
                    <h3>Related Content</h3>
                    <ul class="related-list">
                        <?php foreach ($relatedPages as $relatedPage): ?>
                            <li class="related-item">
                                <a href="<?= $relatedPage->url() ?>" class="related-link">
                                    <span class="related-title">
                                        <?= $relatedPage->title()->html() ?>
                                    </span>

                                    <?php if ($relatedPage->growth_status()->isNotEmpty()): ?>
                                        <?php $status = Helpers::getGrowthDefinition($relatedPage->growth_status()->value()) ?>
                                        <?php if ($status): ?>
                                            <span class="related-status" title="<?= $status['label'] ?>">
                                                <?= $status['emoji'] ?>
                                            </span>
                                        <?php endif ?>
                                    <?php endif ?>
                                </a>

                                <?php if ($relatedPage->wordCount() > 0): ?>
                                    <span class="related-meta">
                                        <?php snippet('tag-garden/reading-time', [
                                            'page' => $relatedPage,
                                            'showIcon' => false
                                        ]) ?>
                                    </span>
                                <?php endif ?>
                            </li>
                        <?php endforeach ?>
                    </ul>

                    <?php if (count($pageTags) === 1): ?>
                        <p class="related-footer">
                            <a href="<?= url('tags/' . Helpers::tagsToUrl($pageTags)) ?>">
                                View all content tagged "<?= html($pageTags[0]) ?>" →
                            </a>
                        </p>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <!-- Drill Down: Related Tags -->
            <?php if ($showDrillDown && !empty($relatedTags)): ?>
                <div class="drill-down">
                    <h3>Explore Related Tags</h3>
                    <p class="drill-down-description">
                        Discover content that shares tags with this page.
                    </p>
                    <ul class="related-tags-list">
                        <?php foreach ($relatedTags as $tag => $count): ?>
                            <li>
                                <?php snippet('tag-garden/badge', [
                                    'tag' => $tag,
                                    'count' => $count,
                                    'showCount' => true
                                ]) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>

                    <p class="drill-down-footer">
                        <a href="<?= url('tags') ?>">
                            Explore all tags →
                        </a>
                    </p>
                </div>
            <?php endif ?>

        </div>
    <?php endif ?>

    <!-- Compact Layout -->
    <?php if ($layout === 'compact' && $showDrillDown && !empty($relatedTags)): ?>
        <div class="tags-section-compact">
            <h3>Related Tags</h3>
            <ul class="related-tags-list">
                <?php foreach (array_slice($relatedTags, 0, 5, true) as $tag => $count): ?>
                    <li>
                        <?php snippet('tag-garden/badge', [
                            'tag' => $tag,
                            'count' => $count,
                            'showCount' => false
                        ]) ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

</section>
