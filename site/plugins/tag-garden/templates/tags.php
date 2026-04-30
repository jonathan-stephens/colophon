<?php
/**
 * Tags Index Template
 *
 * Displays all tags in a list view with filtering options.
 *
 * Variables provided by controller (v2.0):
 * - $tags: Array of tag => count (already sorted)
 * - $totalTags: Total number of tags
 * - $totalTaggedPages: Total number of pages with tags
 * - $groupFilter: Current group filter (if any)
 * - $growthFilter: Current growth status filter (if any)
 * - $tagSort: Current sort method ('count' or 'alpha')
 * - $groups: Available content groups
 * - $growthStatuses: Available growth statuses
 * - $recentlyTended: Recently updated pages
 * - $recentlyPlanted: Recently created pages
 * - Helper functions: $getTagUrl, $isActiveGroup, $isActiveGrowth
 *
 * @version 2.0.0
 */

use jonathanstephens\TagGarden\Helpers;

snippet('site-header') ?>

<div class="wrapper">

    <!-- Page Header -->
    <?php snippet('layout/container/header') ?>

    <!-- Filters -->
    <?php if (!empty($groups) || !empty($growthStatuses)): ?>
        <aside class="cluster">
            <!-- Tag Filter Options -->
            <section class="filters" aria-label="Filter tags">
                <h2>Filter</h2>
                <div class="controls cluster">
                    <!-- Group Filter -->
                    <?php if (!empty($groups)): ?>
                        <?php
                            $activeGroupLabel = $groupFilter && isset($groups[$groupFilter])
                                ? $groups[$groupFilter]['label']
                                : null;

                            // Build "All" href: clear group, keep growth if set
                            $groupAllHref = $growthFilter
                                ? url('tags') . '?growth=' . htmlspecialchars($growthFilter)
                                : url('tags');
                        ?>
                        <div class="filter-dropdown">
                            <button
                                type="button"
                                id="btn-group-filter"
                                class="filter-trigger <?= $activeGroupLabel ? 'filter-trigger--active' : '' ?>"
                                popovertarget="popover-group"
                                aria-expanded="false"
                                aria-haspopup="listbox"
                                aria-controls="popover-group">
                                <span class="filter-trigger__label">
                                    <?= $activeGroupLabel ? $activeGroupLabel : 'All Types' ?>
                                </span>
                                <span class="filter-trigger__chevron" aria-hidden="true">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
                                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>
                            <ul
                                id="popover-group"
                                popover="auto"
                                role="listbox"
                                aria-labelledby="btn-group-filter"
                                class="filter-popover">
                                <li role="option" aria-selected="<?= !$groupFilter ? 'true' : 'false' ?>">
                                    <a
                                        href="<?= $groupAllHref ?>"
                                        class="filter-option <?= !$groupFilter ? 'filter-option--active' : '' ?>"
                                        <?= !$groupFilter ? 'aria-current="true"' : '' ?>>
                                        All Types
                                    </a>
                                </li>
                                <?php foreach ($groups as $key => $def): ?>
                                    <?php
                                        $groupHref = '?group=' . htmlspecialchars($key)
                                            . ($growthFilter ? '&growth=' . htmlspecialchars($growthFilter) : '');
                                    ?>
                                    <li role="option" aria-selected="<?= $isActiveGroup($key) ? 'true' : 'false' ?>">
                                        <a
                                            href="<?= $groupHref ?>"
                                            class="filter-option <?= $isActiveGroup($key) ? 'filter-option--active' : '' ?>"
                                            <?= $isActiveGroup($key) ? 'aria-current="true"' : '' ?>>
                                            <?php if (isset($def['icon'])): ?>
                                                <span class="filter-option__emoji" aria-hidden="true"><?= $def['icon'] ?></span>
                                            <?php endif ?>
                                            <?= $def['label'] ?>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <!-- Growth Status Filter -->
                    <?php if (!empty($growthStatuses)): ?>
                        <?php
                            $activeGrowthLabel = $growthFilter && isset($growthStatuses[$growthFilter])
                                ? $growthStatuses[$growthFilter]['label']
                                : null;

                            // Build "All" href: clear growth, keep group if set
                            $growthAllHref = $groupFilter
                                ? url('tags') . '?group=' . htmlspecialchars($groupFilter)
                                : url('tags');
                        ?>
                        <div class="filter-dropdown">
                            <button
                                type="button"
                                id="btn-growth-filter"
                                class="filter-trigger <?= $activeGrowthLabel ? 'filter-trigger--active' : '' ?>"
                                popovertarget="popover-growth"
                                aria-expanded="false"
                                aria-haspopup="listbox"
                                aria-controls="popover-growth">
                                <span class="filter-trigger__label">
                                    <?= $activeGrowthLabel ? $activeGrowthLabel : 'All Growth Stages' ?>
                                </span>
                                <span class="filter-trigger__chevron" aria-hidden="true">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
                                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>
                            <ul
                                id="popover-growth"
                                popover="auto"
                                role="listbox"
                                aria-labelledby="btn-growth-filter"
                                class="filter-popover">
                                <li role="option" aria-selected="<?= !$growthFilter ? 'true' : 'false' ?>">
                                    <a
                                        href="<?= $growthAllHref ?>"
                                        class="filter-option <?= !$growthFilter ? 'filter-option--active' : '' ?>"
                                        <?= !$growthFilter ? 'aria-current="true"' : '' ?>>
                                        All Growth Stages
                                    </a>
                                </li>
                                <?php foreach ($growthStatuses as $key => $def): ?>
                                    <?php
                                        $growthHref = '?growth=' . htmlspecialchars($key)
                                            . ($groupFilter ? '&group=' . htmlspecialchars($groupFilter) : '');
                                    ?>
                                    <li role="option" aria-selected="<?= $isActiveGrowth($key) ? 'true' : 'false' ?>">
                                        <a
                                            href="<?= $growthHref ?>"
                                            class="filter-option <?= $isActiveGrowth($key) ? 'filter-option--active' : '' ?>"
                                            <?= $isActiveGrowth($key) ? 'aria-current="true"' : '' ?>>
                                            <?php if (isset($def['icon'])): ?>
                                                <span class="filter-option__emoji" aria-hidden="true"><?= $def['icon'] ?></span>
                                            <?php endif ?>
                                            <?= $def['label'] ?>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>
                </div>
            </section>

            <!-- Tag Sort Options -->
            <section class="tag-sort-options">
                <label for="tag-sort"><h2>Sort</h2></label>
                <select id="tag-sort" onchange="window.location.href = '<?= url('tags') ?>?tagSort=' + this.value<?= $groupFilter ? ' + \'&group=' . $groupFilter . '\'' : '' ?><?= $growthFilter ? ' + \'&growth=' . $growthFilter . '\'' : '' ?>">
                    <option value="count" <?= $tagSort !== 'alpha' ? 'selected' : '' ?>>
                        Numerically
                    </option>
                    <option value="alpha" <?= $tagSort === 'alpha' ? 'selected' : '' ?>>
                        Alphabetically
                    </option>
                </select>
            </section>
        </aside>
    <?php endif ?>

    <!-- Tag List -->
    <section class="tags-cloud">
        <?php if (empty($tags)): ?>
            <p class="no-tags">
                No tags found.
                <?php if ($groupFilter || $growthFilter): ?>
                    Try clearing filters.
                <?php endif ?>
            </p>
        <?php else: ?>
            <!-- Tag List -->
            <ul class="tags-cloud">
            <?php foreach ($tags as $tag => $count): ?>
                <li class="tag-item">
                    <a href="<?= $getTagUrl($tag) ?>" rel="tag" class="p-category button">
                        <span class="tag-name"><?= html($tag) ?></span>
                        <span class="count"><?= $count ?></span>
                    </a>
                </li>
            <?php endforeach ?>
            </ul>

        <?php endif ?>
    </section>

    <!-- Sidebar: Featured Content -->
    <aside class="sidebar">

        <!-- Recently Planted -->
        <?php if ($recentlyPlanted->count() > 0): ?>
            <section class="recently-planted">
                <h3>Recently Planted</h3>
                <ul>
                    <?php foreach ($recentlyPlanted as $item): ?>
                        <li>
                            <a href="<?= $item->url() ?>">
                                <?= $item->title()->html() ?>
                            </a>
                            <?php if ($item->date_planted()->isNotEmpty()): ?>
                                <time datetime="<?= $item->date_planted()->toDate('c') ?>">
                                    <?= $item->date_planted()->toDate('M j, Y') ?>
                                </time>
                            <?php endif ?>
                            <?php if ($item->Growthstatus()->isNotEmpty()): ?>
                                <?php $status = Helpers::getGrowthDefinition($item->Growthstatus()->value()) ?>
                                <?php if ($status): ?>
                                    <span class="growth-status" title="<?= $status['label'] ?>">
                                        <?= $status['icon'] ?>
                                    </span>
                                <?php endif ?>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </section>
        <?php endif ?>

        <!-- Recently Tended -->
        <?php if ($recentlyTended->count() > 0): ?>
            <section class="recently-tended">
                <h3>Recently Tended</h3>
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
                            <?php if ($item->Growthstatus()->isNotEmpty()): ?>
                                <?php $status = Helpers::getGrowthDefinition($item->Growthstatus()->value()) ?>
                                <?php if ($status): ?>
                                    <span class="growth-status" title="<?= $status['label'] ?>">
                                        <?= $status['icon'] ?>
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

<?php snippet('site-footer') ?>