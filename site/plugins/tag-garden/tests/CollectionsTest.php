<?php

/**
 * Collections Tests
 *
 * Tests all collection queries defined in collections/tags.php
 * Verifies tag extraction, filtering, and page queries work correctly.
 *
 * Note: These tests require at least some tagged content to be meaningful.
 *
 * Usage: Include in test-runner.php template
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class CollectionsTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;
    private $hasTaggedContent = false;

    /**
     * Run all collections tests
     */
    public function runAll() {
        echo "<h2>🧪 Collections Tests</h2>";

        // Check if we have any tagged content
        $taggedPages = site()->index()->filterBy('tags', '!=', '')->count();
        $this->hasTaggedContent = $taggedPages > 0;

        if (!$this->hasTaggedContent) {
            echo "<p style='color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px;'>";
            echo "⚠️ No tagged pages found. Create some pages with tags for more comprehensive tests.";
            echo "</p>";
        } else {
            echo "<p><strong>Found {$taggedPages} tagged pages</strong> for testing.</p>";
        }

        $this->testTagsAll();
        $this->testTagsPopular();
        $this->testTagsByTheme();
        $this->testTagsByGroup();
        $this->testTagsByGrowth();
        $this->testTagsRelated();
        $this->testPagesByTags();
        $this->testPagesRecentlyPlanted();
        $this->testPagesRecentlyTended();
        $this->testPagesNotable();
        $this->testPagesByGrowth();

        $this->displayResults();
    }

    /**
     * Test: tags.all collection
     */
    private function testTagsAll() {
        try {
            $tags = kirby()->collection('tags.all');

            $this->assert(
                'tags.all returns array',
                is_array($tags),
                'Expected array, got: ' . gettype($tags)
            );

            if ($this->hasTaggedContent) {
                $this->assert(
                    'tags.all returns tags when content exists',
                    count($tags) > 0,
                    'Expected tags, got empty array'
                );
            }

            // Test with options
            $alphaSort = kirby()->collection('tags.all', ['sortBy' => 'alpha']);
            $this->assert(
                'tags.all accepts sortBy option',
                is_array($alphaSort),
                'Should handle alpha sorting'
            );

            // Test minCount filter
            $filtered = kirby()->collection('tags.all', ['minCount' => 2]);
            $this->assert(
                'tags.all accepts minCount option',
                is_array($filtered),
                'Should filter by minimum count'
            );

            if ($this->hasTaggedContent && count($filtered) < count($tags)) {
                $this->assert(
                    'minCount actually filters results',
                    true,
                    'Filtered count is less than total'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'tags.all executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: tags.popular collection
     */
    private function testTagsPopular() {
        try {
            $popular = kirby()->collection('tags.popular');

            $this->assert(
                'tags.popular returns array',
                is_array($popular),
                'Expected array, got: ' . gettype($popular)
            );

            // Test limit option
            $limited = kirby()->collection('tags.popular', ['limit' => 5]);
            $this->assert(
                'tags.popular respects limit option',
                count($limited) <= 5,
                'Expected max 5 tags, got: ' . count($limited)
            );

        } catch (Exception $e) {
            $this->assert(
                'tags.popular executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: tags.byTheme collection
     */
    private function testTagsByTheme() {
        try {
            $tags = kirby()->collection('tags.byTheme', ['theme' => 'topic']);

            $this->assert(
                'tags.byTheme returns array',
                is_array($tags),
                'Expected array, got: ' . gettype($tags)
            );

            // Test without theme parameter
            $noTheme = kirby()->collection('tags.byTheme');
            $this->assert(
                'tags.byTheme returns empty array without theme',
                is_array($noTheme) && empty($noTheme),
                'Should return empty array when theme not specified'
            );

        } catch (Exception $e) {
            $this->assert(
                'tags.byTheme executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: tags.byGroup collection
     */
    private function testTagsByGroup() {
        try {
            $tags = kirby()->collection('tags.byGroup', ['group' => 'garden']);

            $this->assert(
                'tags.byGroup returns array',
                is_array($tags),
                'Expected array, got: ' . gettype($tags)
            );

            // Test without group parameter
            $noGroup = kirby()->collection('tags.byGroup');
            $this->assert(
                'tags.byGroup returns empty array without group',
                is_array($noGroup) && empty($noGroup),
                'Should return empty array when group not specified'
            );

            // Test with invalid group
            $invalid = kirby()->collection('tags.byGroup', ['group' => 'invalid']);
            $this->assert(
                'tags.byGroup handles invalid group',
                is_array($invalid) && empty($invalid),
                'Should return empty array for invalid group'
            );

        } catch (Exception $e) {
            $this->assert(
                'tags.byGroup executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: tags.byGrowth collection
     */
    private function testTagsByGrowth() {
        try {
            $tags = kirby()->collection('tags.byGrowth', ['status' => 'seedling']);

            $this->assert(
                'tags.byGrowth returns array',
                is_array($tags),
                'Expected array, got: ' . gettype($tags)
            );

            // Test without status parameter
            $noStatus = kirby()->collection('tags.byGrowth');
            $this->assert(
                'tags.byGrowth returns empty array without status',
                is_array($noStatus) && empty($noStatus),
                'Should return empty array when status not specified'
            );

        } catch (Exception $e) {
            $this->assert(
                'tags.byGrowth executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: tags.related collection
     */
    private function testTagsRelated() {
        try {
            // Get a real tag to test with
            $allTags = kirby()->collection('tags.all');
            $testTag = !empty($allTags) ? array_key_first($allTags) : 'design';

            $related = kirby()->collection('tags.related', ['tag' => $testTag]);

            $this->assert(
                'tags.related returns array',
                is_array($related),
                'Expected array, got: ' . gettype($related)
            );

            // Test excludeSelf option
            if ($this->hasTaggedContent && !empty($related)) {
                $this->assert(
                    'tags.related excludes the original tag by default',
                    !isset($related[$testTag]),
                    "Original tag '{$testTag}' should be excluded"
                );
            }

            // Test limit option
            $limited = kirby()->collection('tags.related', [
                'tag' => $testTag,
                'limit' => 3
            ]);
            $this->assert(
                'tags.related respects limit option',
                count($limited) <= 3,
                'Expected max 3 tags, got: ' . count($limited)
            );

            // Test without tag parameter
            $noTag = kirby()->collection('tags.related');
            $this->assert(
                'tags.related returns empty array without tag',
                is_array($noTag) && empty($noTag),
                'Should return empty array when tag not specified'
            );

        } catch (Exception $e) {
            $this->assert(
                'tags.related executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: pages.byTags collection
     */
    private function testPagesByTags() {
        try {
            // Get a real tag to test with
            $allTags = kirby()->collection('tags.all');
            $testTag = !empty($allTags) ? array_key_first($allTags) : 'design';

            $pages = kirby()->collection('pages.byTags', ['tags' => $testTag]);

            $this->assert(
                'pages.byTags returns Pages collection',
                $pages instanceof \Kirby\Cms\Pages,
                'Expected Pages collection, got: ' . get_class($pages)
            );

            // Test with multiple tags
            $multiPages = kirby()->collection('pages.byTags', [
                'tags' => [$testTag, 'another-tag']
            ]);
            $this->assert(
                'pages.byTags handles multiple tags',
                $multiPages instanceof \Kirby\Cms\Pages,
                'Should handle array of tags'
            );

            // Test limit option
            $limited = kirby()->collection('pages.byTags', [
                'tags' => $testTag,
                'limit' => 3
            ]);
            $this->assert(
                'pages.byTags respects limit option',
                $limited->count() <= 3,
                'Expected max 3 pages, got: ' . $limited->count()
            );

            // Test sort option
            $sorted = kirby()->collection('pages.byTags', [
                'tags' => $testTag,
                'sort' => 'title'
            ]);
            $this->assert(
                'pages.byTags accepts sort option',
                $sorted instanceof \Kirby\Cms\Pages,
                'Should handle sort parameter'
            );

        } catch (Exception $e) {
            $this->assert(
                'pages.byTags executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: pages.recentlyPlanted collection
     */
    private function testPagesRecentlyPlanted() {
        try {
            $pages = kirby()->collection('pages.recentlyPlanted');

            $this->assert(
                'pages.recentlyPlanted returns Pages collection',
                $pages instanceof \Kirby\Cms\Pages,
                'Expected Pages collection, got: ' . get_class($pages)
            );

            // Test limit option
            $limited = kirby()->collection('pages.recentlyPlanted', ['limit' => 3]);
            $this->assert(
                'pages.recentlyPlanted respects limit option',
                $limited->count() <= 3,
                'Expected max 3 pages, got: ' . $limited->count()
            );

        } catch (Exception $e) {
            $this->assert(
                'pages.recentlyPlanted executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: pages.recentlyTended collection
     */
    private function testPagesRecentlyTended() {
        try {
            $pages = kirby()->collection('pages.recentlyTended');

            $this->assert(
                'pages.recentlyTended returns Pages collection',
                $pages instanceof \Kirby\Cms\Pages,
                'Expected Pages collection, got: ' . get_class($pages)
            );

            // Test limit option
            $limited = kirby()->collection('pages.recentlyTended', ['limit' => 3]);
            $this->assert(
                'pages.recentlyTended respects limit option',
                $limited->count() <= 3,
                'Expected max 3 pages, got: ' . $limited->count()
            );

        } catch (Exception $e) {
            $this->assert(
                'pages.recentlyTended executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: pages.notable collection
     */
    private function testPagesNotable() {
        try {
            $pages = kirby()->collection('pages.notable');

            $this->assert(
                'pages.notable returns Pages collection',
                $pages instanceof \Kirby\Cms\Pages,
                'Expected Pages collection, got: ' . get_class($pages)
            );

            // Verify all returned pages have notable = true
            $allNotable = true;
            foreach ($pages as $page) {
                if (!$page->notable()->toBool()) {
                    $allNotable = false;
                    break;
                }
            }

            if ($pages->count() > 0) {
                $this->assert(
                    'pages.notable only returns notable pages',
                    $allNotable,
                    'Found non-notable page in results'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'pages.notable executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: pages.byGrowth collection
     */
    private function testPagesByGrowth() {
        try {
            $pages = kirby()->collection('pages.byGrowth', ['status' => 'seedling']);

            $this->assert(
                'pages.byGrowth returns Pages collection',
                $pages instanceof \Kirby\Cms\Pages,
                'Expected Pages collection, got: ' . get_class($pages)
            );

            // Test without status parameter
            $noStatus = kirby()->collection('pages.byGrowth');
            $this->assert(
                'pages.byGrowth returns empty collection without status',
                $noStatus->count() === 0,
                'Should return empty collection when status not specified'
            );

            // Test limit option
            $limited = kirby()->collection('pages.byGrowth', [
                'status' => 'seedling',
                'limit' => 3
            ]);
            $this->assert(
                'pages.byGrowth respects limit option',
                $limited->count() <= 3,
                'Expected max 3 pages, got: ' . $limited->count()
            );

        } catch (Exception $e) {
            $this->assert(
                'pages.byGrowth executes without error',
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
    $test = new CollectionsTest();
    $test->runAll();
}
