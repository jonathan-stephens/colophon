<?php
// site/controllers/tags.php

use jonathanstephens\TagGarden\Helpers;

return function ($kirby, $page) {

    $groupFilter  = get('group');
    $growthFilter = get('growth');
    $tagSort      = get('tagSort', 'count'); // 'count' or 'alpha'

    // --- 1. Fetch tags (pass sort intent into the collection) ---
    $sortBy    = $tagSort === 'alpha' ? 'alpha' : 'count';
    $direction = 'desc';

    if ($groupFilter) {
        $tags = $kirby->collection('tags.byGroup', [
            'group'     => $groupFilter,
            'sortBy'    => $sortBy,
            'direction' => $direction,
        ]);
    } elseif ($growthFilter) {
        $tags = $kirby->collection('tags.byGrowth', [
            'status'    => $growthFilter,
            'sortBy'    => $sortBy,
            'direction' => $direction,
        ]);
    } else {
        $tags = $kirby->collection('tags.all', [
            'sortBy'    => $sortBy,
            'direction' => $direction,
        ]);
    }

    // --- 2. Recent content ---
    $recentlyTended  = $kirby->collection('pages.recentlyTended',  ['limit' => 5]);
    $recentlyPlanted = $kirby->collection('pages.recentlyPlanted', ['limit' => 5]);

    // --- 3. Stats ---
    $totalTaggedPages = $kirby->site()->index()
        ->filterBy('tags', '!=', '')
        ->count();

    // --- 4. UI filter options ---
    $groups = [];
    foreach (array_keys(option('jonathanstephens.tag-garden.content.groups', [])) as $key) {
        $def = Helpers::getGroupDefinition($key);
        if ($def) $groups[$key] = $def;
    }

    $growthStatuses = [];
    foreach (array_keys(option('jonathanstephens.tag-garden.growth.definitions', [])) as $key) {
        $def = Helpers::getGrowthDefinition($key);
        if ($def) $growthStatuses[$key] = $def;
    }

    return [
        'tags'             => $tags,
        'totalTags'        => count($tags),
        'totalTaggedPages' => $totalTaggedPages,
        'groupFilter'      => $groupFilter,
        'growthFilter'     => $growthFilter,
        'tagSort'          => $tagSort,
        'groups'           => $groups,
        'growthStatuses'   => $growthStatuses,
        'recentlyTended'   => $recentlyTended,
        'recentlyPlanted'  => $recentlyPlanted,
        'getTagUrl'        => fn($tag) => url('tags/' . \Kirby\Toolkit\Str::slug($tag)),
        'isActiveGroup'    => fn($key) => $groupFilter === $key,
        'isActiveGrowth'   => fn($key) => $growthFilter === $key,
    ];
};