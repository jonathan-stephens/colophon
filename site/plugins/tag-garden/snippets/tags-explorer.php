<?php
/**
 * Tags Explorer Snippet
 *
 * Interactive tag exploration component showing tags with filters,
 * search, and drill-down capabilities.
 *
 * Usage:
 * <?php snippet('tag-garden/explorer') ?>
 *
 * Options:
 * - limit: Max tags to show initially (default: 30)
 * - showSearch: Show search box (default: true)
 * - showFilters: Show group/theme filters (default: true)
 * - showStats: Show usage statistics (default: true)
 * - sortBy: 'count' (default) or 'alpha'
 * - minCount: Minimum tag count to include (default: 1)
 *
 * @version 1.0.0
 */

use TagGarden\Helpers;

// Get parameters
$limit = $limit ?? 30;
$showSearch = $showSearch ?? true;
$showFilters = $showFilters ?? true;
$showStats = $showStats ?? true;
$sortBy = $sortBy ?? 'count';
$minCount = $minCount ?? 1;

// Get current filters from query params
$activeGroup = get('group');
$activeTheme = get('theme');
$searchTerm = get('search');

// Get tags based on filters
if ($activeGroup) {
    $tags = kirby()->collection('tags.byGroup', ['group' => $activeGroup]);
} elseif ($activeTheme) {
    $tags = kirby()->collection('tags.byTheme', ['theme' => $activeTheme]);
} else {
    $tags = kirby()->collection('tags.all', [
        'minCount' => $minCount,
        'sortBy' => $sortBy
    ]);
}

// Apply search filter if provided
if ($searchTerm) {
    $tags = array_filter($tags, function($count, $tag) use ($searchTerm) {
        return stripos($tag, $searchTerm) !== false;
    }, ARRAY_FILTER_USE_BOTH);
}

// Calculate statistics
$totalTags = count($tags);
$totalUses = array_sum($tags);
$avgUses = $totalTags > 0 ? round($totalUses / $totalTags, 1) : 0;

// Get groups and themes for filters
$groups = [];
foreach (['garden', 'soil', 'work', 'about'] as $key) {
    $def = Helpers::getGroupDefinition($key);
    if ($def) {
        $groups[$key] = $def;
    }
}

$themes = [];
foreach (['topic', 'medium', 'status', 'audience'] as $key) {
    $def = Helpers::getThemeDefinition($key);
    if ($def) {
        $themes[$key] = $def;
    }
}

?>

<div class="tags-explorer" data-component="tags-explorer">

    <!-- Header -->
    <header class="explorer-header">
        <h2>Explore Tags</h2>

        <?php if ($showStats): ?>
            <div class="explorer-stats">
                <span class="stat">
                    <strong><?= $totalTags ?></strong> tags
                </span>
                <span class="stat-separator">·</span>
                <span class="stat">
                    <strong><?= $totalUses ?></strong> total uses
                </span>
                <?php if ($totalTags > 0): ?>
                    <span class="stat-separator">·</span>
                    <span class="stat">
                        <strong><?= $avgUses ?></strong> avg per tag
                    </span>
                <?php endif ?>
            </div>
        <?php endif ?>
    </header>

    <!-- Search -->
    <?php if ($showSearch): ?>
        <div class="explorer-search">
            <form method="get" action="">
                <label for="tag-search" class="visually-hidden">Search tags</label>
                <input type="search"
                       id="tag-search"
                       name="search"
                       placeholder="Search tags..."
                       value="<?= html($searchTerm ?? '') ?>"
                       autocomplete="off">
                <button type="submit">Search</button>
                <?php if ($searchTerm): ?>
                    <a href="?" class="clear-search" title="Clear search">×</a>
                <?php endif ?>

                <!-- Preserve filters in search -->
                <?php if ($activeGroup): ?>
                    <input type="hidden" name="group" value="<?= html($activeGroup) ?>">
                <?php endif ?>
                <?php if ($activeTheme): ?>
                    <input type="hidden" name="theme" value="<?= html($activeTheme) ?>">
                <?php endif ?>
            </form>
        </div>
    <?php endif ?>

    <!-- Filters -->
    <?php if ($showFilters && (!empty($groups) || !empty($themes))): ?>
        <div class="explorer-filters">

            <?php if (!empty($groups)): ?>
                <div class="filter-group">
                    <h3>By Content Group</h3>
                    <div class="filter-buttons">
                        <a href="?" class="filter-button <?= !$activeGroup ? 'active' : '' ?>">
                            All
                        </a>
                        <?php foreach ($groups as $key => $def): ?>
                            <a href="?group=<?= $key ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>"
                               class="filter-button <?= $activeGroup === $key ? 'active' : '' ?>">
                                <?php if (isset($def['emoji'])): ?>
                                    <span class="emoji"><?= $def['emoji'] ?></span>
                                <?php endif ?>
                                <?= $def['label'] ?>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if (!empty($themes)): ?>
                <div class="filter-group">
                    <h3>By Theme</h3>
                    <div class="filter-buttons">
                        <a href="?" class="filter-button <?= !$activeTheme ? 'active' : '' ?>">
                            All
                        </a>
                        <?php foreach ($themes as $key => $def): ?>
                            <a href="?theme=<?= $key ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>"
                               class="filter-button <?= $activeTheme === $key ? 'active' : '' ?>">
                                <?php if (isset($def['icon'])): ?>
                                    <span class="icon"><?= $def['icon'] ?></span>
                                <?php endif ?>
                                <?= $def['label'] ?>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

        </div>
    <?php endif ?>

    <!-- Tags List -->
    <div class="explorer-tags">
        <?php if (empty($tags)): ?>
            <p class="no-results">
                <?php if ($searchTerm): ?>
                    No tags found matching "<?= html($searchTerm) ?>".
                <?php elseif ($activeGroup || $activeTheme): ?>
                    No tags found in this category.
                <?php else: ?>
                    No tags found.
                <?php endif ?>
            </p>
        <?php else: ?>

            <!-- Sort Toggle -->
            <div class="explorer-controls">
                <label for="tag-sort">Sort:</label>
                <select id="tag-sort" onchange="window.location.href = '?sort=' + this.value<?= $activeGroup ? '&group=' . $activeGroup : '' ?><?= $activeTheme ? '&theme=' . $activeTheme : '' ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?>">
                    <option value="count" <?= $sortBy !== 'alpha' ? 'selected' : '' ?>>
                        By Popularity
                    </option>
                    <option value="alpha" <?= $sortBy === 'alpha' ? 'selected' : '' ?>>
                        Alphabetically
                    </option>
                </select>
            </div>

            <!-- Tag List -->
            <ul class="tag-list">
                <?php
                $displayTags = $limit > 0 ? array_slice($tags, 0, $limit, true) : $tags;
                foreach ($displayTags as $tag => $count):
                ?>
                    <li>
                        <?php snippet('tag-garden/badge', [
                            'tag' => $tag,
                            'count' => $count,
                            'showCount' => true,
                        ]) ?>
                    </li>
                <?php endforeach ?>
            </ul>

            <?php if ($limit > 0 && count($tags) > $limit): ?>
                <div class="explorer-footer">
                    <p>
                        Showing <?= $limit ?> of <?= count($tags) ?> tags.
                        <a href="<?= url('tags') ?>">View all tags →</a>
                    </p>
                </div>
            <?php endif ?>

        <?php endif ?>
    </div>

</div>
