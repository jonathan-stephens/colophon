<?php
@include_once __DIR__ . '/lib/helpers.php';
@include_once __DIR__ . '/lib/templatehandler.php';

Kirby::plugin('j/template-handler', [
    'options' => [
        'cache' => true,
        'defaultTemplate' => 'default'
    ],
    'blueprints' => [
        'sections/template-rules' => __DIR__ . '/blueprints/sections/template-rules.yml',
    ],
    'collections' => [
        'templatedPages' => function ($site) {
            return $site->index()->filterBy('template-rule', '!=', null);
        }
    ]
]);
