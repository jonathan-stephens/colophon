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
