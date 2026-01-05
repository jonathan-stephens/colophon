<?php
/**
 * Tags Index Template
 *
 * Displays all tags in a cloud/list view with filtering options.
 * Variables provided by controller (controllers/tags.php):
 * - $tags, $sortedTags, $minCount, $maxCount
 * - $groups, $themes, $sortMethods
 * - $recentlyTended, $notablePages, $popularTags
 *
 * @version 1.0.0
 */

snippet('site-header') ?>

<div class="tags-index-page">

    <!-- Page Header -->
    <header class="page-header">
        <h1><?= $page->title()->html() ?></h1>

        <?php if ($page->intro()->isNotEmpty()): ?>
            <div class="intro">
                <?= $page->intro()->kirbytext() ?>
            </div>
        <?php endif ?>

        <!-- Statistics -->
        <div class="tags-stats">
            <span class="stat">
                <strong><?= $totalTags ?></strong>
                <?= $totalTags === 1 ? 'tag' : 'tags' ?>
            </span>
            <span class="stat-separator">·</span>
            <span class="stat">
                <strong><?= $totalTaggedPages ?></strong>
                <?= $totalTaggedPages === 1 ? 'page' : 'pages' ?>
            </span>
        </div>
    </header>

    <!-- Filters -->
    <?php if (!empty($groups) || !empty($themes)): ?>
        <aside class="filters">

            <?php if (!empty($groups)): ?>
                <div class="filter-group">
                    <h3>Filter by Group</h3>
                    <ul class="filter-list">
                        <li>
                            <a href="<?= url('tags') ?>"
                               class="<?= !$group ? 'active' : '' ?>">
                                All Groups
                            </a>
                        </li>
                        <?php foreach ($groups as $key => $def): ?>
                            <li>
                                <a href="<?= url('tags', ['params' => ['group' => $key]]) ?>"
                                   class="<?= $isActiveGroup($key) ? 'active' : '' ?>">
                                    <?php if (isset($def['emoji'])): ?>
                                        <span class="emoji"><?= $def['emoji'] ?></span>
                                    <?php endif ?>
                                    <?= $def['label'] ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <?php if (!empty($themes)): ?>
                <div class="filter-group">
                    <h3>Filter by Theme</h3>
                    <ul class="filter-list">
                        <li>
                            <a href="<?= url('tags') ?>"
                               class="<?= !$theme ? 'active' : '' ?>">
                                All Themes
                            </a>
                        </li>
                        <?php foreach ($themes as $key => $def): ?>
                            <li>
                                <a href="<?= url('tags', ['params' => ['theme' => $key]]) ?>"
                                   class="<?= $isActiveTheme($key) ? 'active' : '' ?>">
                                    <?php if (isset($def['icon'])): ?>
                                        <span class="icon"><?= $def['icon'] ?></span>
                                    <?php endif ?>
                                    <?= $def['label'] ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <!-- Clear Filters -->
            <?php if ($activeFilter): ?>
                <div class="filter-actions">
                    <a href="<?= url('tags') ?>" class="clear-filters">
                        Clear Filters
                    </a>
                </div>
            <?php endif ?>

        </aside>
    <?php endif ?>

    <!-- Tag Cloud -->
    <section class="tags-cloud">
        <h2>Explore Tags</h2>

        <?php if (empty($sortedTags)): ?>
            <p class="no-tags">No tags found. <?php if ($activeFilter): ?>Try clearing filters.<?php endif ?></p>
        <?php else: ?>

            <!-- Tag Sort Options -->
            <div class="tag-sort-options">
                <label for="tag-sort">Sort:</label>
                <select id="tag-sort" onchange="window.location.href = '<?= url('tags') ?>?tagSort=' + this.value<?= $group ? ' + \'&group=' . $group . '\'' : '' ?><?= $theme ? ' + \'&theme=' . $theme . '\'' : '' ?>">
                    <option value="count" <?= get('tagSort') !== 'alpha' ? 'selected' : '' ?>>
                        By Popularity
                    </option>
                    <option value="alpha" <?= get('tagSort') === 'alpha' ? 'selected' : '' ?>>
                        Alphabetically
                    </option>
                </select>
            </div>

            <ul class="tag-list">
                <?php foreach ($sortedTags as $tag => $count): ?>
                    <?php
                        $fontSize = $getTagFontSize($count);
                        $tagUrl = $getTagUrl($tag);
                    ?>
                    <li class="tag-item" style="font-size: <?= $fontSize ?>rem">
                        <a href="<?= $tagUrl ?>" class="tag-link">
                            <span class="tag-name"><?= html($tag) ?></span>
                            <span class="tag-count">(<?= $count ?>)</span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>

        <?php endif ?>
    </section>

    <!-- Sidebar: Featured Content -->
    <aside class="sidebar">

        <!-- Popular Tags -->
        <?php if (!empty($popularTags)): ?>
            <section class="popular-tags">
                <h3>Popular Tags</h3>
                <ul>
                    <?php foreach (array_slice($popularTags, 0, 5, true) as $tag => $count): ?>
                        <li>
                            <a href="<?= $getTagUrl($tag) ?>">
                                <?= html($tag) ?>
                                <span class="count">(<?= $count ?>)</span>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>

        <!-- Recently Tended -->
        <?php if ($recentlyTended->count() > 0): ?>
            <section class="recently-tended">
                <h3>Recently Updated</h3>
                <ul>
                    <?php foreach ($recentlyTended as $item): ?>
                        <li>
                            <a href="<?= $item->url() ?>">
                                <?= $item->title()->html() ?>
                            </a>
                            <?php if ($item->last_tended()->isNotEmpty()): ?>
                                <time datetime="<?= $item->last_tended()->toDate('c') ?>">
                                    <?= $item->last_tended()->toDate('M j, Y') ?>
                                </time>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>

        <!-- Notable Pages -->
        <?php if ($notablePages->count() > 0): ?>
            <section class="notable-pages">
                <h3>Featured Content</h3>
                <ul>
                    <?php foreach ($notablePages as $item): ?>
                        <li>
                            <a href="<?= $item->url() ?>">
                                <?= $item->title()->html() ?>
                            </a>
                            <?php if ($item->growth_status()->isNotEmpty()): ?>
                                <?php $status = \TagGarden\Helpers::getGrowthDefinition($item->growth_status()->value()) ?>
                                <?php if ($status): ?>
                                    <span class="growth-status" title="<?= $status['label'] ?>">
                                        <?= $status['emoji'] ?>
                                    </span>
                                <?php endif ?>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>

    </aside>

</div>

<style>
/* Basic styling - customize to match your site */
.tags-index-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    margin-bottom: 2rem;
}

.tags-stats {
    margin-top: 1rem;
    color: #666;
}

.stat-separator {
    margin: 0 0.5rem;
}

.filters {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-group:last-child {
    margin-bottom: 0;
}

.filter-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.filter-list a {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.filter-list a:hover {
    background: #e9ecef;
}

.filter-list a.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.tag-sort-options {
    margin-bottom: 1rem;
}

.tag-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    line-height: 2;
}

.tag-item {
    display: inline-block;
}

.tag-link {
    text-decoration: none;
    color: #007bff;
    transition: color 0.2s;
}

.tag-link:hover {
    color: #0056b3;
}

.tag-count {
    font-size: 0.75em;
    color: #666;
}

.sidebar {
    margin-top: 3rem;
}

.sidebar section {
    margin-bottom: 2rem;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar li {
    margin-bottom: 0.5rem;
}

.sidebar time {
    display: block;
    font-size: 0.875rem;
    color: #666;
}

.no-tags {
    padding: 2rem;
    text-align: center;
    color: #666;
}
</style>

<?php snippet('site-footer') ?>
