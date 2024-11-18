<?php

class TimeTheme {
    private static function getCookie(): ?string {
        return cookie('timekeeper-theme');
    }

    public static function switchTo(string $theme): bool {
        $duration = option('jonathanstephens.timekeeper.cookieDuration');
        cookie('timekeeper-theme', $theme, $duration);
        return true;
    }

    public static function getCurrentTheme(): string {
        if (option('jonathanstephens.timekeeper.allowManualOverride')) {
            $override = self::getCookie();
            if ($override) {
                return $override;
            }
        }

        return TimeKeeper::getCurrentPeriod();
    }
}
