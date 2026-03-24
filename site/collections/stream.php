<?php

/**
 * Full-site stream collection.
 *
 * Uses config defaults from config.php 'stream' key.
 * To create a scoped sub-collection, copy this file, rename it,
 * and pass overrides to streamCollection() — see helpers.php for
 * the full options reference.
 *
 * Examples:
 *
 *   // All books only
 *   return streamCollection($site, $kirby, ['subtrees' => ['books']]);
 *
 *   // Essays and notes, direct children only
 *   return streamCollection($site, $kirby, [
 *       'subtrees' => ['essays', 'notes'],
 *       'maxDepth' => 2,
 *   ]);
 *
 *   // Drafts only (logged-in users only — public visitors see nothing)
 *   return streamCollection($site, $kirby, ['status' => 'drafts']);
 *
 *   // Listed and unlisted but not drafts
 *   return streamCollection($site, $kirby, ['status' => ['listed', 'unlisted']]);
 *
 * Retrieve in any template or snippet:
 *
 *   $stream = $kirby->collection('stream');
 */
return function ($site, $pages, $kirby) {
    return streamCollection($site, $kirby);
};
