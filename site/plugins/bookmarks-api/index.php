<?php

/**
 * Kirby Bookmarks API Plugin
 * Place this in site/plugins/bookmarks-api/index.php
 */

Kirby::plugin('jonathan-stephens/bookmarks-api', [
    'api' => [
        'routes' => [
            [
                'pattern' => 'bookmarks/add',
                'method' => 'POST',
                'auth' => true, // Requires authentication
                'action' => function () {
                    try {
                        $kirby = kirby();
                        $data = $kirby->request()->data();

                        // Validate required fields
                        if (empty($data['website'])) {
                            return [
                                'status' => 'error',
                                'message' => 'URL is required'
                            ];
                        }

                        // Extract TLD from URL
                        $url = $data['website'];
                        $parsed = parse_url($url);
                        $host = $parsed['host'] ?? '';
                        $tld = substr($host, strrpos($host, '.') + 1);

                        // Generate slug from URL
                        $slug = Str::slug($host . '-' . time());

                        // Find or create the links parent page
                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }

                        // Prepare content
                        $content = [
                            'title' => $data['title'] ?? '',
                            'website' => $url,
                            'tld' => $tld,
                            'text' => $data['text'] ?? '',
                            'tags' => $data['tags'] ?? '',
                            'author' => $data['author'] ?? '',
                        ];

                        // Create the bookmark page
                        $bookmark = $linksPage->createChild([
                            'slug' => $slug,
                            'template' => 'link',
                            'content' => $content,
                            'num' => date('YmdHis') // date-based numbering
                        ]);

                        // Publish the page immediately
                        $bookmark->changeStatus('listed');

                        return [
                            'status' => 'success',
                            'message' => 'Bookmark added successfully',
                            'data' => [
                                'id' => $bookmark->id(),
                                'url' => $bookmark->url()
                            ]
                        ];

                    } catch (Exception $e) {
                        return [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                    }
                }
            ],
            [
                'pattern' => 'bookmarks/quick-add',
                'method' => 'POST',
                'auth' => true,
                'action' => function () {
                    try {
                        $kirby = kirby();
                        $data = $kirby->request()->data();

                        if (empty($data['url'])) {
                            return [
                                'status' => 'error',
                                'message' => 'URL is required'
                            ];
                        }

                        $url = $data['url'];
                        $title = $data['title'] ?? '';
                        $text = $data['text'] ?? '';

                        // Extract TLD
                        $parsed = parse_url($url);
                        $host = $parsed['host'] ?? '';
                        $tld = substr($host, strrpos($host, '.') + 1);

                        // Generate slug
                        $slug = Str::slug($host . '-' . time());

                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }

                        // Create with minimal data
                        $content = [
                            'website' => $url,
                            'tld' => $tld,
                            'text' => $text ?: $title,
                            'tags' => 'read-later'
                        ];

                        $bookmark = $linksPage->createChild([
                            'slug' => $slug,
                            'template' => 'link',
                            'content' => $content,
                            'num' => date('YmdHis')
                        ]);

                        return [
                            'status' => 'success',
                            'message' => 'Bookmark saved',
                            'data' => [
                                'id' => $bookmark->id()
                            ]
                        ];

                    } catch (Exception $e) {
                        return [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                    }
                }
            ]
        ]
    ]
]);
