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

                        // Fetch HTML with user agent to avoid blocks
                        $context = stream_context_create([
                            'http' => [
                                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
                            ]
                        ]);

                        $html = @file_get_contents($url, false, $context);

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

                        // =====================================================
                        // EXTRACT AUTHORS (with multiple author support)
                        // =====================================================
                        $authors = [];

                        // Schema.org author with name
                        $authorNodes = $xpath->query('//*[@itemprop="author"]//*[@itemprop="name"]');
                        foreach ($authorNodes as $node) {
                            $authors[] = trim($node->textContent);
                        }

                        // Direct itemprop="author"
                        if (empty($authors)) {
                            $authorNodes = $xpath->query('//*[@itemprop="author"]');
                            foreach ($authorNodes as $authorNode) {
                                $nameNode = $xpath->query('.//*[@itemprop="name"]', $authorNode);
                                if ($nameNode->length > 0) {
                                    $authors[] = trim($nameNode->item(0)->textContent);
                                } else {
                                    $text = trim($authorNode->textContent);
                                    if (!empty($text) && strlen($text) < 100) { // Reasonable author name length
                                        $authors[] = $text;
                                    }
                                }
                            }
                        }

                        // Meta author tag
                        if (empty($authors)) {
                            $metaAuthor = $xpath->query('//meta[@name="author"]/@content');
                            if ($metaAuthor->length > 0) {
                                $authorText = trim($metaAuthor->item(0)->textContent);
                                // Check if multiple authors separated by common delimiters
                                if (strpos($authorText, ',') !== false) {
                                    $authors = array_map('trim', explode(',', $authorText));
                                } elseif (strpos($authorText, ' and ') !== false) {
                                    $authors = array_map('trim', explode(' and ', $authorText));
                                } elseif (strpos($authorText, '&') !== false) {
                                    $authors = array_map('trim', explode('&', $authorText));
                                } else {
                                    $authors[] = $authorText;
                                }
                            }
                        }

                        // Open Graph article:author
                        if (empty($authors)) {
                            $ogAuthor = $xpath->query('//meta[@property="article:author"]/@content');
                            foreach ($ogAuthor as $node) {
                                $authors[] = trim($node->textContent);
                            }
                        }

                        // Twitter creator
                        if (empty($authors)) {
                            $twitterCreator = $xpath->query('//meta[@name="twitter:creator"]/@content');
                            if ($twitterCreator->length > 0) {
                                $authors[] = trim($twitterCreator->item(0)->textContent);
                            }
                        }

                        // Microformats: p-author or h-card
                        $mfAuthor = $xpath->query('//*[contains(@class, "p-author")]');
                        foreach ($mfAuthor as $node) {
                            $authors[] = trim($node->textContent);
                        }

                        // Clean up authors
                        $authors = array_filter(array_unique($authors));
                        if (!empty($authors)) {
                            $metadata['author'] = implode(', ', $authors);
                        }

                        // =====================================================
                        // EXTRACT TAGS (comprehensive detection)
                        // =====================================================
                        $tags = [];

                        // 1. Schema.org keywords (itemprop="keywords")
                        $schemaKeywords = $xpath->query('//meta[@itemprop="keywords"]/@content');
                        if ($schemaKeywords->length > 0) {
                            $keywords = $schemaKeywords->item(0)->textContent;
                            $tags = array_merge($tags, array_map('trim', explode(',', $keywords)));
                        }

                        // 2. Meta keywords
                        $metaKeywords = $xpath->query('//meta[@name="keywords"]/@content');
                        if ($metaKeywords->length > 0) {
                            $keywords = $metaKeywords->item(0)->textContent;
                            $tags = array_merge($tags, array_map('trim', explode(',', $keywords)));
                        }

                        // 3. Open Graph article:tag
                        $ogTags = $xpath->query('//meta[@property="article:tag"]/@content');
                        foreach ($ogTags as $tag) {
                            $tags[] = trim($tag->textContent);
                        }

                        // 4. Open Graph article:section
                        $ogSection = $xpath->query('//meta[@property="article:section"]/@content');
                        if ($ogSection->length > 0) {
                            $tags[] = trim($ogSection->item(0)->textContent);
                        }

                        // 5. Meta category
                        $metaCategory = $xpath->query('//meta[@name="category"]/@content');
                        if ($metaCategory->length > 0) {
                            $tags[] = trim($metaCategory->item(0)->textContent);
                        }

                        // 6. News keywords
                        $newsKeywords = $xpath->query('//meta[@name="news_keywords"]/@content');
                        if ($newsKeywords->length > 0) {
                            $keywords = $newsKeywords->item(0)->textContent;
                            $tags = array_merge($tags, array_map('trim', explode(',', $keywords)));
                        }

                        // 7. Microformats: p-category
                        $mfCategories = $xpath->query('//*[contains(@class, "p-category")]');
                        foreach ($mfCategories as $cat) {
                            $catText = trim($cat->textContent);
                            if (!empty($catText) && strlen($catText) < 50) { // Reasonable tag length
                                $tags[] = $catText;
                            }
                        }

                        // 8. Microformats2: p-category with rel-tag
                        $mfRelTags = $xpath->query('//a[contains(@class, "p-category") and @rel="tag"]');
                        foreach ($mfRelTags as $tag) {
                            $tags[] = trim($tag->textContent);
                        }

                        // 9. Schema.org about (for broader topics)
                        $schemaAbout = $xpath->query('//*[@itemprop="about"]/@content');
                        foreach ($schemaAbout as $about) {
                            $aboutText = trim($about->textContent);
                            if (!empty($aboutText) && strlen($aboutText) < 50) {
                                $tags[] = $aboutText;
                            }
                        }

                        // 10. WordPress/CMS category links (common pattern)
                        $wpCategories = $xpath->query('//a[contains(@rel, "category") or contains(@rel, "tag")]');
                        foreach ($wpCategories as $cat) {
                            $catText = trim($cat->textContent);
                            if (!empty($catText) && strlen($catText) < 50 && strlen($catText) > 2) {
                                $tags[] = $catText;
                            }
                        }

                        // 11. JSON-LD structured data
                        $jsonLdScripts = $xpath->query('//script[@type="application/ld+json"]');
                        foreach ($jsonLdScripts as $script) {
                            $jsonData = @json_decode($script->textContent, true);
                            if ($jsonData) {
                                // Handle @graph structure
                                $items = isset($jsonData['@graph']) ? $jsonData['@graph'] : [$jsonData];

                                foreach ($items as $item) {
                                    // Extract keywords from JSON-LD
                                    if (isset($item['keywords'])) {
                                        if (is_array($item['keywords'])) {
                                            $tags = array_merge($tags, $item['keywords']);
                                        } else {
                                            $tags = array_merge($tags, array_map('trim', explode(',', $item['keywords'])));
                                        }
                                    }

                                    // Extract articleSection
                                    if (isset($item['articleSection'])) {
                                        if (is_array($item['articleSection'])) {
                                            $tags = array_merge($tags, $item['articleSection']);
                                        } else {
                                            $tags[] = $item['articleSection'];
                                        }
                                    }

                                    // Extract genre
                                    if (isset($item['genre'])) {
                                        if (is_array($item['genre'])) {
                                            $tags = array_merge($tags, $item['genre']);
                                        } else {
                                            $tags[] = $item['genre'];
                                        }
                                    }

                                    // Extract about
                                    if (isset($item['about'])) {
                                        if (is_array($item['about'])) {
                                            foreach ($item['about'] as $about) {
                                                if (isset($about['name'])) {
                                                    $tags[] = $about['name'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // Clean and deduplicate tags
                        $tags = array_map(function($tag) {
                            // Remove HTML tags
                            $tag = strip_tags($tag);
                            // Normalize whitespace
                            $tag = preg_replace('/\s+/', ' ', $tag);
                            // Trim
                            $tag = trim($tag);
                            // Convert to lowercase for comparison
                            return $tag;
                        }, $tags);

                        // Remove empty, too short, too long, or duplicate tags
                        $tags = array_filter($tags, function($tag) {
                            $len = strlen($tag);
                            return $len >= 2 && $len <= 50;
                        });

                        // Remove duplicates (case-insensitive)
                        $tags = array_unique(array_map('strtolower', $tags));

                        // Limit to reasonable number
                        $tags = array_slice($tags, 0, 20);

                        if (!empty($tags)) {
                            $metadata['tags'] = implode(', ', $tags);
                        }

                        // =====================================================
                        // EXTRACT TITLE
                        // =====================================================

                        // Prefer Open Graph title
                        $ogTitle = $xpath->query('//meta[@property="og:title"]/@content');
                        if ($ogTitle->length > 0) {
                            $metadata['title'] = trim($ogTitle->item(0)->textContent);
                        }

                        // Fallback to Twitter title
                        if (!$metadata['title']) {
                            $twitterTitle = $xpath->query('//meta[@name="twitter:title"]/@content');
                            if ($twitterTitle->length > 0) {
                                $metadata['title'] = trim($twitterTitle->item(0)->textContent);
                            }
                        }

                        // Fallback to <title> tag
                        if (!$metadata['title']) {
                            $titleNodes = $xpath->query('//title');
                            if ($titleNodes->length > 0) {
                                $metadata['title'] = trim($titleNodes->item(0)->textContent);
                            }
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
