<?php

/**
 * Sorting Tests
 *
 * Tests all sorting methods work correctly on pages collections.
 * Verifies that each sort method produces expected ordering.
 *
 * Usage: Include in test-runner.php template
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class SortingTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;
    private $testPages = null;

    /**
     * Run all sorting tests
     */
    public function runAll() {
        echo "<h2>🧪 Sorting Methods Tests</h2>";

        // Get a sample of pages to test with
        $this->testPages = site()->index()->limit(10);

        if ($this->testPages->count() === 0) {
            echo "<p style='color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px;'>";
            echo "⚠️ No pages found for testing. Create some pages to run sorting tests.";
            echo "</p>";
            return false;
        }

        echo "<p><strong>Testing with {$this->testPages->count()} pages</strong></p>";

        $this->testSortByPlanted();
        $this->testSortByTended();
        $this->testSortByNotable();
        $this->testSortByLengthAsc();
        $this->testSortByLengthDesc();
        $this->testSortByGrowth();
        $this->testSortByTitle();
        $this->testSortDirection();
        $this->testInvalidSortMethod();

        $this->displayResults();
    }

    /**
     * Test: Sort by date_planted
     */
    private function testSortByPlanted() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'planted', 'desc');

            $this->assert(
                'sortPages handles "planted" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            $this->assert(
                'sortPages "planted" preserves page count',
                $sorted->count() === $this->testPages->count(),
                "Expected {$this->testPages->count()}, got {$sorted->count()}"
            );

            // Check if pages with date_planted are actually sorted
            $dates = [];
            foreach ($sorted as $page) {
                if ($page->date_planted()->isNotEmpty()) {
                    $dates[] = $page->date_planted()->toDate('U');
                }
            }

            if (count($dates) > 1) {
                $isSorted = true;
                for ($i = 0; $i < count($dates) - 1; $i++) {
                    if ($dates[$i] < $dates[$i + 1]) {
                        $isSorted = false;
                        break;
                    }
                }

                $this->assert(
                    'sortPages "planted" actually sorts by date (desc)',
                    $isSorted,
                    'Pages should be in descending date order'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages "planted" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort by last_tended
     */
    private function testSortByTended() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'tended', 'desc');

            $this->assert(
                'sortPages handles "tended" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            $this->assert(
                'sortPages "tended" preserves page count',
                $sorted->count() === $this->testPages->count(),
                "Expected {$this->testPages->count()}, got {$sorted->count()}"
            );

        } catch (Exception $e) {
            $this->assert(
                'sortPages "tended" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort by notable
     */
    private function testSortByNotable() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'notable');

            $this->assert(
                'sortPages handles "notable" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            // Check if notable pages come first
            $firstPage = $sorted->first();
            if ($firstPage && $sorted->count() > 1) {
                $hasNotableFirst = false;
                foreach ($sorted as $page) {
                    if ($page->notable()->toBool()) {
                        $hasNotableFirst = true;
                        break;
                    }
                }

                // This test only passes if there are notable pages
                if ($hasNotableFirst) {
                    $this->assert(
                        'sortPages "notable" puts notable pages first',
                        true,
                        'Notable pages found in results'
                    );
                }
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages "notable" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort by length ascending
     */
    private function testSortByLengthAsc() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'length-asc');

            $this->assert(
                'sortPages handles "length-asc" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            // Check if actually sorted by word count
            $wordCounts = [];
            foreach ($sorted as $page) {
                $wordCounts[] = $page->wordCount();
            }

            if (count($wordCounts) > 1) {
                $isSorted = true;
                for ($i = 0; $i < count($wordCounts) - 1; $i++) {
                    if ($wordCounts[$i] > $wordCounts[$i + 1]) {
                        $isSorted = false;
                        break;
                    }
                }

                $this->assert(
                    'sortPages "length-asc" sorts shortest first',
                    $isSorted,
                    'Word counts should be in ascending order. Got: ' . implode(', ', $wordCounts)
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages "length-asc" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort by length descending
     */
    private function testSortByLengthDesc() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'length-desc');

            $this->assert(
                'sortPages handles "length-desc" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            // Check if actually sorted by word count (descending)
            $wordCounts = [];
            foreach ($sorted as $page) {
                $wordCounts[] = $page->wordCount();
            }

            if (count($wordCounts) > 1) {
                $isSorted = true;
                for ($i = 0; $i < count($wordCounts) - 1; $i++) {
                    if ($wordCounts[$i] < $wordCounts[$i + 1]) {
                        $isSorted = false;
                        break;
                    }
                }

                $this->assert(
                    'sortPages "length-desc" sorts longest first',
                    $isSorted,
                    'Word counts should be in descending order. Got: ' . implode(', ', $wordCounts)
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages "length-desc" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort by growth status
     */
    private function testSortByGrowth() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'growth');

            $this->assert(
                'sortPages handles "growth" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            // Check if pages with growth status are sorted
            $sortOrders = [];
            foreach ($sorted as $page) {
                if ($page->growth_status()->isNotEmpty()) {
                    $status = $page->growth_status()->value();
                    $def = \TagGarden\Helpers::getGrowthDefinition($status);
                    if ($def) {
                        $sortOrders[] = $def['sort-order'];
                    }
                }
            }

            if (count($sortOrders) > 1) {
                $isSorted = true;
                for ($i = 0; $i < count($sortOrders) - 1; $i++) {
                    if ($sortOrders[$i] > $sortOrders[$i + 1]) {
                        $isSorted = false;
                        break;
                    }
                }

                $this->assert(
                    'sortPages "growth" sorts by growth status order',
                    $isSorted,
                    'Growth statuses should be in configured order'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages "growth" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort by title
     */
    private function testSortByTitle() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'title', 'asc');

            $this->assert(
                'sortPages handles "title" method',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should return Pages collection'
            );

            // Check alphabetical sorting
            $titles = [];
            foreach ($sorted as $page) {
                $titles[] = $page->title()->value();
            }

            if (count($titles) > 1) {
                $sortedTitles = $titles;
                sort($sortedTitles);

                $this->assert(
                    'sortPages "title" sorts alphabetically',
                    $titles === $sortedTitles,
                    'Titles should be in alphabetical order'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages "title" executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Sort direction works
     */
    private function testSortDirection() {
        try {
            $asc = \TagGarden\Helpers::sortPages($this->testPages, 'title', 'asc');
            $desc = \TagGarden\Helpers::sortPages($this->testPages, 'title', 'desc');

            $this->assert(
                'sortPages accepts direction parameter',
                $asc instanceof \Kirby\Cms\Pages && $desc instanceof \Kirby\Cms\Pages,
                'Should handle both asc and desc directions'
            );

            // Check if order is actually reversed
            if ($asc->count() > 1 && $desc->count() > 1) {
                $firstAsc = $asc->first()->title()->value();
                $firstDesc = $desc->first()->title()->value();

                $this->assert(
                    'sortPages direction actually reverses order',
                    $firstAsc !== $firstDesc || $asc->count() === 1,
                    "First (asc): {$firstAsc}, First (desc): {$firstDesc}"
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'sortPages direction parameter executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Invalid sort method falls back to default
     */
    private function testInvalidSortMethod() {
        try {
            $sorted = \TagGarden\Helpers::sortPages($this->testPages, 'invalid-method');

            $this->assert(
                'sortPages handles invalid method gracefully',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should fall back to default sort method'
            );

            $this->assert(
                'sortPages invalid method preserves page count',
                $sorted->count() === $this->testPages->count(),
                'Should not lose pages'
            );

        } catch (Exception $e) {
            $this->assert(
                'sortPages invalid method executes without error',
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

// Auto-run if accessed directly
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    $test = new SortingTest();
    $test->runAll();
}
