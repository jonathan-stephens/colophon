<?php
// site/plugins/kirby-timekeeper/index.php

@include_once __DIR__ . '/lib/TimeKeeper.php';
@include_once __DIR__ . '/lib/TimeTheme.php';

Kirby::plugin('j/kirby-timekeeper', [
    'options' => [
        'useClientTime' => true,  // Use client's local time instead of server time
        'allowManualOverride' => true,  // Allow users to manually select time period
        'cookieDuration' => 60 * 60 * 24  // How long to remember manual selection (24 hours)
    ],

    // Make helpers available in templates
    'snippets' => [
        'timekeeper/theme-switcher' => __DIR__ . '/snippets/theme-switcher.php'
    ],

    // Add API routes for theme switching
    'api' => [
        'routes' => [
            [
                'pattern' => 'timekeeper/switch-theme',
                'method' => 'POST',
                'action' => function () {
                    $theme = get('theme');
                    return TimeTheme::switchTo($theme);
                }
            ]
        ]
    ]
]);
