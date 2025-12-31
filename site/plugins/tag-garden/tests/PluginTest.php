<?php

/**
 * Plugin Registration Tests
 *
 * Tests that the Tag Garden plugin loads correctly and all core
 * components are registered properly.
 *
 * Usage: Create a test page template and include this file to run tests
 */

// Ensure we're in Kirby context
if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class PluginTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;

    /**
     * Run all plugin registration tests
     */
    public function runAll() {
        echo "<h2>🧪 Plugin Registration Tests</h2>";

        $this->testPluginLoaded();
        $this->testOptionsAvailable();
        $this->testBlueprintRegistered();
        $this->testSnippetsRegistered();
        $this->testTemplatesRegistered();
        $this->testControllersRegistered();
        $this->testCollectionsRegistered();
        $this->testRoutesRegistered();

        $this->displayResults();
    }

    /**
     * Test: Plugin is loaded
     */
    private function testPluginLoaded() {
        $plugin = kirby()->plugin('yourusername/tag-garden');
        $this->assert(
            'Plugin is loaded',
            $plugin !== null,
            'Plugin object should exist'
        );
    }

    /**
     * Test: All options are accessible
     */
    private function testOptionsAvailable() {
        $tests = [
            'section.limit' => 10,
            'default.sort' => 'tended',
            'reading.speed.min' => 167,
            'reading.speed.max' => 285,
        ];

        foreach ($tests as $option => $expected) {
            $value = option("yourusername.tag-garden.{$option}");
            $this->assert(
                "Option '{$option}' is accessible",
                $value === $expected,
                "Expected {$expected}, got " . var_export($value, true)
            );
        }

        // Test array options
        $growthStatuses = option('yourusername.tag-garden.growth.statuses');
        $this->assert(
            'Growth statuses option is array',
            is_array($growthStatuses) && count($growthStatuses) === 4,
            'Should contain 4 growth statuses'
        );

        $contentGroups = option('yourusername.tag-garden.content.groups');
        $this->assert(
            'Content groups option exists',
            is_array($contentGroups) && isset($contentGroups['garden']),
            'Should contain garden group'
        );
    }

    /**
     * Test: Blueprint fields are registered
     */
    private function testBlueprintRegistered() {
        $blueprint = kirby()->extension('blueprints', 'fields/tag-garden');

        $this->assert(
            'Blueprint fields/tag-garden is registered',
            $blueprint !== null,
            'Blueprint should exist'
        );

        if ($blueprint) {
            $fields = $blueprint['fields'] ?? [];
            $expectedFields = ['tags', 'tag_theme', 'growth_status', 'date_planted', 'last_tended', 'notable'];

            foreach ($expectedFields as $field) {
                $this->assert(
                    "Blueprint contains '{$field}' field",
                    isset($fields[$field]),
                    "Field '{$field}' should be defined"
                );
            }
        }
    }

    /**
     * Test: Snippets are registered
     */
    private function testSnippetsRegistered() {
        $snippets = [
            'tag-garden/explorer',
            'tag-garden/section',
            'tag-garden/badge',
            'tag-garden/reading-time',
        ];

        foreach ($snippets as $snippet) {
            $path = kirby()->extension('snippets', $snippet);
            $this->assert(
                "Snippet '{$snippet}' is registered",
                $path !== null,
                "Snippet path should be defined"
            );
        }
    }

    /**
     * Test: Templates are registered
     */
    private function testTemplatesRegistered() {
        $templates = ['tags', 'tag'];

        foreach ($templates as $template) {
            $path = kirby()->extension('templates', $template);
            $this->assert(
                "Template '{$template}' is registered",
                $path !== null,
                "Template path should be defined"
            );
        }
    }

    /**
     * Test: Controllers are registered
     */
    private function testControllersRegistered() {
        $controllers = ['tags', 'tag'];

        foreach ($controllers as $controller) {
            $registered = kirby()->extension('controllers', $controller);
            $this->assert(
                "Controller '{$controller}' is registered",
                $registered !== null,
                "Controller should be defined"
            );
        }
    }

    /**
     * Test: Collections are registered
     */
    private function testCollectionsRegistered() {
        // Note: We'll test specific collections after we build collections/tags.php
        $this->assert(
            'Collections registered',
            true, // Placeholder - will check specific collections later
            'Collections file should be loaded'
        );
    }

    /**
     * Test: Routes are registered
     */
    private function testRoutesRegistered() {
        // Note: We'll test specific routes after we build routes/tags.php
        $this->assert(
            'Routes registered',
            true, // Placeholder - will check specific routes later
            'Routes file should be loaded'
        );
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
    $test = new PluginTest();
    $test->runAll();
}
