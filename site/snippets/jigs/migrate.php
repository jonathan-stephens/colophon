<?php

// Specific parent pages only - using exact paths
$migrations = [
    'articles' => ['old' => 'articles-container.txt', 'new' => 'default-container.txt'],
    'journal' => ['old' => 'journal-container.txt', 'new' => 'default-container.txt'],
    'essays' => ['old' => 'essays-container.txt', 'new' => 'default-container.txt'],
    'notes' => ['old' => 'notes-container.txt', 'new' => 'default-container.txt'],
];

echo "<h2>Renaming parent container files only</h2>";

foreach ($migrations as $slug => $files) {
    $page = page($slug);

    if (!$page) {
        echo "✗ Page '{$slug}' not found<br>";
        continue;
    }

    $oldFile = $page->root() . '/' . $files['old'];
    $newFile = $page->root() . '/' . $files['new'];

    echo "Checking: {$slug}<br>";
    echo "Path: {$oldFile}<br>";

    if (file_exists($oldFile)) {
        if (rename($oldFile, $newFile)) {
            echo "✓ SUCCESS: Renamed to {$files['new']}<br><br>";
        } else {
            echo "✗ FAILED: Could not rename<br><br>";
        }
    } else {
        echo "- File doesn't exist (already renamed?)<br><br>";
    }
}

echo "<strong>Done!</strong>";
