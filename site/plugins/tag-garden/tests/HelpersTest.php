<?php

/**
 * Helper Class Tests
 *
 * Tests the Yourusername\TagGarden\Helpers static class methods
 * Verifies data retrieval, calculations, and transformations work correctly.
 *
 * Usage: Include in test-runner.php template
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class HelpersTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;

    /**
     * Run all helper function tests
     */
    public function runAll() {
        echo "<h2>🧪 Helper Class Tests</h2>";

        // Check if class exists
        if (!class_exists('Yourusername\TagGarden\Helpers')) {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px;'>";
            echo "❌ Yourusername\TagGarden\Helpers class not found. Make sure lib/Helpers.php is loaded.";
            echo "</p>";
            return false;
        }

        $this->testGetGrowthDefinition();
        $this->testGetGroupDefinition();
        $this->testGetThemeDefinition();
        $this->testGetLengthCategory();
        $this->testGetLengthLabel();
        $this->testFormatReadingTime();
        $this->testGetSortMethods();
        $this->testGetTagFontSize();
        $this->testSanitizeTag();
        $this->testTagsToUrl();
        $this->testUrlToTags();
        $this->testGetAllTags();
        $this->testGetPagesByTags();
        $this->testSortPages();

        $this->displayResults();
    }

    /**
     * Test: getGrowthDefinition returns correct data
     */
    private function testGetGrowthDefinition() {
        // Test valid status
        $seedling = \Yourusername\TagGarden\Helpers::getGrowthDefinition('seedling');
        $this->assert(
            'getGrowthDefinition returns array for valid status',
            is_array($seedling),
            'Expected array, got: ' . gettype($seedling)
        );

        if (is_array($seedling)) {
            $this->assert(
                'Growth definition contains label',
                isset($seedling['label']),
                'Missing label key'
            );

            $this->assert(
                'Growth definition contains emoji',
                isset($seedling['emoji']),
                'Missing emoji key'
            );
        }

        // Test invalid status
        $invalid = \Yourusername\TagGarden\Helpers::getGrowthDefinition('invalid-status');
        $this->assert(
            'getGrowthDefinition returns null for invalid status',
            $invalid === null,
            'Expected null for invalid status'
        );
    }

    /**
     * Test: getGroupDefinition returns correct data
     */
    private function testGetGroupDefinition() {
        // Test valid group
        $garden = \Yourusername\TagGarden\Helpers::getGroupDefinition('garden');
        $this->assert(
            'getGroupDefinition returns array for valid group',
            is_array($garden),
            'Expected array, got: ' . gettype($garden)
        );

        if (is_array($garden)) {
            $this->assert(
                'Group definition contains types array',
                isset($garden['types']) && is_array($garden['types']),
                'Missing or invalid types key'
            );
        }

        // Test all defined groups
        $groups = ['garden', 'soil', 'work', 'about'];
        foreach ($groups as $group) {
            $def = \Yourusername\TagGarden\Helpers::getGroupDefinition($group);
            $this->assert(
                "Group '{$group}' has definition",
                $def !== null,
                "Group '{$group}' should be defined"
            );
        }

        // Test invalid group
        $invalid = \Yourusername\TagGarden\Helpers::getGroupDefinition('invalid-group');
        $this->assert(
            'getGroupDefinition returns null for invalid group',
            $invalid === null,
            'Expected null for invalid group'
        );
    }

    /**
     * Test: getThemeDefinition returns correct data
     */
    private function testGetThemeDefinition() {
        // Test valid theme
        $topic = \Yourusername\TagGarden\Helpers::getThemeDefinition('topic');
        $this->assert(
            'getThemeDefinition returns array for valid theme',
            is_array($topic),
            'Expected array, got: ' . gettype($topic)
        );

        // Test all defined themes
        $themes = ['topic', 'medium', 'status', 'audience'];
        foreach ($themes as $theme) {
            $def = \Yourusername\TagGarden\Helpers::getThemeDefinition($theme);
            $this->assert(
                "Theme '{$theme}' has definition",
                $def !== null,
                "Theme '{$theme}' should be defined"
            );
        }
    }

    /**
     * Test: getLengthCategory calculates correctly
     */
    private function testGetLengthCategory() {
        // Test each threshold
        $tests = [
            ['count' => 300, 'expected' => 'quick'],
            ['count' => 600, 'expected' => 'short'],
            ['count' => 1200, 'expected' => 'medium'],
            ['count' => 2500, 'expected' => 'long'],
            ['count' => 4000, 'expected' => 'deep'],
        ];

        foreach ($tests as $test) {
            $result = \Yourusername\TagGarden\Helpers::getLengthCategory($test['count']);
            $this->assert(
                "{$test['count']} words = '{$test['expected']}' category",
                $result === $test['expected'],
                "Expected '{$test['expected']}', got '{$result}'"
            );
        }
    }

    /**
     * Test: getLengthLabel returns readable labels
     */
    private function testGetLengthLabel() {
        $categories = ['quick', 'short', 'medium', 'long', 'epic'];

        foreach ($categories as $category) {
            $label = \Yourusername\TagGarden\Helpers::getLengthLabel($category);
            $this->assert(
                "Category '{$category}' has label",
                is_string($label) && !empty($label),
                "Expected non-empty string, got: " . var_export($label, true)
            );
        }
    }

    /**
     * Test: formatReadingTime formats correctly
     */
    private function testFormatReadingTime() {
        // Test seconds display (same time)
        $timeData = [
            'minSeconds' => 45,
            'maxSeconds' => 45,
            'minMinutes' => 1,
            'maxMinutes' => 1,
        ];
        $result = \Yourusername\TagGarden\Helpers::formatReadingTime($timeData);
        $this->assert(
            'Formats same time in seconds correctly',
            strpos($result, '45 sec read') !== false,
            "Expected '45 sec read', got: {$result}"
        );

        // Test seconds range
        $timeData = [
            'minSeconds' => 50,
            'maxSeconds' => 30,
            'minMinutes' => 1,
            'maxMinutes' => 1,
        ];
        $result = \Yourusername\TagGarden\Helpers::formatReadingTime($timeData);
        $this->assert(
            'Formats seconds range correctly',
            strpos($result, 'sec read') !== false && strpos($result, '–') !== false,
            "Expected range with '–', got: {$result}"
        );

        // Test minutes display
        $timeData = [
            'minSeconds' => 180,
            'maxSeconds' => 180,
            'minMinutes' => 3,
            'maxMinutes' => 3,
        ];
        $result = \Yourusername\TagGarden\Helpers::formatReadingTime($timeData);
        $this->assert(
            'Formats same time in minutes correctly',
            strpos($result, '3 min read') !== false,
            "Expected '3 min read', got: {$result}"
        );
    }

    /**
     * Test: getSortMethods returns array
     */
    private function testGetSortMethods() {
        $methods = \Yourusername\TagGarden\Helpers::getSortMethods();

        $this->assert(
            'getSortMethods returns array',
            is_array($methods),
            'Expected array, got: ' . gettype($methods)
        );

        $this->assert(
            'getSortMethods returns non-empty array',
            count($methods) > 0,
            'Expected at least one sort method'
        );

        // Check for expected sort methods
        $expectedMethods = ['planted', 'tended', 'notable', 'length-asc', 'length-desc', 'growth', 'title'];
        foreach ($expectedMethods as $method) {
            $this->assert(
                "Sort method '{$method}' exists",
                isset($methods[$method]),
                "Missing sort method: {$method}"
            );
        }
    }

    /**
     * Test: getTagFontSize calculates correctly
     */
    private function testGetTagFontSize() {
        // Test with range
        $minSize = \Yourusername\TagGarden\Helpers::getTagFontSize(1, 1, 10);  // Minimum usage
        $maxSize = \Yourusername\TagGarden\Helpers::getTagFontSize(10, 1, 10); // Maximum usage
        $midSize = \Yourusername\TagGarden\Helpers::getTagFontSize(5, 1, 10);  // Middle usage

        $this->assert(
            'Font size for min count is smallest',
            $minSize < $maxSize,
            "Min size ({$minSize}) should be < max size ({$maxSize})"
        );

        $this->assert(
            'Font size for middle count is between min and max',
            $midSize > $minSize && $midSize < $maxSize,
            "Mid size ({$midSize}) should be between min ({$minSize}) and max ({$maxSize})"
        );

        // Test with same count (avoid division by zero)
        $sameSize = \Yourusername\TagGarden\Helpers::getTagFontSize(5, 5, 5);
        $this->assert(
            'Font size handles same min/max count',
            is_float($sameSize) || is_int($sameSize),
            'Should return numeric value'
        );
    }

    /**
     * Test: sanitizeTag cleans input correctly
     */
    private function testSanitizeTag() {
        $tests = [
            ['input' => 'Design', 'expected' => 'design'],
            ['input' => 'Web-Design', 'expected' => 'web-design'],
            ['input' => 'Web Design', 'expected' => 'web design'],
            ['input' => 'Design123', 'expected' => 'design123'],
            ['input' => 'Design@#$%', 'expected' => 'design'],
        ];

        foreach ($tests as $test) {
            $result = \Yourusername\TagGarden\Helpers::sanitizeTag($test['input']);
            $this->assert(
                "Sanitizes '{$test['input']}' to '{$test['expected']}'",
                $result === $test['expected'],
                "Expected '{$test['expected']}', got: '{$result}'"
            );
        }
    }

    /**
     * Test: tagsToUrl converts array to URL string
     */
    private function testTagsToUrl() {
        $tags = ['design', 'web', 'tutorial'];
        $result = \Yourusername\TagGarden\Helpers::tagsToUrl($tags);

        $this->assert(
            'tagsToUrl returns string',
            is_string($result),
            'Expected string, got: ' . gettype($result)
        );

        $this->assert(
            'tagsToUrl contains all tags',
            strpos($result, 'design') !== false &&
            strpos($result, 'web') !== false &&
            strpos($result, 'tutorial') !== false,
            "Expected all tags in result, got: {$result}"
        );

        // Test with single tag
        $single = \Yourusername\TagGarden\Helpers::tagsToUrl(['design']);
        $this->assert(
            'tagsToUrl handles single tag',
            $single === 'design',
            "Expected 'design', got: {$single}"
        );
    }

    /**
     * Test: urlToTags converts URL string to array
     */
    private function testUrlToTags() {
        $separator = option('yourusername.tag-garden.url.tag-separator', '+');
        $urlString = 'design' . $separator . 'web' . $separator . 'tutorial';
        $result = \Yourusername\TagGarden\Helpers::urlToTags($urlString);

        $this->assert(
            'urlToTags returns array',
            is_array($result),
            'Expected array, got: ' . gettype($result)
        );

        $this->assert(
            'urlToTags returns correct count',
            count($result) === 3,
            'Expected 3 tags, got: ' . count($result)
        );

        // Test with single tag
        $single = \Yourusername\TagGarden\Helpers::urlToTags('design');
        $this->assert(
            'urlToTags handles single tag',
            is_array($single) && count($single) === 1,
            'Expected array with single tag'
        );
    }

    /**
     * Test: getAllTags returns tag counts
     */
    private function testGetAllTags() {
        $tags = \Yourusername\TagGarden\Helpers::getAllTags();

        $this->assert(
            'getAllTags returns array',
            is_array($tags),
            'Expected array, got: ' . gettype($tags)
        );

        // If there are tagged pages, should have tags
        $taggedPages = site()->index()->filterBy('tags', '!=', '')->count();
        if ($taggedPages > 0) {
            $this->assert(
                'getAllTags returns tags when pages are tagged',
                count($tags) > 0,
                "Found {$taggedPages} tagged pages but getAllTags returned 0 tags"
            );
        }
    }

    /**
     * Test: getPagesByTags filters correctly
     */
    private function testGetPagesByTags() {
        $pages = \Yourusername\TagGarden\Helpers::getPagesByTags('design');

        $this->assert(
            'getPagesByTags returns Pages collection',
            $pages instanceof \Kirby\Cms\Pages,
            'Expected Pages collection, got: ' . get_class($pages)
        );

        // Test with array
        $pagesArray = \Yourusername\TagGarden\Helpers::getPagesByTags(['design', 'web']);
        $this->assert(
            'getPagesByTags accepts array of tags',
            $pagesArray instanceof \Kirby\Cms\Pages,
            'Should handle array of tags'
        );

        // Test empty input
        $emptyPages = \Yourusername\TagGarden\Helpers::getPagesByTags([]);
        $this->assert(
            'getPagesByTags returns empty collection for empty input',
            $emptyPages->count() === 0,
            'Should return empty collection'
        );
    }

    /**
     * Test: sortPages sorts correctly
     */
    private function testSortPages() {
        $pages = site()->index()->limit(5);

        // Test each sort method
        $methods = ['planted', 'tended', 'notable', 'length-asc', 'length-desc', 'growth', 'title'];

        foreach ($methods as $method) {
            $sorted = \Yourusername\TagGarden\Helpers::sortPages($pages, $method);
            $this->assert(
                "sortPages handles '{$method}' method",
                $sorted instanceof \Kirby\Cms\Pages,
                "Expected Pages collection for {$method}"
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
    $test = new HelpersTest();
    $test->runAll();
}
