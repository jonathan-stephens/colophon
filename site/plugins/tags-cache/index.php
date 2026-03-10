<?php

Kirby::plugin('site/tags-cache', [
    'siteMethods' => [
        'cachedTags' => function () {
            $cache = kirby()->cache('tags');
            $tags  = $cache->get('all-tags');

            if ($tags === null) {
                $tags = $this->index()->pluck('tags', ',', true);
                sort($tags);
                $cache->set('all-tags', $tags, 60);
            }

            return $tags;
        }
    ],
    'hooks' => [
        'page.update:after' => function () {
            kirby()->cache('tags')->flush();
        },
        'page.create:after' => function () {
            kirby()->cache('tags')->flush();
        }
    ]
]);