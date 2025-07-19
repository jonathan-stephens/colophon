<?php
// site/snippets/last-updated.php

class LastUpdated {
    /**
     * Get time of day with detailed periods
     */
    private static function getTimeOfDay($hour): string {
        if ($hour >= 6 && $hour < 12) {
            if ($hour < 8) return 'early morning';
            if ($hour < 10) return 'morning';
            return 'late morning';
        }

        if ($hour >= 12 && $hour < 17) {
            if ($hour < 14) return 'early afternoon';
            if ($hour < 15) return 'afternoon';
            return 'late afternoon';
        }

        if ($hour >= 17 && $hour < 22) {
            if ($hour < 20) return 'evening';
            return 'late evening';
        }

        if ($hour >= 22 || $hour < 6) {
            if ($hour >= 22 || $hour < 1) return 'night';
            return 'late night';
        }

        return 'day'; // fallback
    }

    /**
     * Get season based on month
     */
    private static function getSeason($month): string {
        if ($month >= 12 || $month <= 2) return 'winter';
        if ($month >= 3 && $month <= 5) return 'spring';
        if ($month >= 6 && $month <= 8) return 'summer';
        return 'autumn';
    }

    /**
     * Generate the formatted update message with metadata
     */
    public static function getFormattedUpdate($page): string {
        // Get the page's modified timestamp using Kirby's native method
        $timestamp = $page->modified();

        // Extract time components
        $hour = (int)date('H', $timestamp);
        $month = (int)date('n', $timestamp);

        $timeOfDay = self::getTimeOfDay($hour);
        $season = self::getSeason($month);

        // ISO 8601 date format for metadata
        $isoDate = date('c', $timestamp);

        // Get Kirby's formatted date
        $kirbyDate = $page->modified('j F Y');

        $calendarIcon = asset('assets/svg/icons/date.svg')->read();
        $clockIcon = asset('assets/svg/icons/time.svg')->read();
        $updateIcon = asset('assets/svg/icons/code-slash.svg')->read();
        $carbonIcon = asset('assets/svg/icons/sprout.svg')->read();

        return <<<HTML
        <div class="last-updated">
            <div class="h-entry">
              {$updateIcon}
                <data
                  class="dt-updated u-updated"
                  value="{$isoDate}"
                  itemprop="dateModified">
                    This page was last updated one {$timeOfDay}
                    in {$season}, <span class="with-icon">{$calendarIcon}
                      <time class="dmy" datetime="{$kirbyDate}">{$kirbyDate}</time></span> at
                      <span class="with-icon">{$clockIcon}
                        <time class="hidden hm" datetime="{$page->modified('c')}">
                        {$page->modified('H:i')}.
                    </time></span>
                </data>
            </div>
            <!-- Schema.org metadata -->
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebPage",
                "dateModified": "{$isoDate}",
                "timeModified": "{$page->modified('H:i')}"
            }
            </script>
        </div>
HTML;
    }
}

// Get the current page from Kirby
$page = page();

// Output the formatted update message
echo LastUpdated::getFormattedUpdate($page);
?>
