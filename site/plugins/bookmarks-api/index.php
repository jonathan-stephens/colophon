<?php

/**
 * Kirby Bookmarks API Plugin - With Improved Session Auth Support
 */

Kirby::plugin('jonathan-stephens/bookmarks-api', [
    'api' => [
        'routes' => [
            // Fetch metadata from URL
            [
                'pattern' => 'bookmarks/fetch-metadata',
                'method' => 'POST',
                'auth' => false,
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

                        // Fetch the HTML content
                        $html = @file_get_contents($url);

                        if ($html === false) {
                            return [
                                'status' => 'error',
                                'message' => 'Could not fetch URL'
                            ];
                        }

                        $metadata = [
                            'author' => null,
                            'tags' => null,
                            'title' => null
                        ];

                        // Create DOMDocument
                        $dom = new DOMDocument();
                        @$dom->loadHTML($html);
                        $xpath = new DOMXPath($dom);

                        // Extract author from Schema.org markup
                        $authorNodes = $xpath->query('//*[@itemprop="author"]//*[@itemprop="name"]');
                        if ($authorNodes->length > 0) {
                            $metadata['author'] = trim($authorNodes->item(0)->textContent);
                        }

                        // If not found, try direct itemprop="author"
                        if (!$metadata['author']) {
                            $authorNodes = $xpath->query('//*[@itemprop="author"]');
                            if ($authorNodes->length > 0) {
                                $authorNode = $authorNodes->item(0);
                                $nameNode = $xpath->query('.//*[@itemprop="name"]', $authorNode);
                                if ($nameNode->length > 0) {
                                    $metadata['author'] = trim($nameNode->item(0)->textContent);
                                } else {
                                    $metadata['author'] = trim($authorNode->textContent);
                                }
                            }
                        }

                        // Also check meta tags for author
                        if (!$metadata['author']) {
                            $metaAuthor = $xpath->query('//meta[@name="author"]/@content');
                            if ($metaAuthor->length > 0) {
                                $metadata['author'] = trim($metaAuthor->item(0)->textContent);
                            }
                        }

                        // Extract tags from various sources
                        $tags = [];

                        // Schema.org keywords
                        $keywordNodes = $xpath->query('//meta[@itemprop="keywords"]/@content');
                        if ($keywordNodes->length > 0) {
                            $keywords = $keywordNodes->item(0)->textContent;
                            $tags = array_merge($tags, array_map('trim', explode(',', $keywords)));
                        }

                        // Meta keywords
                        $metaKeywords = $xpath->query('//meta[@name="keywords"]/@content');
                        if ($metaKeywords->length > 0) {
                            $keywords = $metaKeywords->item(0)->textContent;
                            $tags = array_merge($tags, array_map('trim', explode(',', $keywords)));
                        }

                        // Article tags (Open Graph)
                        $articleTags = $xpath->query('//meta[@property="article:tag"]/@content');
                        foreach ($articleTags as $tag) {
                            $tags[] = trim($tag->textContent);
                        }

                        // Categories
                        $categories = $xpath->query('//meta[@name="category"]/@content');
                        if ($categories->length > 0) {
                            $tags[] = trim($categories->item(0)->textContent);
                        }

                        // News keywords
                        $newsKeywords = $xpath->query('//meta[@name="news_keywords"]/@content');
                        if ($newsKeywords->length > 0) {
                            $keywords = $newsKeywords->item(0)->textContent;
                            $tags = array_merge($tags, array_map('trim', explode(',', $keywords)));
                        }

                        // Remove duplicates and empty values
                        $tags = array_filter(array_unique($tags));

                        if (!empty($tags)) {
                            $metadata['tags'] = implode(', ', $tags);
                        }

                        // Extract title
                        $titleNodes = $xpath->query('//title');
                        if ($titleNodes->length > 0) {
                            $metadata['title'] = trim($titleNodes->item(0)->textContent);
                        }

                        // Check og:title
                        $ogTitle = $xpath->query('//meta[@property="og:title"]/@content');
                        if ($ogTitle->length > 0) {
                            $metadata['title'] = trim($ogTitle->item(0)->textContent);
                        }

                        return [
                            'status' => 'success',
                            'data' => $metadata
                        ];

                    } catch (Exception $e) {
                        return [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                    }
                }
            ],

            // Add full bookmark - IMPROVED SESSION HANDLING
            [
                'pattern' => 'bookmarks/add',
                'method' => 'POST',
                'auth' => false,
                'action' => function () {
                    try {
                        $kirby = kirby();
                        $user = null;

                        // DEBUG: Log authentication attempts
                        error_log('=== BOOKMARK ADD DEBUG ===');

                        // Method 1: Check existing session FIRST
                        $user = $kirby->user();
                        error_log('Session user: ' . ($user ? $user->email() : 'NONE'));

                        // Method 2: Try Basic Auth header if no session
                        if (!$user) {
                            $authHeader = $kirby->request()->header('Authorization');
                            error_log('Auth header present: ' . ($authHeader ? 'YES' : 'NO'));

                            if ($authHeader && strpos($authHeader, 'Basic ') === 0) {
                                $credentials = base64_decode(substr($authHeader, 6));
                                list($email, $password) = explode(':', $credentials, 2);
                                error_log('Attempting Basic Auth for: ' . $email);

                                try {
                                    // Don't persist the session for API calls
                                    $user = $kirby->auth()->login($email, $password, false);
                                    error_log('Basic Auth SUCCESS for: ' . $email);
                                } catch (Exception $e) {
                                    error_log('Basic Auth FAILED: ' . $e->getMessage());
                                }
                            }
                        }

                        // Method 3: Try to impersonate if we're in a trusted environment
                        // This is a fallback for testing - REMOVE IN PRODUCTION
                        if (!$user && option('debug', false)) {
                            $firstUser = $kirby->users()->first();
                            if ($firstUser) {
                                $user = $firstUser;
                                error_log('DEBUG MODE: Using first user: ' . $user->email());
                            }
                        }

                        // If still no user, return detailed error
                        if (!$user) {
                            error_log('Authentication FAILED - no valid user found');

                            return [
                                'status' => 'error',
                                'message' => 'Authentication required. Please log in to the panel first.',
                                'debug' => option('debug', false) ? [
                                    'session_user' => 'none',
                                    'auth_header' => isset($authHeader) ? 'present' : 'missing',
                                    'cookies' => array_keys($_COOKIE),
                                ] : null
                            ];
                        }

                        error_log('Proceeding with user: ' . $user->email());

                        $data = $kirby->request()->data();

                        if (empty($data['website'])) {
                            return [
                                'status' => 'error',
                                'message' => 'URL is required'
                            ];
                        }

                        $url = $data['website'];
                        $parsed = parse_url($url);
                        $host = $parsed['host'] ?? '';
                        $tld = substr($host, strrpos($host, '.') + 1);

                        $slug = Str::slug($host . '-' . time());

                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }

                        $content = [
                            'title' => $data['title'] ?? '',
                            'website' => $url,
                            'tld' => $data['tld'] ?? $tld,
                            'text' => $data['text'] ?? '',
                            'tags' => $data['tags'] ?? '',
                            'author' => $data['author'] ?? '',
                        ];

                        // Impersonate the authenticated user for content creation
                        $kirby->impersonate('kirby');

                        $bookmark = $linksPage->createChild([
                            'slug' => $slug,
                            'template' => 'link',
                            'content' => $content,
                            'num' => date('YmdHis')
                        ]);

                        // Publish immediately
                        $bookmark->changeStatus('listed');

                        error_log('Bookmark created successfully: ' . $bookmark->id());

                        return [
                            'status' => 'success',
                            'message' => 'Bookmark added successfully',
                            'data' => [
                                'id' => $bookmark->id(),
                                'url' => $bookmark->url()
                            ]
                        ];

                    } catch (Exception $e) {
                        error_log('Bookmark creation ERROR: ' . $e->getMessage());
                        return [
                            'status' => 'error',
                            'message' => $e->getMessage(),
                            'trace' => option('debug', false) ? $e->getTraceAsString() : null
                        ];
                    }
                }
            ],

            // Quick add bookmark - IMPROVED SESSION HANDLING
            [
                'pattern' => 'bookmarks/quick-add',
                'method' => 'POST',
                'auth' => false,
                'action' => function () {
                    try {
                        $kirby = kirby();
                        $user = null;

                        // DEBUG: Log authentication attempts
                        error_log('=== QUICK ADD DEBUG ===');

                        // Method 1: Check existing session FIRST
                        $user = $kirby->user();
                        error_log('Session user: ' . ($user ? $user->email() : 'NONE'));

                        // Method 2: Try Basic Auth header if no session
                        if (!$user) {
                            $authHeader = $kirby->request()->header('Authorization');
                            error_log('Auth header present: ' . ($authHeader ? 'YES' : 'NO'));

                            if ($authHeader && strpos($authHeader, 'Basic ') === 0) {
                                $credentials = base64_decode(substr($authHeader, 6));
                                list($email, $password) = explode(':', $credentials, 2);
                                error_log('Attempting Basic Auth for: ' . $email);

                                try {
                                    $user = $kirby->auth()->login($email, $password, false);
                                    error_log('Basic Auth SUCCESS for: ' . $email);
                                } catch (Exception $e) {
                                    error_log('Basic Auth FAILED: ' . $e->getMessage());
                                }
                            }
                        }

                        // Method 3: Try to impersonate if we're in a trusted environment
                        // This is a fallback for testing - REMOVE IN PRODUCTION
                        if (!$user && option('debug', false)) {
                            $firstUser = $kirby->users()->first();
                            if ($firstUser) {
                                $user = $firstUser;
                                error_log('DEBUG MODE: Using first user: ' . $user->email());
                            }
                        }

                        // If still no user, return detailed error
                        if (!$user) {
                            error_log('Authentication FAILED - no valid user found');

                            return [
                                'status' => 'error',
                                'message' => 'Authentication required. Please log in to the panel first.',
                                'debug' => option('debug', false) ? [
                                    'session_user' => 'none',
                                    'auth_header' => isset($authHeader) ? 'present' : 'missing',
                                    'cookies' => array_keys($_COOKIE),
                                ] : null
                            ];
                        }

                        error_log('Proceeding with user: ' . $user->email());

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

                        $parsed = parse_url($url);
                        $host = $parsed['host'] ?? '';
                        $tld = substr($host, strrpos($host, '.') + 1);

                        $slug = Str::slug($host . '-' . time());

                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }

                        $content = [
                            'title' => $title,
                            'website' => $url,
                            'tld' => $tld,
                            'text' => $text ?: $title,
                            'tags' => 'read-later'
                        ];

                        // Impersonate the authenticated user for content creation
                        $kirby->impersonate('kirby');

                        $bookmark = $linksPage->createChild([
                            'slug' => $slug,
                            'template' => 'link',
                            'content' => $content,
                            'num' => date('YmdHis')
                        ]);

                        // Publish immediately
                        $bookmark->changeStatus('listed');

                        error_log('Quick bookmark created successfully: ' . $bookmark->id());

                        return [
                            'status' => 'success',
                            'message' => 'Bookmark saved',
                            'data' => [
                                'id' => $bookmark->id()
                            ]
                        ];

                    } catch (Exception $e) {
                        error_log('Quick add ERROR: ' . $e->getMessage());
                        return [
                            'status' => 'error',
                            'message' => $e->getMessage(),
                            'trace' => option('debug', false) ? $e->getTraceAsString() : null
                        ];
                    }
                }
            ]
        ]
    ]
]);
