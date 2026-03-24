<?php

function readingTime(int $wordCount): string {
    $minSpeed = 167; // words per minute
    $maxSpeed = 285; // words per minute

    $minSeconds = ceil($wordCount / ($minSpeed / 60));
    $maxSeconds = ceil($wordCount / ($maxSpeed / 60));

    if ($minSeconds < 60) {
        return ($minSeconds === $maxSeconds)
            ? $minSeconds . ' sec read'
            : $maxSeconds . '&thinsp;–&thinsp;' . $minSeconds . ' sec read';
    }

    $minMinutes = ceil($minSeconds / 60);
    $maxMinutes = ceil($maxSeconds / 60);

    return ($minMinutes === $maxMinutes)
        ? $minMinutes . ' min read'
        : $maxMinutes . '&thinsp;–&thinsp;' . $minMinutes . ' min read';
}

function getVisibleChildren($page) {
    $user = kirby()->user();

    if ($user) {
        // User is logged in - show all posts (draft, listed, unlisted)
        return $page->children()->listed()
            ->add($page->children()->unlisted())
            ->add($page->children()->drafts());
    }

    // User not logged in - show only listed posts
    return $page->children()->listed();
}

function getPaginationLimit(int $totalItems): int {
    $limit = (int) get('limit', 16);
    $allowedLimits = [16, 56, 121, 211, 326];

    // If limit is greater than total items, show all
    if ($limit >= $totalItems) {
        return $totalItems;
    }

    // Ensure limit is within allowed values
    return in_array($limit, $allowedLimits) ? $limit : 16;
}

/**
 * streamCollection()
 *
 * Core stream logic. Returns a sorted Pages collection across the site
 * (or a subset of it), respecting login state for draft/unlisted visibility.
 *
 * Called by site/collections/stream.php with no arguments for the default
 * full-site stream. Pass $overrides to slice it any way you like.
 *
 * ------------------------------------------------------------------
 * USAGE — in a collection file (site/collections/*.php):
 * ------------------------------------------------------------------
 *
 *   return function ($site, $pages, $kirby) {
 *       return streamCollection($site, $kirby);
 *   };
 *
 * With overrides:
 *
 *   return function ($site, $pages, $kirby) {
 *       return streamCollection($site, $kirby, [
 *           'subtrees' => ['books', 'essays'],
 *           'minDepth' => 2,
 *           'maxDepth' => null,
 *           'exclude'  => ['stream', 'error'],
 *       ]);
 *   };
 *
 * ------------------------------------------------------------------
 * USAGE — inline inside any template or snippet:
 * ------------------------------------------------------------------
 *
 *   $books = streamCollection(site(), kirby(), ['subtrees' => ['books']]);
 *   foreach ($books as $entry) { ... }
 *
 * ------------------------------------------------------------------
 * OPTIONS
 * ------------------------------------------------------------------
 *
 * subtrees (string|array)
 *   'all'                    → crawl the entire site (default from config)
 *   ['books', 'essays']      → only index children of these top-level pages
 *   'books'                  → single subtree as a string also works
 *
 * minDepth (int)
 *   Pages at this depth or deeper are included.
 *   Depth 1 = top-level section pages (/books, /essays).
 *   Depth 2 = their direct children (/books/my-book) — usually what you want.
 *   Default: 2
 *
 * maxDepth (int|null)
 *   Cap how deep the crawl goes. null = no limit.
 *   e.g. maxDepth: 2 returns only direct children, not grandchildren.
 *   Default: null
 *
 * exclude (array)
 *   Page IDs that are always omitted regardless of other settings.
 *   Uses the full Kirby ID (e.g. 'stream', 'error', 'books/some-draft').
 *   Default: from config, typically ['stream', 'error']
 *
 * status (string|array|null)
 *   null or 'all'         → all statuses (respects login state — see below)
 *   'listed'              → listed pages only, regardless of login
 *   'unlisted'            → unlisted pages only
 *   'drafts'              → drafts only
 *   ['listed','unlisted'] → combine any set explicitly
 *   Default: null (all, respects login)
 *
 * ------------------------------------------------------------------
 * LOGIN STATE & VISIBILITY
 * ------------------------------------------------------------------
 *
 * When a Kirby user is logged in:
 *   - drafts, unlisted, and listed pages are all included in the pool
 *     (unless status option restricts it explicitly)
 *
 * When no user is logged in:
 *   - only listed pages are returned, always, regardless of status option
 *     (the status option can only narrow public visibility, never widen it)
 *
 * ------------------------------------------------------------------
 * SORTING
 * ------------------------------------------------------------------
 *
 * Primary:   $tended  descending (YYYY-MM-DD)
 * Fallback:  $date    descending (YYYY-MM-DD)
 * Last:      undated pages, in index order
 *
 * @param  object      $site
 * @param  object      $kirby
 * @param  array       $overrides   Option overrides — see above
 * @return Pages
 */
function streamCollection($site, $kirby, array $overrides = []): Pages
{
    // ------------------------------------------------------------------
    // Merge config defaults with any overrides
    // ------------------------------------------------------------------
    $config   = $kirby->option('stream', []);
    $options  = array_merge([
        'subtrees' => $config['subtrees'] ?? 'all',
        'minDepth' => $config['minDepth'] ?? 2,
        'maxDepth' => $config['maxDepth'] ?? null,
        'exclude'  => $config['exclude']  ?? ['stream', 'error'],
        'status'   => null,
    ], $overrides);

    $isLoggedIn = $kirby->user() !== null;

    // ------------------------------------------------------------------
    // 1. Build raw page pool
    //    index(true)  = listed + unlisted + drafts  (Kirby 5)
    //    index(false) = listed + unlisted only
    // ------------------------------------------------------------------
    $subtrees = $options['subtrees'];

    if ($subtrees === 'all') {
        $pool = $site->index($isLoggedIn);
    } else {
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
    // 2. Visibility — public visitors always get listed only.
    //    Logged-in users can narrow by status option if desired.
    // ------------------------------------------------------------------
    if (!$isLoggedIn) {
        $pool = $pool->listed();
    } elseif (!empty($options['status']) && $options['status'] !== 'all') {
        $requested = (array) $options['status'];
        $pool = $pool->filter(fn($page) => in_array($page->status(), $requested, true));
    }

    // ------------------------------------------------------------------
    // 3. Depth filter
    // ------------------------------------------------------------------
    $minDepth = $options['minDepth'];
    $maxDepth = $options['maxDepth'];

    $pool = $pool->filter(function ($page) use ($minDepth, $maxDepth) {
        $depth = $page->depth();
        if ($depth < $minDepth) return false;
        if ($maxDepth !== null && $depth > $maxDepth) return false;
        return true;
    });

    // ------------------------------------------------------------------
    // 4. Exclusion filter
    // ------------------------------------------------------------------
    if (!empty($options['exclude'])) {
        $excluded = $options['exclude'];
        $pool = $pool->filter(fn($page) => !in_array($page->id(), $excluded, true));
    }

    // ------------------------------------------------------------------
    // 5. Sort: tended desc → date desc → undated last
    // ------------------------------------------------------------------
    $stamp = function ($page) {
        if ($page->tended()->isNotEmpty()) {
            return strtotime($page->tended()->value());
        }
        if ($page->date()->isNotEmpty()) {
            return strtotime($page->date()->value());
        }
        return 0;
    };

    $arr = [];
    foreach ($pool as $page) {
        $arr[$page->id()] = $page;
    }
    uasort($arr, fn($a, $b) => $stamp($b) <=> $stamp($a));

    return new Pages($arr);
}
