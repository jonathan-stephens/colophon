<?php
// File: site/blueprints/blocks/comparison-slider.yml
name: Comparison Slider
icon: image
preview: fields
fields:
  title:
    label: Title
    type: text
    width: 1/2
  id:
    label: Unique ID
    type: text
    width: 1/2
    help: Optional unique identifier for this slider
  type:
    label: Content Type
    type: select
    default: image
    options:
      image: Images
      text: Text Content
      mixed: Mixed Content
    width: 1/2
  initial_position:
    label: Initial Position
    type: range
    min: 0
    max: 100
    step: 1
    default: 50
    after: "%"
    width: 1/2
  
  # Image-specific fields
  before_image:
    label: Before Image
    type: files
    multiple: false
    when:
      type: image
    width: 1/2
  after_image:
    label: After Image
    type: files
    multiple: false
    when:
      type: image
    width: 1/2
  before_image_alt:
    label: Before Image Alt Text
    type: text
    when:
      type: image
    width: 1/2
  after_image_alt:
    label: After Image Alt Text
    type: text
    when:
      type: image
    width: 1/2
  
  # Text-specific fields
  before_text:
    label: Before Content
    type: textarea
    when:
      type: text
    width: 1/2
  after_text:
    label: After Content
    type: textarea
    when:
      type: text
    width: 1/2
  before_background:
    label: Before Background Color
    type: text
    default: "#fff8f0"
    when:
      type: text
    width: 1/2
  after_background:
    label: After Background Color
    type: text
    default: "#122b1f"
    when:
      type: text
    width: 1/2
  
  # Mixed content fields
  before_content:
    label: Before Content
    type: blocks
    fieldsets:
      - heading
      - text
      - image
      - list
    when:
      type: mixed
    width: 1/2
  after_content:
    label: After Content
    type: blocks
    fieldsets:
      - heading
      - text
      - image
      - list
    when:
      type: mixed
    width: 1/2
  
  # Animation options
  enable_animation:
    label: Enable Auto-play Animation
    type: toggle
    default: false
    width: 1/3
  animation_duration:
    label: Animation Duration (seconds)
    type: number
    min: 1
    max: 30
    default: 5
    when:
      enable_animation: true
    width: 1/3
  animation_delay:
    label: Animation Delay (seconds)
    type: number
    min: 0
    max: 10
    default: 2
    when:
      enable_animation: true
    width: 1/3
  
  # Styling options
  height:
    label: Slider Height
    type: select
    default: auto
    options:
      auto: Auto
      small: Small (300px)
      medium: Medium (500px)
      large: Large (700px)
      viewport: Full Viewport Height
    width: 1/2
  show_labels:
    label: Show Before/After Labels
    type: toggle
    default: false
    width: 1/2
  before_label:
    label: Before Label
    type: text
    default: "Before"
    when:
      show_labels: true
    width: 1/2
  after_label:
    label: After Label
    type: text
    default: "After"
    when:
      show_labels: true
    width: 1/2

---

// File: site/snippets/blocks/comparison-slider.php
<?php
$blockId = $block->id()->or('slider-' . uniqid());
$type = $block->type()->value();
$initialPos = $block->initial_position()->or(50);
$height = $block->height()->or('auto');
$showLabels = $block->show_labels()->toBool();
$enableAnimation = $block->enable_animation()->toBool();
$animationDuration = $block->animation_duration()->or(5);
$animationDelay = $block->animation_delay()->or(2);

// Height mapping
$heightClass = match($height) {
    'small' => 'h-300',
    'medium' => 'h-500', 
    'large' => 'h-700',
    'viewport' => 'h-viewport',
    default => 'h-auto'
};
?>

<div class="comparison-slider <?= $heightClass ?>" 
     data-slider-id="<?= $blockId ?>"
     data-initial-pos="<?= $initialPos ?>"
     <?php if ($enableAnimation): ?>
     data-animate="true"
     data-duration="<?= $animationDuration ?>"
     data-delay="<?= $animationDelay ?>"
     <?php endif; ?>>
  
  <?php if ($block->title()->isNotEmpty()): ?>
    <h3 class="slider-title"><?= $block->title()->html() ?></h3>
  <?php endif; ?>
  
  <div class="compare" id="<?= $blockId ?>">
    
    <?php if ($type === 'image'): ?>
      <!-- Image Comparison -->
      <section class="before image-section">
        <?php if ($beforeImage = $block->before_image()->toFile()): ?>
          <img src="<?= $beforeImage->url() ?>" 
               alt="<?= $block->before_image_alt()->or('Before image') ?>"
               loading="lazy">
        <?php endif; ?>
        <?php if ($showLabels): ?>
          <span class="label before-label"><?= $block->before_label()->or('Before') ?></span>
        <?php endif; ?>
      </section>
      
      <section class="after image-section">
        <?php if ($afterImage = $block->after_image()->toFile()): ?>
          <img src="<?= $afterImage->url() ?>" 
               alt="<?= $block->after_image_alt()->or('After image') ?>"
               loading="lazy">
        <?php endif; ?>
        <?php if ($showLabels): ?>
          <span class="label after-label"><?= $block->after_label()->or('After') ?></span>
        <?php endif; ?>
      </section>
      
    <?php elseif ($type === 'text'): ?>
      <!-- Text Comparison -->
      <section class="before text-section" 
               style="background-color: <?= $block->before_background()->or('#fff8f0') ?>">
        <div class="text-content">
          <?= $block->before_text()->kt() ?>
        </div>
        <?php if ($showLabels): ?>
          <span class="label before-label"><?= $block->before_label()->or('Before') ?></span>
        <?php endif; ?>
      </section>
      
      <section class="after text-section"
               style="background-color: <?= $block->after_background()->or('#122b1f') ?>">
        <div class="text-content">
          <?= $block->after_text()->kt() ?>
        </div>
        <?php if ($showLabels): ?>
          <span class="label after-label"><?= $block->after_label()->or('After') ?></span>
        <?php endif; ?>
      </section>
      
    <?php elseif ($type === 'mixed'): ?>
      <!-- Mixed Content Comparison -->
      <section class="before mixed-section">
        <div class="mixed-content">
          <?= $block->before_content()->toBlocks() ?>
        </div>
        <?php if ($showLabels): ?>
          <span class="label before-label"><?= $block->before_label()->or('Before') ?></span>
        <?php endif; ?>
      </section>
      
      <section class="after mixed-section">
        <div class="mixed-content">
          <?= $block->after_content()->toBlocks() ?>
        </div>
        <?php if ($showLabels): ?>
          <span class="label after-label"><?= $block->after_label()->or('After') ?></span>
        <?php endif; ?>  
      </section>
      
    <?php endif; ?>
    
    <input type="range" 
           class="slider-range" 
           id="range-<?= $blockId ?>" 
           min="0" 
           max="100" 
           value="<?= $initialPos ?>" 
           step="0.1"
           aria-label="Comparison slider">
  </div>
</div>

---

// File: assets/css/comparison-slider.css
.comparison-slider {
  margin: 2rem 0;
  width: 100%;
}

.slider-title {
  margin-bottom: 1rem;
  font-size: 1.5rem;
  font-weight: bold;
}

.compare {
  display: grid;
  position: relative;
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.compare > * {
  grid-area: 1 / 1;
}

section {
  display: grid;
  align-items: center;
  justify-content: center;
  position: relative;
  min-height: 300px;
}

.before {
  mask: linear-gradient(to right, #000 0, var(--pos, 50%), transparent 0);
  -webkit-mask: linear-gradient(to right, #000 0, var(--pos, 50%), transparent 0);
}

.after {
  mask: linear-gradient(to right, transparent 0, var(--pos, 50%), #000 0);
  -webkit-mask: linear-gradient(to right, transparent 0, var(--pos, 50%), #000 0);
}

/* Image sections */
.image-section {
  overflow: hidden;
}

.image-section img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Text sections */
.text-section {
  padding: 2rem;
  font-size: 1.2rem;
}

.text-content {
  max-width: 600px;
  text-align: center;
}

/* Mixed content sections */
.mixed-section {
  padding: 1rem;
}

.mixed-content {
  max-width: 800px;
  width: 100%;
}

/* Labels */
.label {
  position: absolute;
  top: 1rem;
  padding: 0.5rem 1rem;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: bold;
  pointer-events: none;
  z-index: 2;
}

.before-label {
  left: 1rem;
}

.after-label {
  right: 1rem;
}

/* Slider input */
.slider-range {
  z-index: 3;
  appearance: none;
  background: transparent;
  cursor: grab;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
}

.slider-range:active {
  cursor: grabbing;
}

.slider-range::-webkit-slider-thumb {
  appearance: none;
  width: 4px;
  height: 100vh;
  background-color: #fff;
  box-shadow: 0 0 0 2px #000, 0 2px 4px rgba(0, 0, 0, 0.3);
  cursor: grab;
}

.slider-range::-moz-range-thumb {
  appearance: none;
  width: 4px;
  height: 100vh;
  background-color: #fff;
  border: 2px solid #000;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  cursor: grab;
  border-radius: 0;
}

.slider-range:active::-webkit-slider-thumb,
.slider-range:active::-moz-range-thumb {
  cursor: grabbing;
}

/* Height classes */
.h-300 .compare section { min-height: 300px; }
.h-500 .compare section { min-height: 500px; }
.h-700 .compare section { min-height: 700px; }
.h-viewport .compare section { min-height: 100vh; }
.h-auto .compare section { min-height: auto; }

/* Responsive design */
@media (max-width: 768px) {
  .text-section {
    padding: 1rem;
    font-size: 1rem;
  }
  
  .label {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
  }
  
  .h-viewport .compare section {
    min-height: 80vh;
  }
}

---

// File: assets/js/comparison-slider.js
class ComparisonSlider {
  constructor(element) {
    this.container = element;
    this.sliderId = element.dataset.sliderId;
    this.slider = element.querySelector('.slider-range');
    this.initialPos = parseFloat(element.dataset.initialPos) || 50;
    this.isAnimating = false;
    
    // Animation settings
    this.enableAnimation = element.dataset.animate === 'true';
    this.animationDuration = parseFloat(element.dataset.duration) || 5;
    this.animationDelay = parseFloat(element.dataset.delay) || 2;
    
    this.init();
  }
  
  init() {
    if (!this.slider) return;
    
    // Set initial position
    this.updatePosition(this.initialPos);
    
    // Bind events
    this.slider.addEventListener('input', (e) => {
      if (!this.isAnimating) {
        this.updatePosition(e.target.value);
      }
    });
    
    // Touch/mouse events for better mobile experience
    this.slider.addEventListener('touchstart', () => this.stopAnimation());
    this.slider.addEventListener('mousedown', () => this.stopAnimation());
    
    // Start animation if enabled
    if (this.enableAnimation) {
      setTimeout(() => this.startAnimation(), this.animationDelay * 1000);
    }
    
    // Keyboard accessibility
    this.slider.addEventListener('keydown', (e) => {
      this.stopAnimation();
      if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
        e.preventDefault();
        this.updatePosition(Math.max(0, parseFloat(this.slider.value) - 1));
        this.slider.value = Math.max(0, parseFloat(this.slider.value) - 1);
      } else if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
        e.preventDefault();
        this.updatePosition(Math.min(100, parseFloat(this.slider.value) + 1));
        this.slider.value = Math.min(100, parseFloat(this.slider.value) + 1);
      }
    });
  }
  
  updatePosition(value) {
    const compare = this.container.querySelector('.compare');
    if (compare) {
      compare.style.setProperty('--pos', value + '%');
    }
  }
  
  startAnimation() {
    if (this.isAnimating) return;
    
    this.isAnimating = true;
    const startTime = Date.now();
    const startValue = parseFloat(this.slider.value);
    const endValue = startValue === 0 ? 100 : 0;
    const duration = this.animationDuration * 1000;
    
    const animate = () => {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / duration, 1);
      
      // Ease in-out function
      const easeInOut = t => t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
      const easedProgress = easeInOut(progress);
      
      const currentValue = startValue + (endValue - startValue) * easedProgress;
      
      this.slider.value = currentValue;
      this.updatePosition(currentValue);
      
      if (progress < 1 && this.isAnimating) {
        requestAnimationFrame(animate);
      } else if (this.isAnimating) {
        // Restart animation after delay
        setTimeout(() => this.startAnimation(), this.animationDelay * 1000);
      }
    };
    
    requestAnimationFrame(animate);
  }
  
  stopAnimation() {
    this.isAnimating = false;
  }
}

// Initialize all comparison sliders
document.addEventListener('DOMContentLoaded', () => {
  const sliders = document.querySelectorAll('.comparison-slider');
  sliders.forEach(slider => new ComparisonSlider(slider));
});

// Reinitialize after HTMX or dynamic content loading (if using HTMX)
document.addEventListener('htmx:afterSwap', () => {
  const sliders = document.querySelectorAll('.comparison-slider');
  sliders.forEach(slider => {
    if (!slider.classList.contains('initialized')) {
      new ComparisonSlider(slider);
      slider.classList.add('initialized');
    }
  });
});

---

// File: site/config/config.php (add this to your existing config)
<?php

return [
    // ... your existing config
    
    // Add comparison slider to available blocks
    'blocks' => [
        'comparison-slider' => [
            'name' => 'Comparison Slider',
            'preview' => 'fields'
        ]
    ]
];

---

// Installation Instructions:

/*
1. Create the blueprint file:
   site/blueprints/blocks/comparison-slider.yml

2. Create the snippet file:
   site/snippets/blocks/comparison-slider.php

3. Add the CSS file to your assets:
   assets/css/comparison-slider.css
   
   Then include it in your template or add to your build process:
   <?= css('assets/css/comparison-slider.css') ?>

4. Add the JavaScript file:
   assets/js/comparison-slider.js
   
   Then include it in your template:
   <?= js('assets/js/comparison-slider.js') ?>

5. Add the block to your page blueprints where you want to use it:
   
   content:
     type: blocks
     fieldsets:
       - comparison-slider
       - heading
       - text
       # ... your other blocks

6. Usage in templates:
   Simply add <?= $page->content()->toBlocks() ?> in your template
   and the comparison slider blocks will render automatically.

Features:
- Image comparison with alt text support
- Text comparison with custom backgrounds
- Mixed content using Kirby blocks
- Customizable height options
- Before/After labels
- Auto-play animation with customizable timing
- Responsive design
- Keyboard accessibility
- Touch-friendly on mobile
- Multiple sliders per page support
- Easy styling customization

The block will appear in the Kirby panel and content editors can easily
configure all aspects through the intuitive interface.
*/