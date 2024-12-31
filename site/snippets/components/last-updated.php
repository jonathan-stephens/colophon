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

        return <<<HTML
        <div class="last-updated">
            <div class="h-entry">
                <data
                    class="dt-updated u-updated"
                    value="{$isoDate}"
                    itemprop="dateModified"
                >
                    Page last updated one {$timeOfDay}
                    in {$season}, <span class="with-icon"><svg class="icon" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M15 17C16.1046 17 17 16.1046 17 15C17 13.8954 16.1046 13 15 13C13.8954 13 13 13.8954 13 15C13 16.1046 13.8954 17 15 17Z"
                        fill="currentColor"
                      />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M6 3C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6ZM5 18V7H19V18C19 18.5523 18.5523 19 18 19H6C5.44772 19 5 18.5523 5 18Z"
                        fill="currentColor"
                      /></svg><time class="dmy" datetime="{$kirbyDate}">{$kirbyDate}</time></span> at
                      <span class="with-icon"><svg class="icon" width="24" viewBox="0 0 24 24"xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 7H11V12H16V14H9V7Z" fill="currentColor" />
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12Z"
                          fill="currentColor"
                        />
                      </svg>
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
