<?php
$endorsements = $page->endorsements()->toPages();
?>
<?php if ($endorsements->count() > 0): ?>
    <div class="inner" id="carousel" role="region" aria-label="Endorsements" tabindex="0">
      <?php foreach ($endorsements as $endorsement): ?>
        <article class="endorsement card" tabindex="0" role="article" aria-label="Endorsement from <?= $endorsement->hed()->html() ?>">
          <div class="content">
            <div class="lede">
              <?= $endorsement->lede()->kt() ?>
            </div>
            <div class="byline">
              <h3 class="reviewer"><?= $endorsement->hed()->html() ?></h3>
              <p>
                <?php if($endorsement->role()->isNotEmpty()): ?>
                  <span class="role"><?= $endorsement->role()->html() ?></span>
                <?php endif ?>
                <?php if($endorsement->relationship()->isNotEmpty()): ?>
                  <span class="relationship"> | <?= $endorsement->relationshipLabel() ?></span>
                <?php endif ?>
              </p>
            </div>
          </div>
        </article>
      <?php endforeach ?>

      <a class="endorsement cta" href="<?= $site->url() ?>/endorsements" role="button" aria-label="View all endorsements">
        <?= asset('assets/svg/icons/review.svg')->read() ?>
        View more endorsements
      </a>
    </div>
    <div class="carousel-nav">
    <!-- Navigation buttons -->
    <button class="scroll-indicator scroll-left" id="scrollLeft" aria-label="Scroll to previous endorsement">
      <?= asset('assets/svg/icons/arrow-left.svg')->read() ?>
    </button>
    <!-- Progress indicator -->
    <div class="carousel-progress" id="carouselProgress" role="tablist" aria-label="Endorsement navigation"></div>

    <button class="scroll-indicator scroll-right" id="scrollRight" aria-label="Scroll to next endorsement">
      <?= asset('assets/svg/icons/arrow-right.svg')->read() ?>
    </button>
    </div>

  </div>

<script>
// Enhanced carousel functionality
class AccessibleCarousel {
  constructor(carouselElement) {
    this.carousel = carouselElement;
    this.cards = Array.from(this.carousel.querySelectorAll('.endorsement.card, .endorsement.cta'));
    this.scrollLeft = document.getElementById('scrollLeft');
    this.scrollRight = document.getElementById('scrollRight');
    this.progress = document.getElementById('carouselProgress');

    this.currentIndex = 0;
    this.isScrolling = false;
    this.init();
  }

  init() {
    this.setupProgressDots();
    this.setupEventListeners();
    this.updateScrollButtons();
    this.updateProgress();
  }

  setupProgressDots() {
    this.cards.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.className = 'progress-dot';
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', `Go to endorsement ${index + 1}`);
      dot.addEventListener('click', () => this.scrollToCard(index));
      this.progress.appendChild(dot);
    });
  }

  setupEventListeners() {
    // Scroll buttons
    this.scrollLeft?.addEventListener('click', () => this.scrollPrevious());
    this.scrollRight?.addEventListener('click', () => this.scrollNext());

    // Keyboard navigation
    this.carousel.addEventListener('keydown', (e) => this.handleKeyboard(e));

    // Card focus management
    this.cards.forEach((card, index) => {
      card.addEventListener('focus', () => this.scrollToCard(index));
    });

    // Scroll detection with improved accuracy
    this.carousel.addEventListener('scroll', () => {
      if (!this.isScrolling) {
        clearTimeout(this.scrollTimeout);
        this.scrollTimeout = setTimeout(() => {
          this.updateCurrentIndexFromScroll();
          this.updateScrollButtons();
          this.updateProgress();
        }, 50);
      }
    });
  }

  handleKeyboard(e) {
    switch(e.key) {
      case 'ArrowRight':
        e.preventDefault();
        this.scrollNext();
        break;
      case 'ArrowLeft':
        e.preventDefault();
        this.scrollPrevious();
        break;
      case 'Home':
        e.preventDefault();
        this.scrollToCard(0);
        break;
      case 'End':
        e.preventDefault();
        this.scrollToCard(this.cards.length - 1);
        break;
    }
  }

  scrollNext() {
    if (this.currentIndex < this.cards.length - 1) {
      this.scrollToCard(this.currentIndex + 1);
    }
  }

  scrollPrevious() {
    if (this.currentIndex > 0) {
      this.scrollToCard(this.currentIndex - 1);
    }
  }

  scrollToCard(index) {
    if (index >= 0 && index < this.cards.length) {
      // Immediately update states for snappy response
      this.currentIndex = index;
      this.updateScrollButtons();
      this.updateProgress();

      // Set scrolling flag to prevent scroll event interference
      this.isScrolling = true;

      const card = this.cards[index];
      const containerRect = this.carousel.getBoundingClientRect();
      const cardRect = card.getBoundingClientRect();

      const scrollLeft = card.offsetLeft - this.carousel.offsetLeft -
                       (containerRect.width / 2) + (cardRect.width / 2);

      this.carousel.scrollTo({
        left: scrollLeft,
        behavior: 'smooth'
      });

      // Clear scrolling flag after animation
      setTimeout(() => {
        this.isScrolling = false;
      }, 500);
    }
  }

  updateCurrentIndexFromScroll() {
    const scrollLeft = this.carousel.scrollLeft;
    const containerWidth = this.carousel.clientWidth;
    const scrollRight = scrollLeft + containerWidth;

    let closestIndex = 0;
    let minDistance = Infinity;

    // Find which card is most centered in the viewport
    this.cards.forEach((card, index) => {
      const cardLeft = card.offsetLeft - this.carousel.offsetLeft;
      const cardCenter = cardLeft + (card.offsetWidth / 2);
      const containerCenter = scrollLeft + (containerWidth / 2);
      const distance = Math.abs(cardCenter - containerCenter);

      if (distance < minDistance) {
        minDistance = distance;
        closestIndex = index;
      }
    });

    // Additional check for the last card (CTA)
    // If we're scrolled near the end, make sure we detect the CTA
    const lastCard = this.cards[this.cards.length - 1];
    const lastCardLeft = lastCard.offsetLeft - this.carousel.offsetLeft;
    const maxScroll = this.carousel.scrollWidth - containerWidth;

    if (scrollLeft >= maxScroll - 50 || scrollLeft + containerWidth >= lastCardLeft + lastCard.offsetWidth - 50) {
      closestIndex = this.cards.length - 1;
    }

    this.currentIndex = closestIndex;
  }

  updateScrollButtons() {
    if (this.scrollLeft && this.scrollRight) {
      const isAtStart = this.currentIndex === 0;
      const isAtEnd = this.currentIndex >= this.cards.length - 1;

      this.scrollLeft.disabled = isAtStart;
      this.scrollRight.disabled = isAtEnd;

      this.scrollLeft.setAttribute('aria-disabled', isAtStart);
      this.scrollRight.setAttribute('aria-disabled', isAtEnd);

      // Visual feedback
      this.scrollLeft.style.opacity = isAtStart ? '0.5' : '1';
      this.scrollRight.style.opacity = isAtEnd ? '0.5' : '1';
    }
  }

  updateProgress() {
    const dots = this.progress?.querySelectorAll('.progress-dot') || [];
    dots.forEach((dot, index) => {
      const isActive = index === this.currentIndex;
      dot.classList.toggle('active', isActive);
      dot.setAttribute('aria-selected', isActive);
    });
  }
}

// Initialize carousel when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.getElementById('carousel');
  if (carousel) {
    new AccessibleCarousel(carousel);
  }
});
</script>

<?php endif ?>
