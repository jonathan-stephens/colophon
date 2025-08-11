<?php
// site/controllers/metrics.php - Enhanced Version

return function ($site, $pages, $page) {

    // Get excluded templates from page settings or defaults
    $excludedTemplates = $page->excludedTemplates()->split(',');
    if (empty($excludedTemplates)) {
        $excludedTemplates = ['home', 'error', 'metrics'];
    }

    // Get all published pages (excluding system pages)
    $allPages = $site->index()->published()->filterBy('template', 'not in', $excludedTemplates);

    // Initialize data structures
    $contentTypeStats = [];
    $timelineData = [];
    $authorStats = [];
    $tagStats = [];
    $categoryStats = [];
    $imageStats = [];
    $totalWords = 0;
    $totalCharacters = 0;
    $totalReadingTime = 0;
    $totalPages = $allPages->count();
    $totalImages = 0;
    $seoScores = [];

    // Process each page
    foreach ($allPages as $contentPage) {
        $template = $contentPage->template()->name();

        // Initialize content type stats if not exists
        if (!isset($contentTypeStats[$template])) {
            $contentTypeStats[$template] = [
                'count' => 0,
                'words' => 0,
                'characters' => 0,
                'readingTime' => 0,
                'wordCounts' => [],
                'minWords' => 0,
                'maxWords' => 0,
                'avgWords' => 0,
                'medianWords' => 0,
                'totalDaysOld' => 0,
                'hasImages' => 0,
                'totalImages' => 0,
                'seoScore' => 0
            ];
        }

        // Get content text from various fields
        $contentText = '';
        $textFields = ['text', 'content', 'body', 'excerpt', 'description', 'intro', 'summary'];

        foreach ($textFields as $field) {
            if ($contentPage->$field()->exists()) {
                $fieldContent = $contentPage->$field()->kirbytextinline();
                $contentText .= ' ' . $fieldContent;
            }
        }

        // Clean and analyze text
        $cleanText = strip_tags($contentText);
        $wordCount = str_word_count($cleanText);
        $charCount = strlen($cleanText);
        $readingTime = max(1, ceil($wordCount / 200)); // 200 words per minute

        // Count images
        $pageImages = $contentPage->images()->count();
        $hasImages = $pageImages > 0 ? 1 : 0;
        $totalImages += $pageImages;

        // Basic SEO scoring
        $seoScore = 0;
        if ($contentPage->title()->isNotEmpty()) $seoScore += 20;
        if ($contentPage->description()->exists() && $contentPage->description()->isNotEmpty()) $seoScore += 20;
        if ($wordCount >= 300) $seoScore += 20;
        if ($wordCount <= 2000) $seoScore += 10; // Not too long
        if ($pageImages > 0) $seoScore += 15;
        if ($contentPage->tags()->exists() && $contentPage->tags()->isNotEmpty()) $seoScore += 15;

        // Update content type stats
        $contentTypeStats[$template]['count']++;
        $contentTypeStats[$template]['words'] += $wordCount;
        $contentTypeStats[$template]['characters'] += $charCount;
        $contentTypeStats[$template]['readingTime'] += $readingTime;
        $contentTypeStats[$template]['wordCounts'][] = $wordCount;
        $contentTypeStats[$template]['hasImages'] += $hasImages;
        $contentTypeStats[$template]['totalImages'] += $pageImages;
        $contentTypeStats[$template]['seoScore'] += $seoScore;

        // Calculate page age
        $created = $contentPage->date()->toTimestamp();
        $daysOld = floor((time() - $created) / (60 * 60 * 24));
        $contentTypeStats[$template]['totalDaysOld'] += $daysOld;

        // Author statistics
        if ($contentPage->author()->exists()) {
            $author = (string)$contentPage->author();
            if (!isset($authorStats[$author])) {
                $authorStats[$author] = ['count' => 0, 'words' => 0];
            }
            $authorStats[$author]['count']++;
            $authorStats[$author]['words'] += $wordCount;
        }

        // Tag statistics
        if ($contentPage->tags()->exists()) {
            $tags = $contentPage->tags()->split(',');
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!isset($tagStats[$tag])) {
                    $tagStats[$tag] = 0;
                }
                $tagStats[$tag]++;
            }
        }

        // Category statistics
        if ($contentPage->category()->exists()) {
            $category = (string)$contentPage->category();
            if (!isset($categoryStats[$category])) {
                $categoryStats[$category] = ['count' => 0, 'words' => 0];
            }
            $categoryStats[$category]['count']++;
            $categoryStats[$category]['words'] += $wordCount;
        }

        // Update totals
        $totalWords += $wordCount;
        $totalCharacters += $charCount;
        $totalReadingTime += $readingTime;

        // Timeline data (last 12 months)
        $monthYear = date('M Y', $created);
        $cutoffDate = strtotime('-12 months');

        if ($created >= $cutoffDate) {
            if (!isset($timelineData[$monthYear])) {
                $timelineData[$monthYear] = 0;
            }
            $timelineData[$monthYear]++;
        }
    }

    // Calculate statistical measures for each content type
    foreach ($contentTypeStats as $type => &$stats) {
        if ($stats['count'] > 0) {
            $wordCounts = $stats['wordCounts'];
            sort($wordCounts);

            $stats['minWords'] = min($wordCounts);
            $stats['maxWords'] = max($wordCounts);
            $stats['avgWords'] = round(array_sum($wordCounts) / count($wordCounts));
            $stats['avgSeoScore'] = round($stats['seoScore'] / $stats['count']);
            $stats['imagePercentage'] = round(($stats['hasImages'] / $stats['count']) * 100);

            // Calculate median
            $count = count($wordCounts);
            if ($count % 2 == 0) {
                $stats['medianWords'] = round(($wordCounts[$count/2 - 1] + $wordCounts[$count/2]) / 2);
            } else {
                $stats['medianWords'] = $wordCounts[floor($count/2)];
            }
        }
    }

    // Sort timeline data chronologically
    $sortedTimeline = [];
    for ($i = 11; $i >= 0; $i--) {
        $month = date('M Y', strtotime("-$i months"));
        $sortedTimeline[$month] = isset($timelineData[$month]) ? $timelineData[$month] : 0;
    }

    // Calculate content freshness
    $freshnessStats = [];
    foreach ($contentTypeStats as $type => $stats) {
        if ($stats['count'] > 0) {
            $avgDays = round($stats['totalDaysOld'] / $stats['count']);

            // Categorize freshness
            if ($avgDays <= 30) {
                $class = 'fresh';
                $label = 'Fresh';
            } elseif ($avgDays <= 180) {
                $class = 'moderate';
                $label = 'Moderate';
            } else {
                $class = 'stale';
                $label = 'Needs Update';
            }

            $freshnessStats[$type] = [
                'avgDays' => $avgDays,
                'class' => $class,
                'label' => $label
            ];
        }
    }

    // Sort author stats by post count
    arsort($authorStats);
    $topAuthors = array_slice($authorStats, 0, 10, true);

    // Sort tag stats by frequency
    arsort($tagStats);
    $topTags = array_slice($tagStats, 0, 20, true);

    // Sort category stats by post count
    uasort($categoryStats, function($a, $b) {
        return $b['count'] - $a['count'];
    });

    // Calculate additional useful metrics
    $averageWordsPerPage = $totalPages > 0 ? round($totalWords / $totalPages) : 0;
    $averageImagesPerPage = $totalPages > 0 ? round($totalImages / $totalPages, 1) : 0;
    $contentTypes = count($contentTypeStats);
    $totalAuthors = count($authorStats);
    $totalTags = count($tagStats);
    $totalCategories = count($categoryStats);

    // Content quality insights
    $shortContentPages = 0;
    $longContentPages = 0;
    $mediumContentPages = 0;
    $pagesWithoutImages = 0;

    foreach ($allPages as $contentPage) {
        $contentText = '';
        $textFields = ['text', 'content', 'body', 'excerpt', 'description', 'intro'];

        foreach ($textFields as $field) {
            if ($contentPage->$field()->exists()) {
                $contentText .= ' ' . $contentPage->$field()->kirbytextinline();
            }
        }

        $wordCount = str_word_count(strip_tags($contentText));

        if ($wordCount < 300) {
            $shortContentPages++;
        } elseif ($wordCount > 1500) {
            $longContentPages++;
        } else {
            $mediumContentPages++;
        }

        if ($contentPage->images()->count() == 0) {
            $pagesWithoutImages++;
        }
    }

    // Content engagement potential
    $engagementScore = 0;
    if ($averageWordsPerPage >= 500) $engagementScore += 25;
    if ($averageImagesPerPage >= 1) $engagementScore += 25;
    if (($pagesWithoutImages / $totalPages) < 0.3) $engagementScore += 25;
    if (count($topTags) >= 10) $engagementScore += 25;

    // Get top content types by word count
    uasort($contentTypeStats, function($a, $b) {
        return $b['words'] - $a['words'];
    });

    return [
        'contentTypeStats' => $contentTypeStats,
        'freshnessStats' => $freshnessStats,
        'timelineData' => $sortedTimeline,
        'authorStats' => $topAuthors,
        'tagStats' => $topTags,
        'categoryStats' => $categoryStats,
        'totalWords' => $totalWords,
        'totalCharacters' => $totalCharacters,
        'totalReadingTime' => $totalReadingTime,
        'totalPages' => $totalPages,
        'totalImages' => $totalImages,
        'averageWordsPerPage' => $averageWordsPerPage,
        'averageImagesPerPage' => $averageImagesPerPage,
        'contentTypes' => $contentTypes,
        'totalAuthors' => $totalAuthors,
        'totalTags' => $totalTags,
        'totalCategories' => $totalCategories,
        'shortContentPages' => $shortContentPages,
        'mediumContentPages' => $mediumContentPages,
        'longContentPages' => $longContentPages,
        'pagesWithoutImages' => $pagesWithoutImages,
        'engagementScore' => $engagementScore
    ];
};
