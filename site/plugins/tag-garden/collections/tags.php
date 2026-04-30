<?php

/**
 * Tag Garden Collections
 * @version 2.0.0
 */

use jonathanstephens\TagGarden\Helpers;

return [

    'tags.all' => function ($kirby, $site, $pages, $sortBy = 'count', $direction = 'desc') {

        $tags = Helpers::getAllTags();

        if ($sortBy === 'alpha') {
            ksort($tags);
            if ($direction === 'desc') {
                $tags = array_reverse($tags, true);
            }
        } else {
            $direction === 'desc' ? arsort($tags) : asort($tags);
        }

        return $tags;
    },

    'tags.byGroup' => function ($kirby, $site, $pages, $group = null, $sortBy = 'count', $direction = 'desc') {

        if (!$group) {
            return [];
        }

        $groups = option('jonathanstephens.tag-garden.content.groups', []);
        $types  = $groups[$group] ?? [];

        if (empty($types)) {
            return [];
        }

        $filtered = $kirby->site()->index()->filter(function ($page) use ($types) {
            return in_array($page->intendedTemplate()->name(), $types);
        });

        $tags = [];
        foreach ($filtered as $page) {
            foreach ($page->tags()->split(',') as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tags[$tag] = ($tags[$tag] ?? 0) + 1;
                }
            }
        }

        if ($sortBy === 'alpha') {
            ksort($tags);
            if ($direction === 'desc') $tags = array_reverse($tags, true);
        } else {
            $direction === 'desc' ? arsort($tags) : asort($tags);
        }

        return $tags;
    },

    'tags.byGrowth' => function ($kirby, $site, $pages, $status = null, $sortBy = 'count', $direction = 'desc') {

        if (!$status) {
            return [];
        }

        $filtered = $kirby->site()->index()->filter(function($page) use ($status) {
            return $page->Growthstatus()->isNotEmpty() 
                && $page->Growthstatus()->value() === $status;
        });

        $tags = [];
        foreach ($filtered as $page) {
            foreach ($page->tags()->split(',') as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tags[$tag] = ($tags[$tag] ?? 0) + 1;
                }
            }
        }

        if ($sortBy === 'alpha') {
            ksort($tags);
            if ($direction === 'desc') $tags = array_reverse($tags, true);
        } else {
            $direction === 'desc' ? arsort($tags) : asort($tags);
        }

        return $tags;
    },

    // Bug fix: was using $tags (undefined) instead of $relatedTags throughout sort block
    'tags.related' => function ($kirby, $site, $pages, $tag = null, $limit = 10, $sortBy = 'count', $direction = 'desc') {

        if (!$tag) {
            return [];
        }

        $taggedPages = Helpers::getPagesByTags($tag);
        $tagLower    = mb_strtolower($tag);

        $relatedTags = [];
        foreach ($taggedPages as $page) {
            foreach ($page->tags()->split(',') as $relatedTag) {
                $relatedTag = trim($relatedTag);
                if (!empty($relatedTag) && mb_strtolower($relatedTag) !== $tagLower) {
                    $relatedTags[$relatedTag] = ($relatedTags[$relatedTag] ?? 0) + 1;
                }
            }
        }

        if ($sortBy === 'alpha') {
            ksort($relatedTags);
            if ($direction === 'desc') $relatedTags = array_reverse($relatedTags, true);
        } else {
            $direction === 'desc' ? arsort($relatedTags) : asort($relatedTags);
        }

        return array_slice($relatedTags, 0, $limit, true);
    },

    'pages.byTags' => function ($kirby, $site, $pages, $tags = [], $sort = 'tended', $direction = 'desc', $limit = 0) {

        $result = Helpers::getPagesByTags($tags);
        $result = Helpers::sortPages($result, $sort, $direction);

        if ($limit > 0) {
            $result = $result->limit($limit);
        }

        return $result;
    },

    'pages.recentlyPlanted' => function ($kirby, $site, $pages, $limit = 10, $tags = null) {

        $result = $tags ? Helpers::getPagesByTags($tags) : $kirby->site()->index();

        return $result
            ->filterBy('date_planted', '!=', '')
            ->sortBy('date_planted', 'desc')
            ->limit($limit);
    },

    'pages.recentlyTended' => function ($kirby, $site, $pages, $limit = 10, $tags = null) {

        $result = $tags ? Helpers::getPagesByTags($tags) : $kirby->site()->index();

        return $result
            ->filterBy('last_tended', '!=', '')
            ->sortBy('last_tended', 'desc')
            ->limit($limit);
    },

    'pages.byGrowth' => function ($kirby, $site, $pages, $status = null, $sort = 'tended', $limit = 0) {

        if (!$status) {
            return new \Kirby\Cms\Pages([]);
        }

        $result = $kirby->site()->index()->filterBy('Growthstatus', $status);
        $result = Helpers::sortPages($result, $sort);

        if ($limit > 0) {
            $result = $result->limit($limit);
        }

        return $result;
    },

];