<?php
return [
    'panel' => [
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
    ],
    'routes' => [
        [
            'pattern' => 'tag/(:any)',
            'action'  => function($tag) {
                return page('default')->render([
                    'template' => 'tag',
                    'data' => [
                        'tag' => urldecode($tag)
                    ]
                ]);
            }
        ]
    ]
];
