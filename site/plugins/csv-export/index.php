<?php

// Load Composer dependencies (league/html-to-markdown)
if(file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// ─────────────────────────────────────────────────────────────────────────────
// Convert a Buttondown HTML email body to KirbyText / Markdown.
//
// Handles the specifics of Buttondown's export format:
//   - <figure>/<figcaption> → Markdown image with caption as alt text
//   - class="pullquote" blockquotes → standard blockquote
//   - Empty <p> tags left after template variable stripping
//   - Inline styles stripped before conversion
//   - Tables preserved via TableConverter
// ─────────────────────────────────────────────────────────────────────────────
function buttondownHtmlToMarkdown(string $html): string {

    // ── Pre-processing ─────────────────────────────────────────────────────

    // 1. Strip inline style attributes — they don't translate to Markdown
    $html = preg_replace('/\s*style="[^"]*"/i', '', $html);

    // 2. Convert <figure><img ...><figcaption>Caption</figcaption></figure>
    //    to a clean <img alt="Caption" src="..."> so the converter produces
    //    ![Caption](src), optionally followed by an italic caption line.
    $html = preg_replace_callback(
        '/<figure[^>]*>\s*<img([^>]*)>\s*(?:<figcaption[^>]*>(.*?)<\/figcaption>)?\s*<\/figure>/is',
        function($matches) {
            $attrs   = $matches[1];
            $caption = isset($matches[2]) ? trim(strip_tags($matches[2])) : '';

            preg_match('/src=["\']([^"\']+)["\']/', $attrs, $srcMatch);
            $src = $srcMatch[1] ?? '';
            if(empty($src)) return '';

            $altAttr    = $caption ? ' alt="' . htmlspecialchars($caption, ENT_QUOTES) . '"' : '';
            $captionLine = $caption ? "\n*{$caption}*" : '';

            return "<img src=\"{$src}\"{$altAttr}>{$captionLine}";
        },
        $html
    );

    // 3. Unwrap pullquote blockquotes so they become standard Markdown >
    $html = preg_replace('/<blockquote[^>]*class="[^"]*pullquote[^"]*"[^>]*>/i', '<blockquote>', $html);

    // 4. Remove empty <p> tags left over after template variable stripping
    $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html);

    // 5. Normalise <hr> tags to Markdown thematic breaks
    $html = preg_replace('/<hr\s*\/?>/i', "\n\n---\n\n", $html);

    // ── Convert ────────────────────────────────────────────────────────────

    if(!class_exists('League\HTMLToMarkdown\HtmlConverter')) {
        // Vendor not installed yet — return plain stripped text as a safe fallback
        return trim(strip_tags($html));
    }

    $converter = new League\HTMLToMarkdown\HtmlConverter([
        'strip_tags'        => false,
        'remove_nodes'      => 'head script style noscript',
        'hard_break'        => true,
        'header_style'      => 'atx',
        'bold_style'        => '**',
        'italic_style'      => '*',
        'list_item_style'   => '-',
        'preserve_comments' => false,
    ]);

    // Enable table support (disabled by default in the library)
    $environment = $converter->getEnvironment();
    $environment->addConverter(new League\HTMLToMarkdown\Converter\TableConverter());

    $markdown = $converter->convert($html);

    // ── Post-processing ────────────────────────────────────────────────────

    // Collapse 3+ consecutive blank lines to 2
    $markdown = preg_replace('/\n{3,}/', "\n\n", $markdown);

    // Strip any residual unknown HTML tags the converter left untouched
    $markdown = preg_replace('/<[^>]+>/', '', $markdown);

    return trim($markdown);
}

Kirby::plugin('jonathan-stephens/csv-export', [
    'pageMethods' => [
        'toCSV' => function() {
            $csv = [];
            $allItems = [];
            $maxTags = 0;

            // First pass: collect all items and find max tag count
            foreach($this->children() as $item) {
                $tags = $item->tags()->isNotEmpty()
                    ? $item->tags()->split(',')
                    : [];

                $maxTags = max($maxTags, count($tags));

                $allItems[] = [
                    'item' => $item,
                    'tags' => $tags
                ];
            }

            // Build headers dynamically - UUID first for matching
            $headers = ['UUID', 'Title', 'URL', 'Date', 'Website', 'TLD', 'Author', 'Author URL', 'Text'];
            for($i = 1; $i <= $maxTags; $i++) {
                $headers[] = 'Tag ' . $i;
            }
            $csv[] = $headers;

            // Second pass: build rows
            foreach($allItems as $data) {
                $item = $data['item'];
                $tags = $data['tags'];

                $row = [
                    $item->uuid()->toString(),
                    $item->title()->value(),
                    $item->url(),
                    $item->date()->toDate('Y-m-d'),
                    $item->website()->value(),
                    $item->tld()->value(),
                    $item->author()->value(),
                    $item->authorURL()->value(),
                    $item->text()->value(),
                ];

                for($i = 0; $i < $maxTags; $i++) {
                    $row[] = $tags[$i] ?? '';
                }

                $csv[] = $row;
            }

            return $csv;
        }
    ],
    'routes' => [

        // ─────────────────────────────────────────────
        // LINKS: Export
        // ─────────────────────────────────────────────
        [
            'pattern' => 'csv-export/links',
            'method'  => 'GET',
            'action'  => function() {
                if(!kirby()->user()) {
                    return false;
                }

                $page = page('links');
                if(!$page) {
                    return false;
                }

                $csv      = $page->toCSV();
                $filename = Str::slug($page->title()) . '-export-' . date('Y-m-d') . '.csv';

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                $output = fopen('php://output', 'w');
                foreach($csv as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit();
            }
        ],

        // ─────────────────────────────────────────────
        // LINKS: Import
        // ─────────────────────────────────────────────
        [
            'pattern' => 'csv-import/links',
            'method'  => 'GET',
            'action'  => function() {
                set_time_limit(120);

                if(!kirby()->user()) {
                    die('Not authenticated');
                }

                $page = page('links');
                if(!$page) {
                    die('Page not found');
                }

                $batch     = (int) get('batch', 0);
                $batchSize = 100;

                $csvFile = $page->files()->filterBy('extension', 'csv')->first();
                if(!$csvFile) {
                    die('No CSV file found. Please upload a CSV file first.');
                }

                $allData = [];
                $handle  = fopen($csvFile->root(), 'r');
                $headers = fgetcsv($handle);

                while (($row = fgetcsv($handle)) !== false) {
                    $allData[] = array_combine($headers, $row);
                }
                fclose($handle);

                $totalRows    = count($allData);
                $totalBatches = max(1, ceil($totalRows / $batchSize));
                $start        = $batch * $batchSize;
                $csvData      = array_slice($allData, $start, $batchSize);

                $updated = 0;
                $errors  = [];

                foreach($csvData as $index => $rowData) {
                    $actualRow = $start + $index + 2;

                    try {
                        $uuid = isset($rowData['UUID']) ? trim($rowData['UUID']) : '';
                        if(empty($uuid)) {
                            $errors[] = "Row $actualRow: Missing UUID";
                            continue;
                        }

                        $linkPage = kirby()->page($uuid);
                        if(!$linkPage) {
                            $errors[] = "Row $actualRow: Page not found";
                            continue;
                        }

                        $tags = [];
                        foreach($rowData as $key => $value) {
                            if(strpos($key, 'Tag ') === 0 && !empty(trim($value))) {
                                $tags[] = trim($value);
                            }
                        }

                        $updateData = [];
                        if(isset($rowData['Title']))       $updateData['title']     = $rowData['Title'];
                        if(isset($rowData['Date']))        $updateData['date']      = $rowData['Date'];
                        if(isset($rowData['Website']))     $updateData['website']   = $rowData['Website'];
                        if(isset($rowData['TLD']))         $updateData['tld']       = $rowData['TLD'];
                        if(isset($rowData['Author']))      $updateData['author']    = $rowData['Author'];
                        if(isset($rowData['Author URL']))  $updateData['authorurl'] = $rowData['Author URL'];
                        if(isset($rowData['Text']))        $updateData['text']      = $rowData['Text'];
                        if(count($tags) > 0)               $updateData['tags']      = implode(', ', $tags);

                        if(count($updateData) > 0) {
                            $linkPage->update($updateData);
                            $updated++;
                        }

                    } catch(Exception $e) {
                        $errors[] = "Row $actualRow: " . $e->getMessage();
                    }
                }

                $nextBatch = $batch + 1;
                $isDone    = $nextBatch >= $totalBatches;
                $progress  = min(100, round(($nextBatch / $totalBatches) * 100));

                if($isDone) {
                    try { $csvFile->delete(); } catch(Exception $e) {}
                }

                return importProgressHtml(
                    isDone:          $isDone,
                    batch:           $batch,
                    totalBatches:    $totalBatches,
                    progress:        $progress,
                    start:           $start,
                    batchSize:       $batchSize,
                    totalRows:       $totalRows,
                    summaryLine:     "Updated: $updated",
                    errors:          $errors,
                    nextBatchUrl:    '/csv-import/links?batch=' . $nextBatch,
                    doneRedirectUrl: '/panel/pages/links'
                );
            }
        ],

        // ─────────────────────────────────────────────
        // NEWSLETTER: Import
        // ─────────────────────────────────────────────
        [
            'pattern' => 'csv-import/newsletter',
            'method'  => 'GET',
            'action'  => function() {
                set_time_limit(120);

                if(!kirby()->user()) {
                    die('Not authenticated');
                }

                $newsletterPage = page('newsletter');
                if(!$newsletterPage) {
                    die(
                        'Newsletter parent page not found. ' .
                        'Create a page with the slug "newsletter" using the newsletter-archive template first.'
                    );
                }

                // ── Find the uploaded CSV ──────────────────────────
                $csvFile = $newsletterPage->files()->filterBy('extension', 'csv')->first();
                if(!$csvFile) {
                    die(
                        'No CSV file found. ' .
                        'Upload emails.csv to the Newsletter archive page files, then try again.'
                    );
                }

                // ── Parse the full CSV, skipping drafts ────────────
                $allData = [];
                $handle  = fopen($csvFile->root(), 'r');
                $headers = fgetcsv($handle);

                while (($row = fgetcsv($handle)) !== false) {
                    $parsed = array_combine($headers, $row);

                    // Skip rows with no secondary_id — drafts or incomplete entries
                    if(empty(trim($parsed['secondary_id'] ?? ''))) {
                        continue;
                    }

                    $allData[] = $parsed;
                }
                fclose($handle);

                // ── Batch setup ────────────────────────────────────
                $batch        = (int) get('batch', 0);
                $batchSize    = 25; // Smaller than links — page creation is heavier
                $totalRows    = count($allData);
                $totalBatches = max(1, ceil($totalRows / $batchSize));
                $start        = $batch * $batchSize;
                $csvData      = array_slice($allData, $start, $batchSize);

                $created = 0;
                $skipped = 0;
                $errors  = [];

                foreach($csvData as $index => $rowData) {
                    $actualRow = $start + $index + 2;

                    try {
                        $slug = trim($rowData['slug'] ?? '');
                        if(empty($slug)) {
                            $errors[] = "Row $actualRow: Missing slug — skipped.";
                            continue;
                        }

                        // Skip pages that already exist in any state — listed, unlisted, or draft.
                        // childrenAndDrafts() is required; children() alone misses drafts.
                        if($newsletterPage->childrenAndDrafts()->find($slug)) {
                            $skipped++;
                            continue;
                        }

                        // ── Find the matching .md body file by slug ─
                        // All .md files from the Buttondown export are uploaded
                        // flat to the newsletter page alongside the CSV.
                        // Buttondown names each file by its slug.
                        $mdFile = $newsletterPage->files()
                            ->filterBy('extension', 'md')
                            ->filterBy('name', $slug)
                            ->first();

                        $bodyHtml = '';
                        if($mdFile) {
                            $raw = file_get_contents($mdFile->root());

                            // Strip the Buttondown editor-mode HTML comment header
                            $bodyHtml = preg_replace(
                                '/<!--\s*buttondown-editor-mode:[^-]*-->\s*/i',
                                '',
                                $raw
                            );

                            // Remove Buttondown template variables, e.g. {{ subscribe_form }}
                            // These are block-level tags that sometimes sit inside <p> tags,
                            // so we strip the surrounding <p> too when it contains only a variable.
                            $bodyHtml = preg_replace(
                                '/<p[^>]*>\s*\{\{[^}]*\}\}\s*<\/p>/i',
                                '',
                                $bodyHtml
                            );
                            // Catch any remaining bare {{ ... }} not wrapped in a tag
                            $bodyHtml = preg_replace('/\{\{[^}]*\}\}/', '', $bodyHtml);

                            // Convert HTML to Markdown / KirbyText
                            $bodyHtml = buttondownHtmlToMarkdown($bodyHtml);
                        } else {
                            $errors[] = "Row $actualRow ($slug): No matching .md file — page created with empty body.";
                        }

                        // ── Field values ───────────────────────────
                        $subject        = trim($rowData['subject']                   ?? '');
                        $publishDate    = trim($rowData['publish_date']              ?? '');
                        $buttondownId   = trim($rowData['id']                        ?? '');
                        $substackId     = trim($rowData['metadata.substack_post_id'] ?? '');
                        $newsletterName = trim($rowData['Newsletter']                ?? '');

                        // Normalise to Kirby datetime format (Y-m-d H:i)
                        $datetimeValue = '';
                        if(!empty($publishDate)) {
                            $ts = strtotime($publishDate);
                            if($ts !== false) {
                                $datetimeValue = date('Y-m-d H:i', $ts);
                            }
                        }

                        // Normalise newsletter name to blueprint select key
                        $newsletterKey = match(true) {
                            in_array(strtolower($newsletterName), ['500 words', '500words', '500-words'])
                                => '500-words',
                            in_array(strtolower($newsletterName), ['500 characters', '500characters', '500-characters'])
                                => '500-characters',
                            in_array(strtolower($newsletterName), ['craft & practice', 'craft and practice', 'craft-and-practice'])
                                => 'craft-and-practice',
                            in_array(strtolower($newsletterName), ['weekly wanders', 'weekly-wanders'])
                                => 'weekly-wanders',
                            default => ''
                        };

                        if(empty($newsletterKey)) {
                            $errors[] = 'Row ' . $actualRow . ' (' . $slug . '): Newsletter value [' . $newsletterName . '] did not match — field left blank.';
                        }

                        // Canonical Buttondown archive URL
                        $buttondownUrl = 'https://buttondown.com/jonathanstephens/archive/' . $slug;

                        // Substack URL: ID stored for traceability.
                        // Full URL not constructed here — subdomain varies by account
                        // and is better filled in manually or via a future lookup step.
                        $substackUrl = '';

                        // ── Create the Kirby child page ────────────
                        kirby()->impersonate('kirby', function() use (
                            $newsletterPage, $slug, $subject, $bodyHtml,
                            $datetimeValue, $buttondownId, $buttondownUrl,
                            $substackId, $substackUrl, $newsletterKey
                        ) {
                            $newPage = $newsletterPage->createChild([
                                'slug'     => $slug,
                                'template' => 'newsletter',
                                'content'  => [
                                    'title'           => $subject,
                                    'hed'             => $subject,
                                    'dek'             => '',
                                    'text'            => $bodyHtml,
                                    'datetime'        => $datetimeValue,
                                    'growthstatus'    => 'evergreen',
                                    'tags'            => 'newsletter',
                                    'newsletter'      => $newsletterKey,
                                    'buttondown_id'   => $buttondownId,
                                    'buttondown_slug' => $slug,
                                    'buttondown_url'  => $buttondownUrl,
                                    'substack_id'     => $substackId,
                                    'substack_url'    => $substackUrl,
                                ],
                            ]);

                            // Publish the page without an explicit sort number — passing one
                            // causes Kirby to renumber all siblings on each call, which throws
                            // during bulk imports. Kirby auto-assigns incrementing numbers in
                            // creation order; sort by datetime on the frontend instead.
                            // If changeStatus fails, delete the draft immediately so no dangling
                            // pages are left and the import can always be re-run cleanly.
                            try {
                                $newPage->changeStatus('listed');
                            } catch(Throwable $statusErr) {
                                try { $newPage->delete(true); } catch(Throwable $del) {}
                                throw new Exception('changeStatus failed (' . $statusErr->getMessage() . ')');
                            }
                        });

                        $created++;

                    } catch(Exception $e) {
                        $slug = $rowData['slug'] ?? 'unknown';
                        $errors[] = "Row $actualRow ($slug): " . $e->getMessage();
                    }
                }

                // ── Cleanup on final batch ─────────────────────────
                $nextBatch = $batch + 1;
                $isDone    = $nextBatch >= $totalBatches;
                $progress  = min(100, round(($nextBatch / $totalBatches) * 100));

                if($isDone) {
                    try {
                        $csvFile->delete();
                        foreach($newsletterPage->files()->filterBy('extension', 'md') as $f) {
                            $f->delete();
                        }
                    } catch(Exception $e) {
                        // Non-fatal — files can be removed from the panel manually
                    }
                }

                $summaryLine = "Created: $created · Skipped (already exist): $skipped";

                return importProgressHtml(
                    isDone:          $isDone,
                    batch:           $batch,
                    totalBatches:    $totalBatches,
                    progress:        $progress,
                    start:           $start,
                    batchSize:       $batchSize,
                    totalRows:       $totalRows,
                    summaryLine:     $summaryLine,
                    errors:          $errors,
                    nextBatchUrl:    '/csv-import/newsletter?batch=' . $nextBatch,
                    doneRedirectUrl: '/panel/pages/newsletter'
                );
            }
        ],

    ] // end routes
]);


// ─────────────────────────────────────────────────────────────────────────────
// Shared helper: renders the batched import progress / completion page.
// Used by both the links and newsletter import routes.
// ─────────────────────────────────────────────────────────────────────────────
function importProgressHtml(
    bool   $isDone,
    int    $batch,
    int    $totalBatches,
    int    $progress,
    int    $start,
    int    $batchSize,
    int    $totalRows,
    string $summaryLine,
    array  $errors,
    string $nextBatchUrl,
    string $doneRedirectUrl
): string {
    $html  = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">';
    $html .= '<meta name="viewport" content="width=device-width, initial-scale=1">';

    if(!$isDone) {
        $html .= '<meta http-equiv="refresh" content="2;url=' . htmlspecialchars($nextBatchUrl) . '">';
    }

    $html .= '<style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 640px; margin: 60px auto; padding: 0 20px;
            text-align: center; color: #16171a; background: #fff;
        }
        h2 { font-size: 1.25rem; margin-bottom: 0.5rem; }
        p  { color: #555; font-size: 0.9rem; margin: 0.4rem 0; }
        .progress-bar  { width: 100%; height: 24px; background: #f0f0f0; border-radius: 12px; overflow: hidden; margin: 20px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #16ba00, #0d8000); transition: width 0.3s ease; }
        .stats { background: #f7f7f7; padding: 16px 20px; border-radius: 8px; margin: 20px 0; font-size: 0.9rem; }
        .errors-wrap { text-align: left; margin-top: 24px; }
        .errors-wrap h3 { font-size: 1rem; margin-bottom: 8px; }
        ul.errors {
            max-height: 280px; overflow-y: auto;
            background: #fff8f8; border: 1px solid #f5c2c2; border-radius: 8px;
            padding: 12px 16px 12px 28px; margin: 0;
        }
        ul.errors li { font-size: 0.8rem; color: #a00; margin-bottom: 4px; }
        a.button {
            display: inline-block; background: #16171a; color: #fff;
            padding: 10px 22px; text-decoration: none; border-radius: 6px;
            margin-top: 24px; font-size: 0.9rem;
        }
        a.button:hover { background: #333; }
    </style></head><body>';

    if(!$isDone) {
        $end   = min($start + $batchSize, $totalRows);
        $html .= '<h2>Processing batch ' . ($batch + 1) . ' of ' . $totalBatches . '</h2>';
        $html .= '<div class="progress-bar"><div class="progress-fill" style="width:' . $progress . '%"></div></div>';
        $html .= '<p>' . $progress . '% complete — rows ' . ($start + 1) . '–' . $end . ' of ' . $totalRows . '</p>';
        $html .= '<p>' . htmlspecialchars($summaryLine) . ' this batch…</p>';
        $html .= '<p><em>Continuing automatically in 2 seconds…</em></p>';
    } else {
        $html .= '<h2>✓ Import complete</h2>';
        $html .= '<div class="stats">';
        $html .= '<p><strong>All ' . $totalRows . ' rows processed</strong></p>';
        $html .= '<p>' . htmlspecialchars($summaryLine) . '</p>';
        $html .= '</div>';

        if(count($errors) > 0) {
            $html .= '<div class="errors-wrap">';
            $html .= '<h3>Warnings &amp; errors (' . count($errors) . ')</h3>';
            $html .= '<ul class="errors">';
            foreach($errors as $error) {
                $html .= '<li>' . htmlspecialchars($error) . '</li>';
            }
            $html .= '</ul></div>';
        }

        $html .= '<a href="' . htmlspecialchars($doneRedirectUrl) . '" class="button">← Back to panel</a>';
    }

    $html .= '</body></html>';
    return $html;
}
