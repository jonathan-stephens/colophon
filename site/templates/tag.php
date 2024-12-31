<?php snippet('header') ?>

<h1>Posts tagged: <?= html($tag) ?></h1>

<?php
$taggedPosts = page('blog')->children()->filterBy('tags', $tag, ',');
foreach ($taggedPosts as $post):
?>
    <article>
        <h2><a href="<?= $post->url() ?>"><?= $post->title() ?></a></h2>
        <!-- Add more post details as needed -->
    </article>
<?php endforeach ?>

<?php snippet('footer') ?>
