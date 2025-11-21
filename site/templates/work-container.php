<?php snippet('site-header') ?>

<article class="article post h-entry wrapper" itemscope itemtype="http://schema.org/Article">
  <header class="masthead work">
    <h1><?= $page->title()->html() ?></h1>

    <p class="dek"><span><?= $page->dek()->html() ?></span></p>

    <div class="lede">
      <?= $page->lede()->kt() ?>
    </div>
    <ul class="metrics">
      <li>
        <span>18+</span>
        Years of experience
      </li>
      <li>
        <span>10+</span>
        Years in leadership
      </li>
      <li>
        <span>~100</span>
        Largest org managed
      </li>
      <li>
        <span>~150</span>
        Performance reviews
      </li>
      <li>
        <span>40+</span>
        Promotions awarded
      </li>
      <li>
        <span>~40</span>
        Teams managed
      </li>
    </ul>

  </header>

  <section class="selected-work">
    <h2>Selected Work</h2>
    <p style="--measure:60ch;font-size:var(--nutgraf-secondary);max-width:var(--measure);">I've built a diverse career over 18 years—contributing individually as a designer to leading organizations of over 100 diverse people &amp; roles. This showcase focuses <em>(mostly)</em> on the last few years of my independent practice—running my studio and taking on select freelance and consulting projects.</p>
    <?php snippet('/components/case-studies', ['hedLevel' => 3]) ?>
  </section>
  <section class="timeline">
    <h2>Experience</h2>
    <ul>
      <li>
        <p>Product Design Lead
          <span>Strategi Consulting</span>
        </p>
        <time>
          2024 – now
        </time>
      </li>
      <li>
        <p>Design Systems & UX Consultant
          <span>Leantime.io</span>
        </p>
        <time>
          2024
        </time>
      </li>
      <li>
        <p>Business & Design Consultant
          <span>Moon Audio</span>
        </p>
        <time>
          2023
        </time>
      </li>
      <li>
        <p>Co-founder
          <span>Poet & Scribe</span>
        </p>
        <time>
          2022 – now
        </time>
      </li>
      <li>
        <p>Director of Product Development
          <span>Booking.com</span>
        </p>
        <time>
          2016 – 2022
        </time>
      </li>
      <li>
        <p>Freelance
          <span>Self-employed</span>
        </p>
        <time>
          2008 – 2016
        </time>
      </li>
      <li>
        <p>Manager of Product Development
          <span>Booking.com</span>
        </p>
        <time>
          2014 – 2016
        </time>
      </li>
      <li>
        <p>Senior Product Designer
          <span>Booking.com</span>
        </p>
        <time>
          2015 – 2016
        </time>
      </li>
      <li>
        <p>UX Designer
          <span>Booking.com</span>
        </p>
        <time>
          2013 – 2015
        </time>
      </li>
      <li>
        <p>Designer
          <span>Smashing Boxes</span>
        </p>
        <time>
          2011 – 2012
        </time>
      </li>
    </ul>

    <div class="cta">
      <p>View full experience</p>
      <a href="/work/experience" class="button">
        On my site</a>
      <a href="https://linkedin.com/in/elnatnal" class="button">
        On LinkedIn</a>
    </div>
  </section>


<?php snippet('site-footer') ?>
