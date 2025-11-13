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

            // Build headers dynamically
            $headers = ['Title', 'URL', 'Date', 'Website', 'TLD', 'Author', 'Author URL', 'Text'];
            for($i = 1; $i <= $maxTags; $i++) {
                $headers[] = 'Tag ' . $i;
            }
            $csv[] = $headers;

            // Second pass: build rows
            foreach($allItems as $data) {
                $item = $data['item'];
                $tags = $data['tags'];

                $row = [
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
        ]
    ]
]);
