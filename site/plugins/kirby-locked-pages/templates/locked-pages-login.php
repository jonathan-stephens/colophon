<?php snippet('site-header') ?>

<section class="locked-dialog">
  <header>
    <h1>Password Protected</h1>
    <p>This page contains confidential information.<br/>Access granted by request.</p>
  </header>

  <form method="post" <?php if ($error): ?>class="error"<?php endif ?>>
    <label for="password">Enter password</label>
    <input type="password" id="password" name="password" value="<?= esc(get('password', '')) ?>">

    <?php if ($error): ?>
      <p class="error-text"><?= $error ?></p>
    <?php endif ?>

    <input type="hidden" name="csrf" value="<?= csrf() ?>">
    <button type="submit" class="with-icon"><?= asset('assets/svg/icons/locked.svg')->read() ?>Unlock page</button>
  </form>

  <footer>
    <h3>Want access?</h3>
    <p>Request the password by writing me at <a href="mailto:hello@jonathanstephens.us">hello@jonathanstephens.us</a>, including your name, the page you'd like to view, and why you need it.</p>
  </footer>
</section>



<?php snippet('site-footer') ?>
