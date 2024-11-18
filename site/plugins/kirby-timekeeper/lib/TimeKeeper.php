<?php

class TimeKeeper {
    private static $periods = [
        'early-morning' => ['start' => 6, 'end' => 8],
        'morning' => ['start' => 8, 'end' => 10],
        'late-morning' => ['start' => 10, 'end' => 12],
        'early-afternoon' => ['start' => 12, 'end' => 14],
        'afternoon' => ['start' => 14, 'end' => 15],
        'late-afternoon' => ['start' => 15, 'end' => 17],
        'evening' => ['start' => 17, 'end' => 20],
        'late-evening' => ['start' => 20, 'end' => 22],
        'night' => ['start' => 22, 'end' => 1],
        'late-night' => ['start' => 1, 'end' => 6]
    ];

    private static $seasons = [
        'winter' => ['start' => 12, 'end' => 2],
        'spring' => ['start' => 3, 'end' => 5],
        'summer' => ['start' => 6, 'end' => 8],
        'autumn' => ['start' => 9, 'end' => 11]
    ];

    public static function getCurrentPeriod(): string {
        $hour = (int)date('H');

        foreach (self::$periods as $period => $times) {
            if ($times['start'] <= $hour && $hour < $times['end']) {
                return $period;
            }
            // Special case for night spanning midnight
            if ($period === 'night' && ($hour >= $times['start'] || $hour < $times['end'])) {
                return $period;
            }
        }

        return 'day'; // fallback
    }

    public static function getCurrentSeason(): string {
        $month = (int)date('n');

        foreach (self::$seasons as $season => $months) {
            if ($months['start'] <= $month && $month <= $months['end']) {
                return $season;
            }
            // Special case for winter spanning new year
            if ($season === 'winter' && ($month === 12 || $month <= 2)) {
                return $season;
            }
        }

        return 'unknown';
    }

    public static function getPeriodData(): array {
        return self::$periods;
    }

    public static function getSeasonData(): array {
        return self::$seasons;
    }

    public static function isNightTime(): bool {
        $period = self::getCurrentPeriod();
        return in_array($period, ['night', 'late-night']);
    }

    public static function isDayTime(): bool {
        return !self::isNightTime();
    }
}
