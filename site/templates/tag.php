<?php snippet('header') ?>

<h1>Content tagged: <?= html($tag) ?></h1>

<?php
$taggedContent = site()
    ->index()
    ->listed()
    ->filterBy('tags', '*=', $tag);

if ($taggedContent->count()): ?>
    <?php foreach ($taggedContent as $item): ?>
        <article>
            <h2><a href="<?= $item->url() ?>"><?= $item->title() ?></a></h2>
        </article>
    <?php endforeach ?>
<?php else: ?>
    <p>No content found with this tag.</p>
<?php endif ?>

<?php snippet('footer') ?>
