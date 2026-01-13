<?php

use Kirby\Cms\Pages;

function generateSectionFeed(string $section, string $format)
{
    $configPath = kirby()->root('config') . '/feeds.php';

    if (!file_exists($configPath)) {
        return false;
    }

    $config = require $configPath;
    $sections = $config['sections'] ?? [];
    $defaults = $config['defaults'] ?? [];

    // Validate section exists in config
    if (!isset($sections[$section])) {
        return false;
    }

    $sectionConfig = $sections[$section];
    $snippetFormat = $format === 'feed' ? 'json' : $format;

    // Check if this is a combined feed or single section
    if (isset($sectionConfig['sections']) && is_array($sectionConfig['sections'])) {
        // Combined feed - collect from multiple sections
        $items = new Pages();
        foreach ($sectionConfig['sections'] as $subSection) {
            $subSectionPage = page($subSection);
            if ($subSectionPage) {
                $children = $subSectionPage->children()->listed();
                if ($children->count() > 0) {
                    $items = $items->add($children);
                }
            }
        }

        if ($items->count() === 0) {
            return feed(fn() => new Pages(), array_merge($defaults, [
                'title' => site()->title() . ' - ' . ucfirst($section),
                'description' => $sectionConfig['description'] ?? 'Latest ' . $section,
                'link' => $section,
                'snippet' => 'feed/' . $snippetFormat,
                'feedurl' => site()->url() . '/' . $section . '/' . $format,
                'modified' => time(),
            ]));
        }

        $options = array_merge($defaults, [
            'title' => site()->title() . ' - ' . ucfirst($section) . ' ' . strtoupper($snippetFormat),
            'description' => $sectionConfig['description'] ?? 'Latest ' . $section,
            'link' => $section,
            'snippet' => 'feed/' . $snippetFormat,
            'feedurl' => site()->url() . '/' . $section . '/' . $format,
            'modified' => time(),
            'item' => function($page) {
                $parent = $page->parent();
                $parentSection = $parent ? $parent->slug() : 'unknown';
                return generateFeedItem($page, $parentSection);
            }
        ]);

        $limit = $sectionConfig['limit'] ?? 20;
        return feed(fn() => $items->sortBy('date', 'desc')->limit($limit), $options);

    } else {
        // Single section feed
        if (!page($section)) {
            return false;
        }

        $options = array_merge($defaults, [
            'title' => site()->title() . ' - ' . ucfirst($section) . ' ' . strtoupper($snippetFormat),
            'description' => $sectionConfig['description'] ?? 'Latest ' . $section,
            'link' => $section,
            'snippet' => 'feed/' . $snippetFormat,
            'feedurl' => site()->url() . '/' . $section . '/' . $format,
            'modified' => time(),
            'item' => function($page) use ($section) {
                return generateFeedItem($page, $section);
            }
        ]);

        $limit = $sectionConfig['limit'] ?? 20;

        $pages = page($section)->children()->listed();
        if ($pages->count() === 0) {
            return feed(fn() => new Pages(), $options);
        }

        return feed(fn() => $pages->flip()->limit($limit), $options);
    }
}

function generateMainFeed(string $format)
{
    $configPath = kirby()->root('config') . '/feeds.php';

    if (!file_exists($configPath)) {
        return false;
    }

    $config = require $configPath;
    $sections = $config['sections'] ?? [];
    $defaults = $config['defaults'] ?? [];

    // Collect all actual page sections (not combined feeds)
    $items = new Pages();
    foreach ($sections as $sectionKey => $sectionConfig) {
        // Skip combined feeds in the main feed to avoid duplicates
        if (isset($sectionConfig['sections'])) {
            continue;
        }

        $sectionPage = page($sectionKey);
        if ($sectionPage) {
            $children = $sectionPage->children()->listed();
            if ($children->count() > 0) {
                $items = $items->add($children);
            }
        }
    }

    $snippetFormat = $format === 'feed' ? 'json' : $format;

    $options = array_merge($defaults, [
        'title' => site()->title() . ' - All Content ' . strtoupper($snippetFormat),
        'description' => 'The latest content from ' . site()->title(),
        'link' => $format,
        'snippet' => 'feed/' . $snippetFormat,
        'feedurl' => site()->url() . '/' . $format,
        'modified' => time(),
        'item' => function($page) {
            $parent = $page->parent();
            $section = $parent ? $parent->slug() : 'unknown';
            return generateFeedItem($page, $section);
        }
    ]);

    if ($items->count() === 0) {
        return feed(fn() => new Pages(), $options);
    }

    return feed(fn() => $items->sortBy('date', 'desc')->limit(20), $options);
}

function generateFeedItem($page, string $section): array
{
    if (!$page || !is_object($page)) {
        return [];
    }

    $item = [
        'title' => $page->title()->value(),
        'link' => $page->url(),
        'pubDate' => $page->date()->exists()
            ? date('r', $page->date()->toTimestamp())
            : date('r', $page->modified()),
    ];

    if ($page->text()->exists()) {
        $item['description'] = $page->text()->kirbytext()->value();
    }

    if ($section === 'links' && $page->website()->exists() && $page->website()->isNotEmpty()) {
        $item['guid'] = $page->website()->value();
    } else {
        $item['guid'] = $page->url();
    }

    if ($page->tags()->exists() && $page->tags()->isNotEmpty()) {
        $tags = $page->tags()->split();
        if (is_array($tags) && count($tags) > 0) {
            $categories = [];
            foreach ($tags as $tag) {
                if (!empty($tag)) {
                    $categories[] = ['name' => $tag];
                }
            }
            if (!empty($categories)) {
                $item['category'] = $categories;
            }
        }
    }

    return $item;
}
function generateTagFeed(string $tag, string $format, ?string $section = null)
{
    $configPath = kirby()->root('config') . '/feeds.php';

    if (!file_exists($configPath)) {
        return false;
    }

    $config = require $configPath;
    $sections = $config['sections'] ?? [];
    $defaults = $config['defaults'] ?? [];

    $snippetFormat = $format === 'feed' ? 'json' : $format;

    // Normalize tag for comparison
    $searchTag = strtolower(trim($tag));

    // Collect items with this tag
    $items = new Pages();

    if ($section) {
        // Tag feed for specific section
        if (!isset($sections[$section])) {
            return false;
        }

        $sectionConfig = $sections[$section];

        // Check if combined feed or single section
        if (isset($sectionConfig['sections']) && is_array($sectionConfig['sections'])) {
            // Combined feed - search in all subsections
            foreach ($sectionConfig['sections'] as $subSection) {
                $subSectionPage = page($subSection);
                if ($subSectionPage) {
                    $filtered = filterByTag($subSectionPage->children()->listed(), $searchTag);
                    if ($filtered->count() > 0) {
                        $items = $items->add($filtered);
                    }
                }
            }
        } else {
            // Single section
            $sectionPage = page($section);
            if (!$sectionPage) {
                return false;
            }
            $items = filterByTag($sectionPage->children()->listed(), $searchTag);
        }

        $title = site()->title() . ' - ' . ucfirst($section) . ' tagged "' . $tag . '"';
        $description = ucfirst($section) . ' entries tagged with "' . $tag . '"';
        $link = $section . '/tags/' . $tag;
        $feedurl = site()->url() . '/' . $section . '/tags/' . $tag . '/' . $format;

    } else {
        // Tag feed across all content
        foreach ($sections as $sectionKey => $sectionConfig) {
            // Skip combined feeds to avoid duplicates
            if (isset($sectionConfig['sections'])) {
                continue;
            }

            $sectionPage = page($sectionKey);
            if ($sectionPage) {
                $filtered = filterByTag($sectionPage->children()->listed(), $searchTag);
                if ($filtered->count() > 0) {
                    $items = $items->add($filtered);
                }
            }
        }

        $title = site()->title() . ' - Everything tagged "' . $tag . '"';
        $description = 'All content tagged with "' . $tag . '"';
        $link = 'tags/' . $tag;
        $feedurl = site()->url() . '/tags/' . $tag . '/' . $format;
    }

    $options = array_merge($defaults, [
        'title' => $title . ' ' . strtoupper($snippetFormat),
        'description' => $description,
        'link' => $link,
        'snippet' => 'feed/' . $snippetFormat,
        'feedurl' => $feedurl,
        'modified' => time(),
        'item' => function($page) {
            $parent = $page->parent();
            $pageSection = $parent ? $parent->slug() : 'unknown';
            return generateFeedItem($page, $pageSection);
        }
    ]);

    if ($items->count() === 0) {
        return feed(fn() => new Pages(), $options);
    }

    return feed(fn() => $items->sortBy('date', 'desc')->limit(50), $options);
}

function filterByTag($pages, string $searchTag)
{
    return $pages->filter(function($page) use ($searchTag) {
        if (!$page->tags()->exists() || $page->tags()->isEmpty()) {
            return false;
        }

        $pageTags = $page->tags()->split();
        if (!is_array($pageTags)) {
            return false;
        }

        foreach ($pageTags as $pageTag) {
            if (strtolower(trim($pageTag)) === $searchTag) {
                return true;
            }
        }

        return false;
    });
}
