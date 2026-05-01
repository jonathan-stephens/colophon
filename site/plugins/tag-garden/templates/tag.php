<?php
/**
 * Single Tag Template - Unified Timeline
 *
 * Displays content filtered by one or more tags in a single timeline.
 * Users can filter by content group and sort results.
 *
 * Variables provided by controller/route (v2.0):
 * - $filterTags: Array of tag names being filtered
 * - $taggedPages: Filtered pages collection
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

<div class="wrapper">
    <header>
        <h1>
            Tags
        </h1>
        
        <p class="tag-stats">
            <?= $tagCount ?>
            <?= $tagCount === 1 ? 'result' : 'results' ?> with 
            <?php if (count($filterTags) === 1): ?>
                <span class="tag-prefix">tag:</span>
                <?= html($filterTags[0]) ?>
            <?php else: ?>
                <span class="tag-prefix">tags:</span>
                <?= html(implode(' + ', $filterTags)) ?>
            <?php endif ?>
        </p>
    </header>

    <!-- Main Content - Unified Timeline -->
    <section class="tag-content">
        
<!-- Filters & Sort -->
<?php if (!empty($groupStats) || $tagCount > 1): ?>
    <section class="filters cluster">      
        <!-- Combine Tags (Drill Down) -->
        <?php if (!empty($relatedTags)): ?>
            <div class="combine-tags">
                <h2>Combine tags, narrow results.</h2>

                <ul class="tag-list cluster">

                    <!-- Active filter tags — click to remove -->
                    <?php foreach ($filterTags as $activeTag): ?>
                        <?php
                            $remaining = array_values(array_filter(
                                $filterTags,
                                fn($t) => $t !== $activeTag
                            ));
                            $removeUrl = empty($remaining)
                                ? url('tags')
                                : url('tags/' . implode(',', $remaining));
                        ?>
                        <li class="tag-item">
                            <a href="<?= $removeUrl ?>"
                            rel="tag"
                            class="tag-link p-category button active"
                            aria-label="Remove filter: <?= html($activeTag) ?>">
                                <span class="tag-name"><?= html($activeTag) ?></span>
                                <span class="tag-remove" aria-hidden="true">×</span>
                            </a>
                        </li>
                    <?php endforeach ?>

                    <!-- Related tags — click to add (AND) -->
                    <?php foreach ($relatedTags as $tag => $count): ?>
                        <li class="tag-item">
                            <a href="<?= $getCombinedTagUrl($tag) ?>"
                            rel="tag"
                            class="tag-link p-category button"
                            aria-label="Add filter: <?= html($tag) ?>">
                                <span class="tag-name"><?= html($tag) ?></span>
                                <span class="count"><?= $count ?></span>
                            </a>
                        </li>
                    <?php endforeach ?>

                </ul>
            </div>        
        <?php endif ?>
    </section>        
<?php endif ?>
        <?php if (!isset($taggedPages) || $taggedPages->count() === 0): ?>
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

            <!-- Unified Stream -->
            <div class="stream">
                <?php foreach ($taggedPages as $item): ?>
                    <?php
                        $template = $item->intendedTemplate()->name();
                        $group = $item->contentGroup() ?? 'other';
                        $groupDef = Helpers::getGroupDefinition($group);
                    ?>

                    <article class="stream-item h-entry" data-template="<?= $template ?>" data-group="<?= $group ?>">
                        <!-- Link to Content -->
                        <a href="<?= $item->url() ?>" class="stream-link e-content">

                            <!-- Metadata -->
                            <div class="item-meta">
                                <!-- Content Type Badge -->
                                <div class="content-type-badge with-icon">
                                    <span class="content-type-icon icon content-type-<?= $template ?>">
                                        <?= Helpers::getTemplateIcon($template) ?>
                                    </span>
                                    <p class="content-type-label"><?= ucfirst($template) ?></p>
                                </div>

                                <!-- Growth Status -->
                                <?php if ($item->Growthstatus()->isNotEmpty()): ?>
                                    <?php $status = $getGrowthDefinition($item->Growthstatus()->value()) ?>
                                    <?php if ($status): ?>
                                        <span class="growth-status" title="<?= $status['label'] ?>">
                                            <?= $status['icon'] ?>
                                            <?= $status['label'] ?>
                                        </span>
                                    <?php endif ?>
                                <?php endif ?>
                            </div>
                            <div class="item-content">
                                <!-- Title -->
                                <h3 class="p-name"><?= $item->title()->html() ?></h3>
                                <!-- Dek (if exists) -->
                                <?php if ($item->dek()->isNotEmpty()): ?>
                                    <p class="dek"><?= $item->dek()->html() ?></p>
                                <?php endif ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach ?>
            </div>

        <?php endif ?>

        </section>

    <!-- Sidebar -->
</div>

<?php snippet('site-footer') ?>