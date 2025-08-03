<?php
// site/snippets/page-timestamps.php
class PageTimestamps {
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
     * Get relative date description
     */
    private static function getRelativeDate($timestamp): string {
        $now = time();
        $diff = $now - $timestamp;
        $days = floor($diff / 86400); // 86400 seconds in a day

        $currentYear = (int)date('Y', $now);
        $timestampYear = (int)date('Y', $timestamp);
        $currentMonth = (int)date('n', $now);
        $timestampMonth = (int)date('n', $timestamp);
        $currentQuarter = ceil($currentMonth / 3);
        $timestampQuarter = ceil($timestampMonth / 3);

        // Yesterday
        if ($days == 1) {
            return 'yesterday';
        }

        // Days ago (2-6 days)
        if ($days >= 2 && $days <= 6) {
            return 'a few days ago';
        }

        // Weeks ago (7-13 days)
        if ($days >= 7 && $days <= 13) {
            $weeks = floor($days / 7);
            return $weeks == 1 ? 'a week ago' : 'a few weeks ago';
        }

        // Fortnights ago (14-28 days)
        if ($days >= 14 && $days <= 28) {
            $fortnights = floor($days / 14);
            return $fortnights == 1 ? 'a fortnight ago' : 'a couple of fortnights ago';
        }

        // Last month (different calendar month, but within ~2 months)
        if ($currentYear == $timestampYear && $currentMonth - $timestampMonth == 1) {
            return 'last month';
        }

        // Months ago (2-3 months past)
        if ($currentYear == $timestampYear && $currentMonth - $timestampMonth >= 2 && $currentMonth - $timestampMonth <= 3) {
            $months = $currentMonth - $timestampMonth;
            return 'a few months ago';
        }

        // Quarters ago (within the same year)
        if ($currentYear == $timestampYear && $currentQuarter > $timestampQuarter) {
            $quarterDiff = $currentQuarter - $timestampQuarter;
            return $quarterDiff == 1 ? 'last quarter' : 'a few quarters ago';
        }

        // Earlier this year (same year, but more than 1 quarter difference)
        if ($currentYear == $timestampYear && $currentQuarter - $timestampQuarter > 1) {
            return 'earlier this year';
        }

        // Last year
        if ($currentYear - $timestampYear == 1) {
            return 'last year';
        }

        // A few years ago (2-4 years)
        if ($currentYear - $timestampYear >= 2 && $currentYear - $timestampYear <= 4) {
            $years = $currentYear - $timestampYear;
            return 'a few years ago';
        }

        // More than 4 years ago
        if ($currentYear - $timestampYear > 4) {
            $years = $currentYear - $timestampYear;
            return $years . 'years ago';
        }

        // Fallback for edge cases
        return 'recently';
    }

    /**
     * Generate a single timestamp entry
     */
    private static function generateTimestampEntry($timestamp, $type, $icon, $page): string {
        $hour = (int)date('H', $timestamp);
        $month = (int)date('n', $timestamp);
        $timeOfDay = self::getTimeOfDay($hour);
        $season = self::getSeason($month);
        $relativeDate = self::getRelativeDate($timestamp);

        // ISO 8601 date format for metadata
        $isoDate = date('c', $timestamp);
        $kirbyDate = date('j F Y', $timestamp);
        $timeString = date('H:i', $timestamp);

        $calendarIcon = asset('assets/svg/icons/date.svg')->read();
        $clockIcon = asset('assets/svg/icons/time.svg')->read();

        $verb = $type === 'created' ? 'was created' : 'was last updated';
        $classPrefix = $type === 'created' ? 'dt-published u-published' : 'dt-updated u-updated';
        $schemaProp = $type === 'created' ? 'datePublished' : 'dateModified';

        return <<<HTML
            <div class="{$type}-entry">
                {$icon}
                <data
                  class="{$classPrefix}"
                  value="{$isoDate}"
                  itemprop="{$schemaProp}">
                    This page {$verb} one {$timeOfDay}
                    in {$season}, {$relativeDate} — <span class="with-icon">{$calendarIcon}
                      <time class="dmy" datetime="{$kirbyDate}">{$kirbyDate}</time></span> at
                      <span class="with-icon">{$clockIcon}
                        <time class="hidden hm" datetime="{$isoDate}">
                        {$timeString}.
                    </time></span>
                </data>
            </div>
HTML;
    }

    /**
     * Generate the formatted timestamp messages with metadata
     */
    public static function getFormattedTimestamps($page): string {
        // Get the page's modified timestamp using Kirby's native method
        $timestamp = $page->modified();
        if (is_object($timestamp)) {
            $timestamp = $timestamp->toInt();
        }

        // Get icons
        $updateIcon = asset('assets/svg/icons/code-slash.svg')->read();

        // Generate only the updated entry
        $updatedEntry = self::generateTimestampEntry($timestamp, 'updated', $updateIcon, $page);

        // ISO dates for schema
        $modifiedIso = date('c', $timestamp);

        return <<<HTML
        <div class="last-updated">
            <div class="h-entry">
                {$updatedEntry}
            </div>
            <!-- Schema.org metadata -->
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebPage",
                "dateModified": "{$modifiedIso}",
                "timeModified": "{$page->modified('H:i')}"
            }
            </script>
        </div>
HTML;
    }
}

// Get the current page from Kirby
$page = page();

// Output the formatted timestamp messages
echo PageTimestamps::getFormattedTimestamps($page);
?>
