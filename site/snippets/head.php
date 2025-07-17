<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
  <?php snippet('head/title') ?>
  <?php snippet('head/meta') ?>
  <?php snippet('head/endpoints') ?>
  <?php snippet('head/feeds') ?>
  <?php snippet('head/timekeeper') ?>
  <?php snippet('head/styles') ?>

  <style>
  .site-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: var(--rhythm-moderato);
      display: flex;
      justify-content: space-between;
      transition: all 0.3s ease;
      align-items:center;
  }

  .site-header .button {
    --button-gap: var(--rhythm-vivace);
    --button-bg: oklch(12.43% 0.015 47.04 / .42); ;
    --button-border-color: transparent;
    --button-hover-color: var(--color-emphasis);
    --button-hover-border-color: var(--color-emphasis);
    --button-padding: var(--rhythm-vivace) var(--rhythm-allegro);

    backdrop-filter:blur(var(--rhythm-vivace));
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
    font-size: var(--caption);

    #nav-toggle-text, #preferences-toggle-text{
      em{
      opacity: 0;
      }
    }

    .icon{
      --button-icon-size: var(--prose);
      fill: var(--color-prominent);
      top:1px;
    }
  }

  .site-header .button:hover {
    em{
      opacity: 1;
    }
    .icon{
      fill: var(--color-emphasis);
    }
  }

  .brandmark {
    text-decoration: none;
    width:calc(1.25*var(--hed-primary));

    .background, .border {
      backdrop-filter:blur(200px);
      fill: oklch(12.43% 0.015 47.04 / .60);
    }
    .frills, .letter{
      fill:var(--color-prominent);
    }
  }

  .brandmark:hover, .brandmark:focus {
    .background, .border{
      fill: var(--background-primary);
      transition: all ease-in-out .1s;
    }
    box-shadow: var(--rhythm-andante) var(--rhythm-prestissimo) var(--rhythm-moderato) var(--background-primary);
  }

  .brandmark{
    .letter {
      fill: ;
    }
  }

  #nav-toggle-text, #preferences-toggle-text{
    align-items: baseline;
    font-style:var(--font-mono);

    em {
      display:none;
    }
  }

  #nav-toggle-text{
    align-items: flex-start;
  }

  #preferences-toggle-text{
    align-items: flex-end;
  }

  .panel {
      position: fixed;
      background: light-dark(var(--palette-shade-30), var(--palette-shade-80));
      border: var(--border-light) solid var(--border-color-distinct);
      box-shadow: var(--rhythm-andante) var(--rhythm-prestissimo) var(--rhythm-largo) var(--background-primary);
      transform: translateX(-100%);
      backdrop-filter: blur(var(--flow-13));
      transition: transform 0.15s .125s, opacity .5s;
      z-index: 999999;
      overflow: hidden;
      padding: var(--rhythm-moderato);
      min-height:100vh;
      border-inline-end: var(--border-fine) solid transparent;
  }

  .panel.open {
      transform: translateX(0);
  }

  .nav-panel {
      left: 0;
      width: 100%;
  }
  .nav-panel.open{

  }

  .preferences-panel {
      right: 0;
      width: 100%;
      transform: translateX(100%);
  }

  .preferences-panel.open {
      transform: translateX(0);
      box-shadow: calc(-1*var(--rhythm-andante)) calc(-1*var(--rhythm-prestissimo)) var(--rhythm-largo) var(--background-primary);
  }

  .panel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--border-color);
  }

  .panel-logo {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--accent-color);
      text-decoration: none;
  }

  .nav-panel[aria-hidden="true"] .nav-list{
    transform: translateY(1.5em);
    opacity: 0;
  }
  .nav-list {
      list-style: none;
  }

  .nav-panel.open .nav-item {
    opacity: 1;
    transform: none;
      margin-bottom: 0.5rem;
      font-size:var(--hed-tertiary);
      transition: opacity .3s,transform .3s;
      transition-delay: var(--item-delay);
  }

  .nav-link {
      display: block;
      color: var(--text-primary);
      text-decoration: none;
      border-radius: 6px;
      transition: all 0.2s ease;
      font-weight: 500;
  }

  .nav-link:hover, .nav-link:focus {
      background-color: var(--accent-color);
      color: white;
      outline: none;
  }

  .preference-section {
      margin-bottom: 2rem;
  }

  .preference-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--color-distinct);
  }

  .preference-options {
      display: flex;
      flex-direction: column;
      gap: var(--rhythm-vivace);
  }

  .preference-option {
      display: flex;
      align-items: center;
      gap: var(--rhythm-vivace);
  }

  .preference-option input[type="radio"] {
      width: 18px;
      height: 18px;
      accent-color: var(--color-prominent);
  }

  .preference-option label {
      font-size: 0.9rem;
      color: var(--color-distinct);
      cursor: pointer;
  }

  .overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--palette-shade-50);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 998;
  }

  .overlay.active {
      opacity: 1;
      visibility: visible;
  }
  </style>
</head>
