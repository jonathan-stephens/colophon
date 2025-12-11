<?php
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
                    $item->uuid()->toString(), // Add UUID as first column
                    $item->title()->value(),
                    $item->url(),
                    $item->date()->toDate('Y-m-d'),
                    $item->website()->value(),
                    $item->tld()->value(),
                    $item->author()->value(),
                    $item->authorURL()->value(),
                    $item->text()->value(),
                ];

                // Add tag columns
                for($i = 0; $i < $maxTags; $i++) {
                    $row[] = $tags[$i] ?? '';
                }

                $csv[] = $row;
            }

            return $csv;
        }
    ],
    'routes' => [
        [
            'pattern' => 'csv-export/links',
            'method' => 'GET',
            'action' => function() {
                if(!kirby()->user()) {
                    return false;
                }
                $page = page('links');
                if(!$page) {
                    return false;
                }
                $csv = $page->toCSV();
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
        [
            'pattern' => 'csv-import/links',
            'method' => 'GET',
            'action' => function() {
                set_time_limit(120); // 2 minutes per batch

                if(!kirby()->user()) {
                    die('Not authenticated');
                }

                $page = page('links');
                if(!$page) {
                    die('Page not found');
                }

                // Get batch parameters
                $batch = get('batch', 0);
                $batchSize = 100; // Process 100 at a time

                // Find the uploaded CSV file
                $csvFile = $page->files()->filterBy('extension', 'csv')->first();
                if(!$csvFile) {
                    die('No CSV file found. Please upload a CSV file first.');
                }

                // Parse CSV
                $allData = [];
                $handle = fopen($csvFile->root(), 'r');
                $headers = fgetcsv($handle);

                while (($row = fgetcsv($handle)) !== false) {
                    $allData[] = array_combine($headers, $row);
                }
                fclose($handle);

                $totalRows = count($allData);
                $totalBatches = ceil($totalRows / $batchSize);

                // Get current batch data
                $start = $batch * $batchSize;
                $csvData = array_slice($allData, $start, $batchSize);

                $updated = 0;
                $errors = [];

                // Process current batch
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

                        // Collect tags
                        $tags = [];
                        foreach($rowData as $key => $value) {
                            if(strpos($key, 'Tag ') === 0 && !empty(trim($value))) {
                                $tags[] = trim($value);
                            }
                        }

                        // Prepare update data
                        $updateData = [];
                        if(isset($rowData['Title'])) $updateData['title'] = $rowData['Title'];
                        if(isset($rowData['Date'])) $updateData['date'] = $rowData['Date'];
                        if(isset($rowData['Website'])) $updateData['website'] = $rowData['Website'];
                        if(isset($rowData['TLD'])) $updateData['tld'] = $rowData['TLD'];
                        if(isset($rowData['Author'])) $updateData['author'] = $rowData['Author'];
                        if(isset($rowData['Author URL'])) $updateData['authorurl'] = $rowData['Author URL'];
                        if(isset($rowData['Text'])) $updateData['text'] = $rowData['Text'];
                        if(count($tags) > 0) $updateData['tags'] = implode(', ', $tags);

                        if(count($updateData) > 0) {
                            $linkPage->update($updateData);
                            $updated++;
                        }

                    } catch(Exception $e) {
                        $title = isset($rowData['Title']) ? $rowData['Title'] : 'Unknown';
                        $errors[] = "Row $actualRow: " . $e->getMessage();
                    }
                }

                // Check if we're done
                $nextBatch = $batch + 1;
                $isDone = $nextBatch >= $totalBatches;

                // Build result page
                $progress = min(100, round(($nextBatch / $totalBatches) * 100));

                $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';

                if(!$isDone) {
                    // Auto-redirect to next batch
                    $html .= '<meta http-equiv="refresh" content="2;url=/csv-import/links?batch=' . $nextBatch . '">';
                }

                $html .= '<style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; text-align: center; }
                    .progress-bar { width: 100%; height: 30px; background: #f0f0f0; border-radius: 15px; overflow: hidden; margin: 20px 0; }
                    .progress-fill { height: 100%; background: linear-gradient(90deg, #16ba00, #0d8000); transition: width 0.3s; }
                    .stats { background: #f7f7f7; padding: 20px; border-radius: 8px; margin: 20px 0; }
                    a.button { display: inline-block; background: #16171a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 20px; }
                </style></head><body>';

                if(!$isDone) {
                    $html .= "<h2>Processing Batch " . ($batch + 1) . " of $totalBatches</h2>";
                    $html .= '<div class="progress-bar"><div class="progress-fill" style="width: ' . $progress . '%"></div></div>';
                    $html .= "<p>$progress% complete - Processing rows " . ($start + 1) . " to " . min($start + $batchSize, $totalRows) . " of $totalRows</p>";
                    $html .= "<p>Updated $updated in this batch...</p>";
                    $html .= "<p><em>This page will automatically continue to the next batch...</em></p>";
                } else {
                    // Delete CSV after completion
                    try {
                        $csvFile->delete();
                    } catch(Exception $e) {}

                    $html .= "<h2>✓ Import Complete!</h2>";
                    $html .= '<div class="stats">';
                    $html .= "<p><strong>All $totalRows rows processed</strong></p>";
                    $html .= '</div>';

                    if(count($errors) > 0) {
                        $html .= "<h3>Errors (" . count($errors) . "):</h3>";
                        $html .= "<ul style='text-align: left; max-height: 300px; overflow-y: auto;'>";
                        foreach($errors as $error) {
                            $html .= "<li>" . htmlspecialchars($error) . "</li>";
                        }
                        $html .= "</ul>";
                    }

                    $html .= '<a href="/panel/pages/links" class="button">← Back to Links</a>';
                }

                $html .= '</body></html>';

                return $html;
            }
        ]
      ]
]);
