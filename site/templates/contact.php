<?php snippet('site-header') ?>

<section class="wrapper">
  <h1><?= $page->title()->html() ?></h1>
  <?php if ($success): ?>
    <p class="message success">
      <?= $success ?>
    </p>
  <?php else: ?>
    <?php if (isset($alert['error'])): ?>
      <p class="message error"><?= $alert['error'] ?></p>
    <?php endif ?>
    <form class="site-contact" method="post" action="<?= $page->url() ?>">
      <div class="honeypot">
        <label for="website">Website <abbr title="required">*</abbr></label>
        <input
          type="url"
          id="website"
          name="website"
          tabindex="-1"
          placeholder="Where could I learn more about you?">
      </div>
      <div class="field">
        <label for="name">
          Name <abbr title="required">*</abbr>
        </label>
        <input
          type="text"
          id="name"
          name="name"
          value="<?= esc($data['name'] ?? '', 'attr') ?>"
          placeholder="How should I call you?"
          required>
        <?= isset($alert['name']) ? '<span class="alert error">' . esc($alert['name']) . '</span>' : '' ?>
      </div>
      <div class="field">
        <label for="email">
          Email <abbr title="required">*</abbr>
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= esc($data['email'] ?? '', 'attr') ?>"
          placeholder="Where should I send my reply?"
          required>
        <?= isset($alert['email']) ? '<span class="alert error">' . esc($alert['email']) . '</span>' : '' ?>
      </div>
      <div class="field">
        <label for="text">
          Message <abbr title="required">*</abbr>
        </label>
        <textarea
          id="text"
          name="text"
          placeholder="Please share why you're contacting, in however many words you deem appropriate."
          required>
        </textarea>
        <?= isset($alert['text']) ? '<span class="alert error">' . esc($alert['text']) . '</span>' : '' ?>
      </div>
      <input type="submit" name="submit" value="Submit">
    </form>
  <?php endif ?>
</section>

<?php snippet('site-footer') ?>
