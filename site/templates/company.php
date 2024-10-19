<h1><?= $page->title() ?></h1>
<div><?= $page->description()->kirbytext() ?></div>

<h2>Projects</h2>
<ul>
<?php foreach($site->find('work')->grandChildren()->filterBy('company', $page->id()) as $project): ?>
  <li><a href="<?= $project->url() ?>"><?= $project->title() ?></a></li>
<?php endforeach ?>
</ul>
