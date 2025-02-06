<?php
return [
    'panel' => [
        'install' => true,
        'debug' => true,
        'mauricerenck.indieConnector.secret' => 'my-secret',
    ],
    'date.timezone' => 'America/New_York',
    'jonathanstephens.template-handler' => [
        'cache' => true,
        'defaultTemplate' => 'default'
    ],
    'jonathanstephens.timekeeper' => [
        'useClientTime' => true,
        'allowManualOverride' => true,
        'cookieDuration' => 60 * 60 * 24
    ]
];
