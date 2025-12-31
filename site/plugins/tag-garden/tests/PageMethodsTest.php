<?php

/**
 * Page Methods Tests
 *
 * Tests all custom page methods added by the Tag Garden plugin.
 * Requires at least one page with content to test against.
 *
 * Usage: Create a test page template and include this file
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class PageMethodsTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;
    private $testPage = null;

    /**
     * Run all page method tests
     */
    public function runAll($page = null) {
        echo "<h2>🧪 Page Methods Tests</h2>";

        // Use provided page or try to find a suitable test page
        $this->testPage = $page ?? $this->findTestPage();

        if (!$this->testPage) {
            echo "<p style='color: #856404; background: #fff3cd; padding: 10px;'>";
            echo "⚠️ No test page found. Create a page with some text content to run these tests.";
            echo "</p>";
            return false;
        }

        echo "<p><strong>Testing with page:</strong> {$this->testPage->title()} ({$this->testPage->url()})</p>";

        $this->testMethodsExist();
        $this->testWordCount();
        $this->testCharCount();
        $this->testReadingTime();
        $this->testReadingTimeFormatted();
        $this->testContentGroup();
        $this->testRelatedTags();
        $this->testRelatedPages();

        $this->displayResults();
    }

    /**
     * Find a suitable page for testing
     */
    private function findTestPage() {
        // Try to find a page with text content
        $pages = site()->index()->filterBy('text', '!=', '');
        return $pages->first();
    }

    /**
     * Test: All methods exist on page object
     */
    private function testMethodsExist() {
        $methods = [
            'wordCount',
            'charCount',
            'readingTime',
            'readingTimeFormatted',
            'contentGroup',
            'relatedTags',
            'relatedPages',
        ];

        foreach ($methods as $method) {
            $this->assert(
                "Method '{$method}' exists",
                $this->testPage->hasMethod($method),
                "Page should have {$method}() method"
            );
        }
    }

    /**
     * Test: wordCount() returns positive integer
     */
    private function testWordCount() {
        try {
            $count = $this->testPage->wordCount();

            $this->assert(
                'wordCount() returns integer',
                is_int($count),
                "Returned: " . var_export($count, true)
            );

            $this->assert(
                'wordCount() is non-negative',
                $count >= 0,
                "Word count should be >= 0, got {$count}"
            );

            // If page has text, word count should be > 0
            if ($this->testPage->text()->isNotEmpty()) {
                $this->assert(
                    'wordCount() is positive for pages with text',
                    $count > 0,
                    "Expected > 0 words, got {$count}"
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'wordCount() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: charCount() returns positive integer
     */
    private function testCharCount() {
        try {
            $count = $this->testPage->charCount();

            $this->assert(
                'charCount() returns integer',
                is_int($count),
                "Returned: " . var_export($count, true)
            );

            $this->assert(
                'charCount() is non-negative',
                $count >= 0,
                "Character count should be >= 0, got {$count}"
            );

            // Character count should be >= word count (words + spaces)
            $wordCount = $this->testPage->wordCount();
            $this->assert(
                'charCount() >= wordCount()',
                $count >= $wordCount,
                "Chars ({$count}) should be >= words ({$wordCount})"
            );

        } catch (Exception $e) {
            $this->assert(
                'charCount() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: readingTime() returns correct structure
     */
    private function testReadingTime() {
        try {
            $time = $this->testPage->readingTime();

            $this->assert(
                'readingTime() returns array',
                is_array($time),
                "Should return array, got: " . gettype($time)
            );

            // Check required keys
            $requiredKeys = ['wordCount', 'minSeconds', 'maxSeconds', 'minMinutes', 'maxMinutes', 'avgMinutes'];
            foreach ($requiredKeys as $key) {
                $this->assert(
                    "readingTime() contains '{$key}'",
                    isset($time[$key]),
                    "Missing key: {$key}"
                );
            }

            // Validate logic: maxSeconds (fast readers) should be <= minSeconds (slow readers)
            if (isset($time['maxSeconds']) && isset($time['minSeconds'])) {
                $this->assert(
                    'maxSeconds <= minSeconds (fast readers are quicker)',
                    $time['maxSeconds'] <= $time['minSeconds'],
                    "Fast ({$time['maxSeconds']}s) should be <= Slow ({$time['minSeconds']}s)"
                );
            }

            // All values should be non-negative
            foreach (['minSeconds', 'maxSeconds', 'minMinutes', 'maxMinutes'] as $key) {
                if (isset($time[$key])) {
                    $this->assert(
                        "{$key} is non-negative",
                        $time[$key] >= 0,
                        "{$key} should be >= 0, got {$time[$key]}"
                    );
                }
            }

        } catch (Exception $e) {
            $this->assert(
                'readingTime() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: readingTimeFormatted() returns string
     */
    private function testReadingTimeFormatted() {
        try {
            $formatted = $this->testPage->readingTimeFormatted();

            $this->assert(
                'readingTimeFormatted() returns string',
                is_string($formatted),
                "Should return string, got: " . gettype($formatted)
            );

            $this->assert(
                'readingTimeFormatted() contains "read"',
                strpos($formatted, 'read') !== false,
                "Should contain 'read', got: {$formatted}"
            );

            // Should contain either "sec" or "min"
            $hasTimeUnit = strpos($formatted, 'sec') !== false || strpos($formatted, 'min') !== false;
            $this->assert(
                'readingTimeFormatted() contains time unit (sec/min)',
                $hasTimeUnit,
                "Should contain 'sec' or 'min', got: {$formatted}"
            );

        } catch (Exception $e) {
            $this->assert(
                'readingTimeFormatted() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: contentGroup() returns valid group or null
     */
    private function testContentGroup() {
        try {
            $group = $this->testPage->contentGroup();

            $this->assert(
                'contentGroup() returns string or null',
                is_string($group) || is_null($group),
                "Should return string or null, got: " . gettype($group)
            );

            // If group is returned, it should be one of the defined groups
            if ($group !== null) {
                $validGroups = array_keys(option('yourusername.tag-garden.content.groups', []));
                $this->assert(
                    'contentGroup() returns valid group name',
                    in_array($group, $validGroups),
                    "'{$group}' is not in defined groups: " . implode(', ', $validGroups)
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'contentGroup() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: relatedTags() returns array
     */
    private function testRelatedTags() {
        try {
            $tags = $this->testPage->relatedTags();

            $this->assert(
                'relatedTags() returns array',
                is_array($tags),
                "Should return array, got: " . gettype($tags)
            );

            // If page has tags, check logic
            $currentTags = $this->testPage->tags()->split(',');
            $currentTags = array_filter(array_map('trim', $currentTags));

            if (!empty($currentTags)) {
                // Related tags should not include current page's tags
                $overlap = array_intersect($tags, $currentTags);
                $this->assert(
                    'relatedTags() excludes current page tags',
                    empty($overlap),
                    "Found overlap: " . implode(', ', $overlap)
                );
            } else {
                $this->assert(
                    'relatedTags() returns empty array for untagged pages',
                    empty($tags),
                    "Expected empty array, got " . count($tags) . " tags"
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'relatedTags() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: relatedPages() returns Pages collection
     */
    private function testRelatedPages() {
        try {
            $pages = $this->testPage->relatedPages();

            $this->assert(
                'relatedPages() returns Pages collection',
                $pages instanceof \Kirby\Cms\Pages,
                "Should return Pages collection, got: " . get_class($pages)
            );

            // Should not include current page
            $this->assert(
                'relatedPages() excludes current page',
                !$pages->has($this->testPage),
                "Related pages should not include current page"
            );

            // Test with custom limit
            $limited = $this->testPage->relatedPages(3);
            $this->assert(
                'relatedPages() respects limit parameter',
                $limited->count() <= 3,
                "Expected <= 3 pages, got {$limited->count()}"
            );

        } catch (Exception $e) {
            $this->assert(
                'relatedPages() executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Assert helper
     */
    private function assert($name, $condition, $message = '') {
        $result = [
            'name' => $name,
            'passed' => $condition,
            'message' => $message
        ];

        $this->results[] = $result;

        if ($condition) {
            $this->passed++;
        } else {
            $this->failed++;
        }
    }

    /**
     * Display test results
     */
    private function displayResults() {
        echo "<div style='margin: 20px 0; padding: 15px; background: " .
             ($this->failed === 0 ? '#d4edda' : '#f8d7da') .
             "; border-radius: 5px;'>";

        echo "<h3>Results: {$this->passed} passed, {$this->failed} failed</h3>";

        echo "<ul style='list-style: none; padding: 0;'>";
        foreach ($this->results as $result) {
            $icon = $result['passed'] ? '✅' : '❌';
            $color = $result['passed'] ? '#155724' : '#721c24';

            echo "<li style='color: {$color}; padding: 5px 0;'>";
            echo "{$icon} <strong>{$result['name']}</strong>";
            if (!$result['passed'] && $result['message']) {
                echo "<br><small style='margin-left: 25px;'>{$result['message']}</small>";
            }
            echo "</li>";
        }
        echo "</ul>";

        echo "</div>";

        return $this->failed === 0;
    }
}

// Auto-run if accessed directly in a template
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    $test = new PageMethodsTest();
    $test->runAll();
}
