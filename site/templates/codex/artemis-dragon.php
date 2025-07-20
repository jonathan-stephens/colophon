<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
  <?php snippet('head/title') ?>
  <?php snippet('head/meta') ?>
  <?php snippet('head/endpoints') ?>
  <?php snippet('head/feeds') ?>
  <?php snippet('head/timekeeper') ?>


  <style type="text/css">
  /* FONTS */
  @font-face {
    font-family: 'IBM Plex Mono';
    font-weight: 400;
    font-style: normal;
    size-adjust:105%;
    font-display: fallback;
    src:
      url("../fonts/ibm-plex-mono/mono-text.woff2") format("woff2"),
      url("../fonts/ibm-plex-mono/mono-text.woff") format("woff");
    @supports (font-size-adjust: .514){
      font-size-adjust: .514;
    }
  }
  @font-face {
    font-family: 'IBM Plex Mono';
    font-weight: 500;
    font-style: normal;
    size-adjust:105%;
    font-display: fallback;
    src:
      url("../fonts/ibm-plex-mono/mono-medium.woff2") format("woff2"),
      url("../fonts/ibm-plex-mono/mono-medium.woff") format("woff");
    @supports (font-size-adjust: .514){
      font-size-adjust: .514;
    }
  }
  @font-face {
    font-family: 'IBM Plex Mono';
    font-weight: 700;
    font-style: normal;
    size-adjust:105%;
    font-display: fallback;
    src:
      url("../fonts/ibm-plex-mono/mono-semibold.woff2") format("woff2"),
      url("../fonts/ibm-plex-mono/mono-semibold.woff2") format("woff");
    @supports (font-size-adjust: .514){
      font-size-adjust: .514;
    }
  }
  @font-face {
    font-family: 'IBM Plex Mono';
    font-weight: 800;
    size-adjust:105%;
    font-style: normal;
    font-display: fallback;
    src:
      url("../fonts/ibm-plex-mono/mono-bold.woff2") format("woff2"),
      url("../fonts/ibm-plex-mono/mono-bold.woff") format("woff");
    @supports (font-size-adjust: .514){
      font-size-adjust: .514;
    }
  }
  </style>
  <?= css([
    'assets/css/artemis.css',
  ]) ?>
</head>
<body class="artemis colorPage" data-color-mode="system" data-theme="design">
<a href="#main" class="skip-link visually-hidden">Skip to main content</a>

  <header class="">
    <nav href="#">
      <a href="#" title="Link to Artemis Color System's Home">
        Home
      </a>

      <?php /*

      // get the first set of subpages which should be used
      $subpages = $pages->find('artemis')->children();

      // create the snippet beginning with those subpages
      snippet('treemenu', ['subpages' => $subpages]);

      ?>

      <?php snippet('treemenu') ?>

    </nav>
     */ ?>
    <h1>
      Brand Blue
    </h1>

    <?php snippet('/components/theme-picker') ?>

  </header>

  <main id="main">

    <section class="color-information">
      <div class="naming">

      </div>
      <div class="role">

        <div class="swatch var-name var-value">
          <h3>
            $swatch-token(name.value)</h3>
          <p>$swatch.description
            Primary action color. Used for default link state and primary action buttons.</p>
          <div class="examples">
            <h4>Examples</h4>
            <a href="#" class="link at-rest">
              Click me! I'm a link you can click.
            </a>
            <a href="#" class="button">
              I'm an important big blue button. Buy now!
            </a>
          </div>
        </div>
        <div class="swatch var-name var-value">
          <h3>
            $swatch-token(name.value)</h3>
          <p>$swatch.description
            Primary action color. Used for default link state and primary action buttons.</p>
          <div class="examples">
            <h4>Examples</h4>
            <a href="#" class="link at-rest">
              Click me! I'm a link you can click.
            </a>
            <a href="#" class="button">
              I'm an important big blue button. Buy now!
            </a>
          </div>
        </div>
        <div class="swatch var-name var-value">
          <h3>
            $swatch-token(name.value)</h3>
          <p>$swatch.description
            Primary action color. Used for default link state and primary action buttons.</p>
          <div class="examples">
            <h4>Examples</h4>
            <a href="#" class="link at-rest">
              Click me! I'm a link you can click.
            </a>
            <a href="#" class="button">
              I'm an important big blue button. Buy now!
            </a>
          </div>
        </div>
      </div>
      <div class="palette-overview">
      </div>

      <section class="color-information">
        <div class="color-description">
          <h2>Brand blue is one of the two brand colors for Lunar Acoustic.</h2>
        </div>
        <div class="color-poetic">
          <p>Poetic Naming: Blue because the moon shines at night; from light-blue of day to dark blue of night.</p>
        </div>
        <div class="color-role">
          <h2>Color Roles</h2>
          <p>Each color has a role when we use them. Brand Blue should be used for primary actions or elements that communicate the Lunar Acoustic brand.</p>

          <div class="swatch"></div>
          <div class="primary"></div>
          <div class="example"></div>
          <div class="token"></div>
        </div>
        <div class="color-overview">
          <h2>Inverse Colors & Dark Mode</h2>
          <p>
            Color families have 10 swatches in each palette. If the swatches are divided in half, each half becomes a mirror. If using a swatch as a background, pair appropriate text-color from the same color family.
          </p>
          <img src="https://placekittens.com/200/300">
          <p>
            For example, if a button color is 700 in the light theme, it will be 400 in the dark theme. If a section background is 100 in a light theme, it'll be 1000 in the dark theme.
          </p>
        </div>
      </section>

    </section>
    <section class="color-default">
      <div class="swatch">
        <p>
          $token-number
        </p>
      </div>
      <h2>
        $purpose</br>
        <?php $page->colorName()->html() ?>
      </h2>
    </section>

    <section class="color-values">
      <div class="color-default"></div>
      <div class="color-swatch">500</div>
      <dl class="color-codes">
        <dt>$color.format</dt>
        <dd>$color.value</dd>
        <dt>$color.format</dt>
        <dd>$color.value</dd>
      </dl>
      <dl class="color-contrast">
        <dt>$background.for</dt>
        <dd>$values.for.background</dd>
        <dt>$foreground.for</dt>
        <dd>$values.for.foreground</dd>
        <dt>$with.white</dt>
        <dd>$values.for.white</dd>
        <dt>$with.black</dt>
        <dd>$values.for.black</dd>
      </dl>
    </section>


  <script>

  </script>
