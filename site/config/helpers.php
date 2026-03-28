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
/**
 * streamApplyFilters()
 *
 * Applies active filter selections to a Pages collection.
 * Empty string or absent key = dimension is unfiltered.
 *
 * @param  Pages  $pool
 * @param  array  $filters  ['type' => 'essay', 'stage' => 'evergreen', 'listed' => 'draft']
 * @return Pages
 */
function streamApplyFilters(Pages $pool, array $filters): Pages
{
    if (!empty($filters['type'])) {
        $type = $filters['type'];
        $pool = $pool->filter(fn($p) => $p->intendedTemplate()->name() === $type);
    }

    if (!empty($filters['stage'])) {
        $stage = $filters['stage'];
        $pool  = $pool->filter(fn($p) => (string) $p->growthStatus() === $stage);
    }

    if (!empty($filters['listed'])) {
        $status = $filters['listed'];
        $pool   = $pool->filter(fn($p) => $p->status() === $status);
    }

    return $pool;
}

/**
 * streamSort()
 *
 * Sorts a stream Pages collection by a named sort key.
 * Valid keys: tended_desc, tended_asc, date_desc, date_asc
 * Falls back to tended_desc for unrecognised values.
 *
 * @param  Pages  $pool
 * @param  string $sort
 * @return Pages
 */
function streamSort(Pages $pool, string $sort = 'tended_desc'): Pages
{
    $parts = explode('_', $sort, 2);
    $field = $parts[0] ?? 'tended';
    $dir   = $parts[1] ?? 'desc';

    if (!in_array($field, ['tended', 'date'], true))    $field = 'tended';
    if (!in_array($dir,   ['asc',   'desc'], true))     $dir   = 'desc';

    $stamp = function ($page) use ($field) {
        $f = $field === 'tended' ? $page->tended() : $page->date();
        return $f->isNotEmpty() ? strtotime($f->value()) : 0;
    };

    $arr = [];
    foreach ($pool as $page) {
        $arr[$page->id()] = $page;
    }

    uasort($arr, function ($a, $b) use ($stamp, $dir) {
        $ta = $stamp($a);
        $tb = $stamp($b);
        return $dir === 'asc' ? $ta <=> $tb : $tb <=> $ta;
    });

    return new Pages($arr);
}

/**
 * streamFacets()
 *
 * Computes faceted counts for every filter dimension.
 *
 * "Faceted" means: the count for dimension D reflects the pool with
 * all OTHER active filters applied — so option counts update as you
 * filter, always showing what's reachable from the current selection.
 *
 * Returns:
 * [
 *   'type'   => ['essay' => ['label' => 'Essay',     'count' => 12], ...],
 *   'stage'  => ['evergreen' => ['label' => 'Evergreen', 'count' => 5], ...],
 *   'listed' => ['draft' => ['label' => 'draft',      'count' => 3], ...],
 * ]
 *
 * Counts are sorted: highest first, then alphabetically.
 * Zero-count options are included so the UI can show disabled states.
 *
 * USAGE — in stream template:
 *   $facets = streamFacets($pool, $activeFilters);
 *
 * USAGE — in any other template for a scoped facet:
 *   $pool   = streamCollection(site(), kirby(), ['subtrees' => ['essays']]);
 *   $facets = streamFacets($pool, []);
 *
 * @param  Pages  $pool           Full unfiltered stream collection
 * @param  array  $activeFilters  Same shape as streamApplyFilters $filters param
 * @return array
 */
function streamFacets(Pages $pool, array $activeFilters): array
{
    $dimensions = ['type', 'stage', 'listed'];

    // Collect all possible values per dimension from the full pool first,
    // so zero-count options appear in the output for disabled states.
    $allValues = ['type' => [], 'stage' => [], 'listed' => []];

    foreach ($pool as $page) {
        $t = $page->intendedTemplate()->name();
        if ($t && !isset($allValues['type'][$t])) {
            $allValues['type'][$t] = $page->blueprint()->title();
        }

        $s = (string) $page->growthStatus();
        if ($s !== '' && !isset($allValues['stage'][$s])) {
            $allValues['stage'][$s] = $s;
        }

        $l = $page->status();
        if ($l && !isset($allValues['listed'][$l])) {
            $allValues['listed'][$l] = $l;
        }
    }

    $facets = [];

    foreach ($dimensions as $dim) {
        // Apply every active filter except this dimension's own
        $otherFilters = array_diff_key($activeFilters, [$dim => true]);
        $subset       = streamApplyFilters($pool, $otherFilters);

        // Count occurrences in the cross-filtered subset
        $counts = [];
        foreach ($subset as $page) {
            switch ($dim) {
                case 'type':
                    $val   = $page->intendedTemplate()->name();
                    $label = $page->blueprint()->title();
                    break;
                case 'stage':
                    $val   = (string) $page->growthStatus();
                    $label = $val;
                    break;
                case 'listed':
                    $val   = $page->status();
                    $label = $val;
                    break;
                default:
                    continue 2;
            }

            if ($val === '') continue;

            if (!isset($counts[$val])) {
                $counts[$val] = ['label' => $label, 'count' => 0];
            }
            $counts[$val]['count']++;
        }

        // Ensure every known value appears, even at zero
        foreach ($allValues[$dim] as $val => $label) {
            if (!isset($counts[$val])) {
                $counts[$val] = ['label' => $label, 'count' => 0];
            }
        }

        // Sort: count desc, then label asc
        uasort($counts, function ($a, $b) {
            if ($b['count'] !== $a['count']) return $b['count'] <=> $a['count'];
            return strcmp((string) $a['label'], (string) $b['label']);
        });

        $facets[$dim] = $counts;
    }

    return $facets;
}
