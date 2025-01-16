<?php
return [
    'panel' => [
        'install' => true,
        'debug' => true,
        'mauricerenck.indieConnector.secret' => 'my-secret',
    ],
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
