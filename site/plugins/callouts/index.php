<?php

declare(strict_types=1);

use Lemmon\Callouts\Renderer;

require_once __DIR__ . '/lib/callouts.php';

Kirby::plugin('lemmon/callouts', [
    'options' => [
        'classPrefix' => Renderer::DEFAULT_CLASS_PREFIX,
        'icons' => Renderer::DEFAULT_ICONS,
        'renderHeader' => Renderer::DEFAULT_RENDER_HEADER,
        'wrapper' => Renderer::DEFAULT_WRAPPER,
    ],
    'hooks' => [
        'kirbytext:before' => function (string|null $text): string|null {
            if (empty($text)) {
                return $text;
            }

            return Renderer::transform($text, [
                'classPrefix' => option('lemmon.callouts.classPrefix', Renderer::DEFAULT_CLASS_PREFIX),
                'icons' => option('lemmon.callouts.icons', Renderer::DEFAULT_ICONS),
                'renderHeader' => option('lemmon.callouts.renderHeader', Renderer::DEFAULT_RENDER_HEADER),
                'wrapper' => option('lemmon.callouts.wrapper', Renderer::DEFAULT_WRAPPER),
            ]);
        },
    ],
]);
