<?php

/**
 * Stream collection
 *
 * Returns all qualifying pages across the site, sorted by
 * last-tended date descending, falling back to first-published date.
 *
 * Configuration lives in config.php under the 'stream' key.
 * Logged-in users see drafts and unlisted pages in addition to listed ones.
 */
return function ($site, $pages, $kirby) {

    // ------------------------------------------------------------------
    // Config
    // ------------------------------------------------------------------
    $config   = $kirby->option('stream', []);
    $subtrees = $config['subtrees'] ?? 'all';
    $minDepth = $config['minDepth'] ?? 2;
    $maxDepth = $config['maxDepth'] ?? null;
    $exclude  = $config['exclude']  ?? ['stream', 'error'];

    $isLoggedIn = $kirby->user() !== null;

    // ------------------------------------------------------------------
    // 1. Build the raw page pool
    //    index(true)  = listed + unlisted + drafts  (logged-in users)
    //    index(false) = listed + unlisted only       (public default)
    // ------------------------------------------------------------------
    if ($subtrees === 'all') {

        $pool = $site->index($isLoggedIn);

    } else {

        // Gather children from each named subtree into one keyed array,
        // then construct a single Pages collection from it.
        $items = [];
        foreach ((array) $subtrees as $id) {
            $root = $site->find($id);
            if (!$root) {
                continue;
            }
            foreach ($root->index($isLoggedIn) as $p) {
                $items[$p->id()] = $p;
            }
        }
        $pool = new Pages($items);

    }

    // ------------------------------------------------------------------
    // 2. Visibility — public visitors see only listed pages
    // ------------------------------------------------------------------
    if (!$isLoggedIn) {
        $pool = $pool->listed();
    }

    // ------------------------------------------------------------------
    // 3. Depth filter
    // ------------------------------------------------------------------
    $pool = $pool->filter(function ($page) use ($minDepth, $maxDepth) {
        $depth = $page->depth();
        if ($depth < $minDepth) {
            return false;
        }
        if ($maxDepth !== null && $depth > $maxDepth) {
            return false;
        }
        return true;
    });

    // ------------------------------------------------------------------
    // 4. Exclusion filter
    // ------------------------------------------------------------------
    if (!empty($exclude)) {
        $pool = $pool->filter(function ($page) use ($exclude) {
            return !in_array($page->id(), $exclude, true);
        });
    }

    // ------------------------------------------------------------------
    // 5. Sort: tended desc → date desc → 0 (undated pages last)
    // ------------------------------------------------------------------
    $effectiveTimestamp = function ($page) {
        if ($page->tended()->isNotEmpty()) {
            return strtotime($page->tended()->value());
        }
        if ($page->date()->isNotEmpty()) {
            return strtotime($page->date()->value());
        }
        return 0;
    };

    // Pull into a keyed array, sort preserving keys, rebuild Pages
    $arr = [];
    foreach ($pool as $page) {
        $arr[$page->id()] = $page;
    }

    uasort($arr, function ($a, $b) use ($effectiveTimestamp) {
        return $effectiveTimestamp($b) <=> $effectiveTimestamp($a);
    });

    return new Pages($arr);
};
