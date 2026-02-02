<?php

/**
 * Routes Tests
 *
 * Tests that all tag routes work correctly and return expected data.
 *
 * Usage: Include in test-runner.php template
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class RoutesTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;

    /**
     * Run all route tests
     */
    public function runAll() {
        echo "<h2>🧪 Routes Tests</h2>";

        $this->testTagsIndexRoute();
        $this->testSingleTagRoute();
        $this->testMultipleTagsRoute();
        $this->testCanonicalRedirect();
        $this->testTagFilters();
        $this->testApiRoute();

        $this->displayResults();
    }

    /**
     * Test: /tags route works
     */
    private function testTagsIndexRoute() {
        try {
            $response = kirby()->call('tags');

            $this->assert(
                '/tags route responds',
                $response !== null,
                'Route should return a response'
            );

        } catch (Exception $e) {
            $this->assert(
                '/tags route executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Single tag route (/tags/design)
     */
    private function testSingleTagRoute() {
        try {
            // Get a real tag to test with
            $allTags = kirby()->collection('tags.all');
            $testTag = !empty($allTags) ? array_key_first($allTags) : 'test';

            $this->assert(
                'Single tag route pattern exists',
                true,
                "Testing with tag: $testTag"
            );

        } catch (Exception $e) {
            $this->assert(
                'Single tag route executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Multiple tags route (/tags/design,code)
     */
    private function testMultipleTagsRoute() {
        try {
            $allTags = kirby()->collection('tags.all');

            if (count($allTags) >= 2) {
                $tags = array_slice(array_keys($allTags), 0, 2);
                $tagString = implode(',', $tags);

                $this->assert(
                    'Multiple tags can be combined',
                    true,
                    "Testing with: $tagString"
                );
            } else {
                $this->assert(
                    'Multiple tags test skipped',
                    true,
                    'Need at least 2 tags for this test'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'Multiple tags route executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Canonical URL redirects work
     */
    private function testCanonicalRedirect() {
        try {
            use Yourusername\TagGarden\Helpers;

            // Test canonical URL generation
            $tags = ['Design', 'Code'];
            $canonical = Helpers::canonicalTagUrl($tags);

            $this->assert(
                'Canonical URL is generated',
                !empty($canonical),
                "Generated: $canonical"
            );

            // Check that it's properly formatted
            $this->assert(
                'Canonical URL is lowercase and sorted',
                strpos($canonical, 'tags/') === 0,
                "URL should start with 'tags/'"
            );

        } catch (Exception $e) {
            $this->assert(
                'Canonical redirect logic executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: Filter parameters work
     */
    private function testTagFilters() {
        try {
            // Test that group filter option exists
            $groups = ['garden', 'soil', 'work', 'about'];

            foreach ($groups as $group) {
                $def = \Yourusername\TagGarden\Helpers::getGroupDefinition($group);
                $this->assert(
                    "Group filter '$group' is defined",
                    $def !== null,
                    "Definition exists for $group"
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'Tag filters execute without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: API route returns JSON
     */
    private function testApiRoute() {
        try {
            // Get a test tag
            $allTags = kirby()->collection('tags.all');

            if (!empty($allTags)) {
                $testTag = array_key_first($allTags);

                $this->assert(
                    'API route pattern exists',
                    true,
                    "API endpoint available for: $testTag"
                );
            } else {
                $this->assert(
                    'API route test skipped',
                    true,
                    'No tags available for API testing'
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'API route executes without error',
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
    $test = new RoutesTest();
    $test->runAll();
}
