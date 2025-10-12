<?php

/**
 * Kirby Bookmarks API Plugin - With Improved Session Auth Support
 */

Kirby::plugin('jonathan-stephens/bookmarks-api', [
    'api' => [
        'routes' => [
            // Get all tags used across the site
            [
                'pattern' => 'bookmarks/tags',
                'method' => 'GET',
                'auth' => false,
                'action' => function () {
                    try {
                        $kirby = kirby();
                        $linksPage = page('links');
                        
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links page not found'
                            ];
                        }

                        $allTags = [];
                        
                        // Collect all tags from all bookmarks
                        foreach ($linksPage->children()->listed() as $link) {
                            if ($link->tags()->isNotEmpty()) {
                                $tags = $link->tags()->split(',');
                                foreach ($tags as $tag) {
                                    $tag = trim($tag);
                                    if (!empty($tag)) {
                                        $allTags[] = $tag;
                                    }
                                }
                            }
                        }

                        // Remove duplicates and sort
                        $allTags = array_unique($allTags);
                        sort($allTags);

                        return [
                            'status' => 'success',
                            'data' => array_values($allTags)
                        ];

                    } catch (Exception $e) {
                        return [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                    }
                }
            ],

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
                            $authorText = trim($node->textContent);
                            // Skip if it looks like a Twitter handle
                            if (!empty($authorText) && $authorText[0] !== '@') {
                                $authors[] = $authorText;
                            }
                        }

                        // Direct itemprop="author"
                        if (empty($authors)) {
                            $authorNodes = $xpath->query('//*[@itemprop="author"]');
                            foreach ($authorNodes as $authorNode) {
                                $nameNode = $xpath->query('.//*[@itemprop="name"]', $authorNode);
                                if ($nameNode->length > 0) {
                                    $authorText = trim($nameNode->item(0)->textContent);
                                    if (!empty($authorText) && $authorText[0] !== '@') {
                                        $authors[] = $authorText;
                                    }
                                } else {
                                    $text = trim($authorNode->textContent);
                                    // Reasonable author name length and not a Twitter handle
                                    if (!empty($text) && strlen($text) < 100 && $text[0] !== '@') {
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

                                // Skip if it's a Twitter handle
                                if ($authorText[0] !== '@') {
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
                        }

                        // Open Graph article:author (but not Twitter handles)
                        if (empty($authors)) {
                            $ogAuthor = $xpath->query('//meta[@property="article:author"]/@content');
                            foreach ($ogAuthor as $node) {
                                $authorText = trim($node->textContent);
                                // Skip URLs and Twitter handles
                                if (strpos($authorText, 'http') === false && $authorText[0] !== '@') {
                                    $authors[] = $authorText;
                                }
                            }
                        }

                        // Microformats: p-author or h-card (but not Twitter handles)
                        if (empty($authors)) {
                            $mfAuthor = $xpath->query('//*[contains(@class, "p-author")]');
                            foreach ($mfAuthor as $node) {
                                $authorText = trim($node->textContent);
                                if (!empty($authorText) && $authorText[0] !== '@') {
                                    $authors[] = $authorText;
                                }
                            }
                        }

                        // Clean up authors - remove Twitter handles and empty values
                        $authors = array_filter($authors, function($author) {
                            return !empty($author) && $author[0] !== '@';
                        });

                        $authors = array_unique($authors);

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

            // Add full bookmark - IMPROVED SESSION AUTH
            [
                'pattern' => 'bookmarks/add',
                'method' => 'POST',
                'auth' => false,
                'action' => function () {
                    try {
                        $kirby = kirby();
                        $user = null;

                        error_log('=== BOOKMARK ADD ===');

                        // Method 1: Check existing session FIRST (most common for web users)
                        $user = $kirby->user();
                        if ($user) {
                            error_log('✅ Session user found: ' . $user->email());
                        }

                        // Method 2: Try Basic Auth if no session
                        if (!$user) {
                            $authHeader = $kirby->request()->header('Authorization');
                            if ($authHeader && strpos($authHeader, 'Basic ') === 0) {
                                $credentials = base64_decode(substr($authHeader, 6));
                                list($email, $password) = explode(':', $credentials, 2);

                                try {
                                    // Try to authenticate but don't start a session
                                    $user = $kirby->auth()->login($email, $password, false);
                                    error_log('✅ Basic Auth success: ' . $email);
                                } catch (Exception $e) {
                                    error_log('❌ Basic Auth failed: ' . $e->getMessage());
                                }
                            } else {
                                error_log('⚠️ No Authorization header found');
                            }
                        }

                        if (!$user) {
                            error_log('❌ No authenticated user found');
                            return [
                                'status' => 'error',
                                'message' => 'Authentication required. Please log in to the panel first, or provide credentials.'
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
                        $path = $parsed['path'] ?? '';
                        $tld = substr($host, strrpos($host, '.') + 1);

                        // =====================================================
                        // GENERATE SLUG FROM URL
                        // =====================================================
                        $slug = '';
                        
                        // If path exists and is not just root
                        if ($path && $path !== '/') {
                            // Get the last segment of the path
                            $pathSegments = array_filter(explode('/', trim($path, '/')));
                            $lastSegment = end($pathSegments);
                            
                            // Remove file extensions if present
                            $lastSegment = preg_replace('/\.(html|htm|php|asp|aspx)$/i', '', $lastSegment);
                            
                            // Use the last path segment as slug if it's substantial
                            if (strlen($lastSegment) > 0) {
                                $slug = Str::slug($lastSegment);
                            }
                        }
                        
                        // Fallback: if no good path slug, use domain name (without TLD)
                        if (empty($slug)) {
                            // Remove www. and TLD from host
                            $domainWithoutWww = preg_replace('/^www\./', '', $host);
                            $domainParts = explode('.', $domainWithoutWww);
                            // Get everything except the TLD (last part)
                            array_pop($domainParts);
                            $domainName = implode('-', $domainParts);
                            $slug = Str::slug($domainName);
                        }
                        
                        // Ensure slug is unique by checking if it exists
                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }
                        
                        $originalSlug = $slug;
                        $counter = 1;
                        
                        // Check for existing pages with same slug
                        while ($linksPage->find($slug)) {
                            $slug = $originalSlug . '-' . $counter;
                            $counter++;
                        }

                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }

                        
                        // =====================================================
                        // CAPITALIZE TAGS
                        // =====================================================
                        $tagsInput = $data['tags'] ?? '';
                        $formattedTags = '';
                        
                        if (!empty($tagsInput)) {
                            // Split by comma
                            $tagArray = array_map('trim', explode(',', $tagsInput));
                            
                            // Capitalize first letter of each word in each tag
                            $tagArray = array_map(function($tag) {
                                return ucwords(strtolower($tag));
                            }, $tagArray);
                            
                            // Join back with commas
                            $formattedTags = implode(', ', $tagArray);
                        }

                        $content = [
                            'title' => $data['title'] ?? '',
                            'website' => $url,
                            'tld' => $data['tld'] ?? $tld,
                            'text' => $data['text'] ?? '',
                            'tags' => $formattedTags,
                            'author' => $data['author'] ?? '',
                        ];

                        // Impersonate for content creation
                        $kirby->impersonate('kirby');

                        $linksPage = page('links');
                        if (!$linksPage) {
                            return [
                                'status' => 'error',
                                'message' => 'Links parent page not found'
                            ];
                        }

                        $bookmark = $linksPage->createChild([
                            'slug' => $slug,
                            'template' => 'link',
                            'content' => $content,
                            'num' => date('YmdHis')
                        ]);

                        $bookmark->changeStatus('listed');

                        error_log('✅ Bookmark created: ' . $bookmark->id());

                        return [
                            'status' => 'success',
                            'message' => 'Bookmark added successfully',
                            'data' => [
                                'id' => $bookmark->id(),
                                'url' => $bookmark->url()
                            ]
                        ];

                    } catch (Exception $e) {
                        error_log('❌ Bookmark error: ' . $e->getMessage());
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