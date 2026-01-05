<?php

/**
 * Controllers Tests
 *
 * Tests that controllers return the correct data structures for templates.
 * Verifies all expected variables are present and correctly typed.
 *
 * Usage: Include in test-runner.php template
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class ControllersTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;

    /**
     * Run all controller tests
     */
    public function runAll() {
        echo "<h2>🧪 Controllers Tests</h2>";

        $this->testTagsControllerExists();
        $this->testTagControllerExists();
        $this->testTagsControllerData();
        $this->testTagControllerData();

        $this->displayResults();
    }

    /**
     * Test: tags controller exists and is callable
     */
    private function testTagsControllerExists() {
        $controllerPath = kirby()->root('plugins') . '/tag-garden/controllers/tags.php';

        $this->assert(
            'tags.php controller file exists',
            file_exists($controllerPath),
            "File not found at: {$controllerPath}"
        );

        if (file_exists($controllerPath)) {
            $controller = require $controllerPath;

            $this->assert(
                'tags.php returns callable',
                is_callable($controller),
                'Controller should return a function'
            );
        }
    }

    /**
     * Test: tag controller exists and is callable
     */
    private function testTagControllerExists() {
        $controllerPath = kirby()->root('plugins') . '/tag-garden/controllers/tag.php';

        $this->assert(
            'tag.php controller file exists',
            file_exists($controllerPath),
            "File not found at: {$controllerPath}"
        );

        if (file_exists($controllerPath)) {
            $controller = require $controllerPath;

            $this->assert(
                'tag.php returns callable',
                is_callable($controller),
                'Controller should return a function'
            );
        }
    }

    /**
     * Test: tags controller returns correct data structure
     */
    private function testTagsControllerData() {
        try {
            $controllerPath = kirby()->root('plugins') . '/tag-garden/controllers/tags.php';

            if (!file_exists($controllerPath)) {
                $this->assert('tags controller loads', false, 'Controller file not found');
                return;
            }

            $controller = require $controllerPath;

            // Create a mock page for testing
            $mockPage = page() ?? site()->homePage();

            // Call controller
            $data = $controller(kirby(), $mockPage);

            $this->assert(
                'tags controller returns array',
                is_array($data),
                'Expected array, got: ' . gettype($data)
            );

            // Check for required keys
            $requiredKeys = [
                'tags',
                'sortedTags',
                'minCount',
                'maxCount',
                'totalTags',
                'totalTaggedPages',
                'sort',
                'groups',
                'themes',
                'sortMethods',
                'recentlyTended',
                'notablePages',
                'popularTags',
            ];

            foreach ($requiredKeys as $key) {
                $this->assert(
                    "tags controller includes '{$key}'",
                    isset($data[$key]),
                    "Missing required key: {$key}"
                );
            }

            // Check data types
            if (isset($data['tags'])) {
                $this->assert(
                    'tags controller "tags" is array',
                    is_array($data['tags']),
                    'Expected array'
                );
            }

            if (isset($data['sortedTags'])) {
                $this->assert(
                    'tags controller "sortedTags" is array',
                    is_array($data['sortedTags']),
                    'Expected array'
                );
            }

            if (isset($data['recentlyTended'])) {
                $this->assert(
                    'tags controller "recentlyTended" is Pages collection',
                    $data['recentlyTended'] instanceof \Kirby\Cms\Pages,
                    'Expected Pages collection'
                );
            }

            if (isset($data['notablePages'])) {
                $this->assert(
                    'tags controller "notablePages" is Pages collection',
                    $data['notablePages'] instanceof \Kirby\Cms\Pages,
                    'Expected Pages collection'
                );
            }

            // Check helper functions exist
            $helperFunctions = ['getTagFontSize', 'getTagUrl', 'isActiveGroup', 'isActiveTheme'];
            foreach ($helperFunctions as $func) {
                $this->assert(
                    "tags controller includes '{$func}' helper",
                    isset($data[$func]) && is_callable($data[$func]),
                    "Helper function '{$func}' should be callable"
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'tags controller executes without error',
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Test: tag controller returns correct data structure
     */
    private function testTagControllerData() {
        try {
            $controllerPath = kirby()->root('plugins') . '/tag-garden/controllers/tag.php';

            if (!file_exists($controllerPath)) {
                $this->assert('tag controller loads', false, 'Controller file not found');
                return;
            }

            $controller = require $controllerPath;

            // Create a mock page with filter tags
            $mockPage = new \Kirby\Cms\Page([
                'slug' => 'test-tag',
                'template' => 'tag',
                'content' => [
                    'filterTags' => ['design', 'web']
                ]
            ]);

            // Call controller
            $data = $controller(kirby(), $mockPage);

            $this->assert(
                'tag controller returns array',
                is_array($data),
                'Expected array, got: ' . gettype($data)
            );

            // Check for required keys
            $requiredKeys = [
                'filterTags',
                'pages',
                'groupedPages',
                'relatedTags',
                'tagCount',
                'sort',
                'logic',
                'growthStats',
                'lengthStats',
                'avgWords',
                'sortMethods',
                'groups',
            ];

            foreach ($requiredKeys as $key) {
                $this->assert(
                    "tag controller includes '{$key}'",
                    isset($data[$key]),
                    "Missing required key: {$key}"
                );
            }

            // Check data types
            if (isset($data['filterTags'])) {
                $this->assert(
                    'tag controller "filterTags" is array',
                    is_array($data['filterTags']),
                    'Expected array'
                );
            }

            if (isset($data['pages'])) {
                $this->assert(
                    'tag controller "pages" is Pages collection',
                    $data['pages'] instanceof \Kirby\Cms\Pages,
                    'Expected Pages collection'
                );
            }

            if (isset($data['groupedPages'])) {
                $this->assert(
                    'tag controller "groupedPages" is array',
                    is_array($data['groupedPages']),
                    'Expected array'
                );
            }

            if (isset($data['relatedTags'])) {
                $this->assert(
                    'tag controller "relatedTags" is array',
                    is_array($data['relatedTags']),
                    'Expected array'
                );
            }

            if (isset($data['growthStats'])) {
                $this->assert(
                    'tag controller "growthStats" is array',
                    is_array($data['growthStats']),
                    'Expected array with growth status counts'
                );

                // Check for expected growth statuses
                $expectedStatuses = ['seedling', 'budding', 'evergreen', 'wilting'];
                foreach ($expectedStatuses as $status) {
                    $this->assert(
                        "growthStats includes '{$status}'",
                        isset($data['growthStats'][$status]),
                        "Missing growth status: {$status}"
                    );
                }
            }

            if (isset($data['lengthStats'])) {
                $this->assert(
                    'tag controller "lengthStats" is array',
                    is_array($data['lengthStats']),
                    'Expected array with length categories'
                );
            }

            // Check helper functions exist
            $helperFunctions = [
                'getTagUrl',
                'getCombinedTagUrl',
                'isActiveSort',
                'getGrowthDefinition',
                'getLengthLabel',
                'getPaginatedPages'
            ];

            foreach ($helperFunctions as $func) {
                $this->assert(
                    "tag controller includes '{$func}' helper",
                    isset($data[$func]) && is_callable($data[$func]),
                    "Helper function '{$func}' should be callable"
                );
            }

        } catch (Exception $e) {
            $this->assert(
                'tag controller executes without error',
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
    $test = new ControllersTest();
    $test->runAll();
}
