<?php
/**
 * Single Tag Template - Unified Timeline
 *
 * Displays content filtered by one or more tags in a single timeline.
 * Users can filter by content group and sort results.
 *
 * Variables provided by controller/route (v2.0):
 * - $filterTags: Array of tag names being filtered
 * - $pages: Filtered pages collection
 * - $relatedTags: Array of related tags with counts
 * - $tagCount: Total number of results
 * - $sort: Current sort method
 * - $groupFilter: Current group filter (if any)
 * - $groupStats: Group statistics with counts
 * - $growthStats: Growth status statistics
 * - Helper functions: $getTagUrl, $getCombinedTagUrl, $isActiveSort, $isActiveGroup, $getGrowthDefinition
 *
 * SVG Icons:
 * Add your content type icons as SVG symbols in your site's header/footer.
 * Icons are referenced as #icon-{template-name}
 * Example: <symbol id="icon-article">...</symbol>
 *
 * @version 2.0.0
 */

use jonathanstephens\TagGarden\Helpers;

snippet('site-header') ?>

<div class="tag-page wrapper">

    <!-- Page Header -->
    <header class="page-header">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?= url('tags') ?>">← All Tags</a>
        </nav>
        
        <!-- Title -->
        <h1>
            <?php if (count($filterTags) === 1): ?>
                <span class="tag-prefix">Tag:</span>
                <?= html($filterTags[0]) ?>
            <?php else: ?>
                <span class="tag-prefix">Tags:</span>
                <?= html(implode(' + ', $filterTags)) ?>
            <?php endif ?>
        </h1>
        
        <!-- Statistics -->
        <p class="tag-stats">
            <span class="stat">
                <?= $tagCount ?>
                <?= $tagCount === 1 ? 'result' : 'results' ?>
            </span>
            <?php if ($tagCount > 0 && $groupFilter): ?>
                <?php 
                    $activeGroupData = isset($groupStats[$groupFilter]) ? $groupStats[$groupFilter] : null;
                    $activeGroupDef = ($activeGroupData && isset($activeGroupData['def'])) ? $activeGroupData['def'] : null;
                ?>
                <?php if ($activeGroupDef && !empty($activeGroupDef['label'])): ?>
                    <span class="stat-separator">·</span>
                    <span class="stat">
                        in <?= $activeGroupDef['label'] ?>
                    </span>
                <?php endif ?>
            <?php endif ?>
        </p>
    </header>

    <!-- Main Content - Unified Timeline -->
    <main class="tag-content">
        
        <!-- Group Filters -->
        <?php if (!empty($groupStats)): ?>
            <nav class="group-filters">
                <h2>Filter by Type</h2>
                <ul class="filter-list">
                    <li>
                        <a href="?<?= http_build_query(array_filter(['sort' => $sort !== 'tended' ? $sort : null])) ?>"
                           class="filter-button <?= !$groupFilter ? 'active' : '' ?>">
                            All
                            <span class="count">(<?= $tagCount ?>)</span>
                        </a>
                    </li>
                    <?php foreach ($groupStats as $groupKey => $groupData): ?>
                        <?php
                            $def = isset($groupData['def']) ? $groupData['def'] : [];
                            $count = isset($groupData['count']) ? $groupData['count'] : 0;
                            $label = !empty($def['label']) ? $def['label'] : ucfirst($groupKey);
                            $emoji = !empty($def['emoji']) ? $def['emoji'] : '';
                        ?>
                        <li>
                            <a href="?<?= http_build_query(array_filter(['group' => $groupKey, 'sort' => $sort !== 'tended' ? $sort : null])) ?>"
                               class="filter-button <?= $isActiveGroup($groupKey) ? 'active' : '' ?>">
                                <?php if ($emoji): ?>
                                    <span class="emoji"><?= $emoji ?></span>
                                <?php endif ?>
                                <?= $label ?>
                                <span class="count">(<?= $count ?>)</span>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </nav>
        <?php endif ?>

        <!-- Sort Options -->
        <?php if ($tagCount > 1): ?>
            <div class="sort-options">
                <label for="sort-select">Sort by:</label>
                <select id="sort-select" onchange="window.location.href = '?<?= http_build_query(array_filter(['group' => $groupFilter, 'sort' => ''])) ?>' + this.value">
                    <?php foreach ($sortMethods as $key => $method): ?>
                        <?php
                            // Handle both old format (array with 'label') and new format (string)
                            $label = is_array($method) ? ($method['label'] ?? $key) : $method;
                        ?>
                        <option value="<?= $key ?>" <?= $isActiveSort($key) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endif ?>

        <?php if (!isset($pages) || $pages->count() === 0): ?>
            <!-- No Results -->
            <div class="no-results">
                <p>No content found<?= $groupFilter ? ' in this group' : '' ?> with <?= count($filterTags) === 1 ? 'this tag' : 'these tags' ?>.</p>
                <?php if ($groupFilter): ?>
                    <a href="?<?= http_build_query(array_filter(['sort' => $sort !== 'tended' ? $sort : null])) ?>" class="button">
                        Show All Groups
                    </a>
                <?php endif ?>
                <a href="<?= url('tags') ?>" class="button">Explore All Tags</a>
            </div>

        <?php else: ?>

            <!-- Unified Timeline -->
            <div class="timeline">
                <?php foreach ($pages as $item): ?>
                    <?php
                        $template = $item->intendedTemplate()->name();
                        $group = $item->contentGroup() ?? 'other';
                        $groupDef = Helpers::getGroupDefinition($group);
                    ?>

                    <article class="timeline-item h-entry" data-template="<?= $template ?>" data-group="<?= $group ?>">

                        <!-- Content Type Badge -->
                        <div class="content-type-badge">
                            <svg class="content-type-icon content-type-<?= $template ?>" width="16" height="16" aria-hidden="true">
                                <use href="#icon-<?= $template ?>"></use>
                            </svg>
                            <span class="content-type-label"><?= ucfirst($template) ?></span>
                            <?php if ($groupDef && !empty($groupDef['emoji'])): ?>
                                <span class="content-group-label" title="<?= $groupDef['label'] ?? '' ?>">
                                    <?= $groupDef['emoji'] ?>
                                </span>
                            <?php endif ?>
                        </div>

                        <!-- Link to Content -->
                        <a href="<?= $item->url() ?>" class="timeline-link">

                            <!-- Title -->
                            <h3 class="p-name"><?= $item->title()->html() ?></h3>

                            <!-- Dek (if exists) -->
                            <?php if ($item->dek()->isNotEmpty()): ?>
                                <p class="dek"><?= $item->dek()->html() ?></p>
                            <?php endif ?>

                        </a>

                        <!-- Metadata -->
                        <div class="item-meta">

                            <!-- Reading Time -->
                            <?php if ($item->wordCount() > 0): ?>
                                <span class="reading-time">
                                    <?= $item->readingTimeFormatted() ?>
                                </span>
                                <span class="meta-separator">·</span>
                            <?php endif ?>

                            <!-- Growth Status -->
                            <?php if ($item->Growthstatus()->isNotEmpty()): ?>
                                <?php $status = $getGrowthDefinition($item->Growthstatus()->value()) ?>
                                <?php if ($status): ?>
                                    <span class="growth-status" title="<?= $status['label'] ?>">
                                        <?= $status['emoji'] ?>
                                        <?= $status['label'] ?>
                                    </span>
                                    <span class="meta-separator">·</span>
                                <?php endif ?>
                            <?php endif ?>

                            <!-- Last Tended -->
                            <?php if ($item->last_tended()->isNotEmpty()): ?>
                                <time class="last-tended dt-updated" datetime="<?= $item->last_tended()->toDate('c') ?>">
                                    <?= $item->last_tended()->toDate('M j, Y') ?>
                                </time>
                            <?php endif ?>

                        </div>

                        <!-- Tags -->
                        <?php if ($item->tags()->isNotEmpty()): ?>
                            <footer class="meta">
                                <?php $pageTags = $item->tags()->split(',') ?>
                                <div class="cluster tags">
                                    <?php foreach ($pageTags as $tag): ?>
                                        <?php $tag = trim($tag) ?>
                                        <?php if (!empty($tag)): ?>
                                            <a href="<?= $getTagUrl($tag) ?>"
                                               rel="tag"
                                               class="button p-category <?= in_array(mb_strtolower($tag), array_map('mb_strtolower', $filterTags)) ? 'active' : '' ?>">
                                                <?= html($tag) ?>
                                            </a>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </div>
                            </footer>
                        <?php endif ?>

                    </article>
                <?php endforeach ?>
            </div>

        <?php endif ?>

    </main>

    <!-- Sidebar -->
    <aside class="sidebar">
        
                <!-- Combine Tags (Drill Down) -->
        <?php if (!empty($relatedTags)): ?>
            <section class="combine-tags">
                <h3>Combine tags, narrow results</h3>
                <ul class="tag-list">
                    <?php foreach ($relatedTags as $tag => $count): ?>
                        <li>
                            <a href="<?= $getCombinedTagUrl($tag) ?>"
                               rel="tag"
                               class="tag-link">
                                <?= html($tag) ?>
                                <span class="count">(<?= $count ?>)</span>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>

        <!-- Growth Status Stats -->
        <?php if (array_sum($growthStats) > 0): ?>
            <section class="growth-stats">
                <h3>Growth Status</h3>
                <ul class="stats-list">
                    <?php foreach ($growthStats as $status => $count): ?>
                        <?php if ($count > 0): ?>
                            <?php $statusDef = $getGrowthDefinition($status) ?>
                            <li>
                                <?php if ($statusDef): ?>
                                    <span class="status-emoji"><?= $statusDef['emoji'] ?></span>
                                    <span class="status-label"><?= $statusDef['label'] ?></span>
                                <?php else: ?>
                                    <span class="status-label"><?= ucfirst($status) ?></span>
                                <?php endif ?>
                                <span class="count"><?= $count ?></span>
                            </li>
                        <?php endif ?>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>

    </aside>

</div>

<?php snippet('site-footer') ?>