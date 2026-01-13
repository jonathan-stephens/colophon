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

    // Validate section
    if (!isset($sections[$section]) || !page($section)) {
        return false;
    }

    $sectionConfig = $sections[$section];
    $snippetFormat = $format === 'feed' ? 'json' : $format;

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
        // Return empty feed if no pages
        return feed(fn() => new Pages(), $options);
    }

    return feed(fn() => $pages->flip()->limit($limit), $options);
}

function generateMainFeed(string $format)
{
    $configPath = kirby()->root('config') . '/feeds.php';

    if (!file_exists($configPath)) {
        // Fallback if config doesn't exist
        return false;
    }

    $config = require $configPath;
    $sections = array_keys($config['sections'] ?? []);
    $defaults = $config['defaults'] ?? [];

    // Collect entries from all sections
    $items = new Pages();
    foreach ($sections as $section) {
        $sectionPage = page($section);
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
        // Return empty feed if no items
        return feed(fn() => new Pages(), $options);
    }

    return feed(fn() => $items->sortBy('date', 'desc')->limit(20), $options);
}

function generateFeedItem($page, string $section): array
{
    // Safety checks
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

    // Add description if text field exists
    if ($page->text()->exists()) {
        $item['description'] = $page->text()->kirbytext()->value();
    }

    // Add GUID (use website URL for links section if it exists)
    if ($section === 'links' && $page->website()->exists() && $page->website()->isNotEmpty()) {
        $item['guid'] = $page->website()->value();
    } else {
        $item['guid'] = $page->url();
    }

    // Add categories/tags if they exist
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
