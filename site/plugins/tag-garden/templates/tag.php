<?php
/**
 * Single Tag Template - Unified Timeline
 *
 * Displays content filtered by one or more tags in a single timeline.
 * Users can filter by content group and sort results.
 *
 * Variables provided by route:
 * - $filterTags, $pages (paginated), $allPages, $relatedTags
 * - $sort, $logic, $groupFilter, $tagCount
 * - $groupStats, $growthStats, $lengthStats
 *
 * SVG Icons:
 * Add your content type icons as SVG symbols in your site's header/footer.
 * Icons are referenced as #icon-{template-name}
 * Example: <symbol id="icon-article">...</symbol>
 *
 * @version 2.0.0
 */

snippet('site-header') ?>

<?php
// Write debug to file instead of output
if (isset($routeDebug)) {
    file_put_contents(
        kirby()->root('site') . '/debug-tag.txt',
        $routeDebug . "\n\n" .
        "=== TEMPLATE VARIABLES ===\n" .
        "filterTags: " . print_r($filterTags ?? 'NOT SET', true) . "\n" .
        "pages count: " . (isset($pages) ? $pages->count() : 'NOT SET') . "\n" .
        "tagCount: " . ($tagCount ?? 'NOT SET') . "\n" .
        "groupStats: " . (isset($groupStats) ? count($groupStats) . ' groups' : 'NOT SET') . "\n" .
        "typeStats: " . (isset($typeStats) ? count($typeStats) . ' types' : 'NOT SET') . "\n"
    );
}
?>
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
            <?php
            $hasFilters = ($groupFilter || $typeFilter || $autoDetectedGroup);
            if ($tagCount > 0 && $hasFilters):
            ?>                <?php
                    $activeGroupKey = $groupFilter ?? $autoDetectedGroup;
                    $activeGroupData = isset($groupStats[$activeGroupKey]) ? $groupStats[$activeGroupKey] : null;
                    $activeGroupDef = ($activeGroupData && isset($activeGroupData['def'])) ? $activeGroupData['def'] : null;
                ?>
                <?php if ($activeGroupDef && !empty($activeGroupDef['label'])): ?>
                    <span class="stat-separator">·</span>
                    <span class="stat">
                        in <?= $activeGroupDef['label'] ?>
                    </span>
                <?php endif ?>
                <?php if ($typeFilter): ?>
                    <span class="stat-separator">→</span>
                    <span class="stat">
                        <?= ucfirst($typeFilter) ?>
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
                         class="filter-button <?= !$groupFilter && !$typeFilter ? 'active' : '' ?>">
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
                      <option value="<?= $key ?>" <?= $isActiveSort($key) ? 'selected' : '' ?>>
                          <?= $method['label'] ?>
                      </option>
                  <?php endforeach ?>
              </select>
          </div>
      <?php endif ?>

      <?php if (!isset($tagPages) || $tagPages->count() === 0): ?>            <!-- No Results -->
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
              <?php foreach ($tagPages as $item): ?>
                  <?php
                        $template = $item->intendedTemplate()->name();
                        $group = $item->contentGroup() ?? 'other';
                        $groupDef = \Yourusername\TagGarden\Helpers::getGroupDefinition($group);
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
                            <?php if ($item->growth_status()->isNotEmpty()): ?>
                                <?php $status = $getGrowthDefinition($item->growth_status()->value()) ?>
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
                        <?php endif ?>
                      </footer>
                    </article>

                <?php endforeach ?>
            </div>

            <!-- Pagination -->
            <?php if ($tagPages->pagination() && $tagPages->pagination()->hasPages()): ?>
              <nav class="pagination" aria-label="Pagination">
                <?php $pagination = $tagPages->pagination() ?>

                    <?php if ($pagination->hasPrevPage()): ?>
                        <a href="?<?= http_build_query(array_filter(['group' => $groupFilter, 'sort' => $sort !== 'tended' ? $sort : null, 'page' => $pagination->prevPage()])) ?>"
                           class="pagination-link pagination-prev">
                            ← Previous
                        </a>
                    <?php endif ?>

                    <span class="pagination-info">
                        Page <?= $currentPage ?> of <?= $pagination->pages() ?>
                    </span>

                    <?php if ($pagination->hasNextPage()): ?>
                        <a href="?<?= http_build_query(array_filter(['group' => $groupFilter, 'sort' => $sort !== 'tended' ? $sort : null, 'page' => $pagination->nextPage()])) ?>"
                           class="pagination-link pagination-next">
                            Next →
                        </a>
                    <?php endif ?>
                </nav>
            <?php endif ?>

        <?php endif ?>

    </main>

    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Combine Tags (Drill Down) -->
        <?php if (!empty($relatedTags) && count($filterTags) === 1): ?>
            <section class="combine-tags">
                <h3>Combine tags, narrow results.</h3>
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

        <!-- Related Tags (for multiple tag searches) -->
        <?php if (!empty($relatedTags) && count($filterTags) > 1): ?>
            <section class="related-tags">
                <h3>Other tags in results</h3>
                <ul class="tag-list">
                    <?php foreach ($relatedTags as $tag => $count): ?>
                        <li>
                            <a href="<?= $getTagUrl($tag) ?>"
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

        <!-- Length Stats -->
        <?php if (array_sum($lengthStats) > 0): ?>
            <section class="length-stats">
                <h3>Content Length</h3>
                <ul class="stats-list">
                    <?php foreach ($lengthStats as $category => $count): ?>
                        <?php if ($count > 0): ?>
                            <li>
                                <span class="length-label"><?= $getLengthLabel($category) ?></span>
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
