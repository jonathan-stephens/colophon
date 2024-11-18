<?php
  return [
    'panel' =>[
      'debug' => false,
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

  return [
];

?>
