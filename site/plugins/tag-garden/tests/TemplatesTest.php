<?php

/**
 * Templates Tests
 *
 * Basic tests to verify template files exist and are valid PHP.
 * More comprehensive testing would require rendering pages, which is
 * beyond unit testing scope.
 *
 * Usage: Include in test-runner.php template
 */

if (!function_exists('kirby')) {
    die('This test file must be run within Kirby CMS');
}

class TemplatesTest {

    private $results = [];
    private $passed = 0;
    private $failed = 0;

    /**
     * Run all template tests
     */
    public function runAll() {
        echo "<h2>🧪 Templates Tests</h2>";

        $this->testTagsTemplateExists();
        $this->testTagTemplateExists();
        $this->testTagsTemplateIsValidPHP();
        $this->testTagTemplateIsValidPHP();

        $this->displayResults();
    }

    /**
     * Test: tags.php template exists
     */
    private function testTagsTemplateExists() {
        $templatePath = kirby()->root('plugins') . '/tag-garden/templates/tags.php';

        $this->assert(
            'tags.php template file exists',
            file_exists($templatePath),
            "File not found at: {$templatePath}"
        );
    }

    /**
     * Test: tag.php template exists
     */
    private function testTagTemplateExists() {
        $templatePath = kirby()->root('plugins') . '/tag-garden/templates/tag.php';

        $this->assert(
            'tag.php template file exists',
            file_exists($templatePath),
            "File not found at: {$templatePath}"
        );
    }

    /**
     * Test: tags.php is valid PHP
     */
    private function testTagsTemplateIsValidPHP() {
        $templatePath = kirby()->root('plugins') . '/tag-garden/templates/tags.php';

        if (!file_exists($templatePath)) {
            $this->assert('tags.php is valid PHP', false, 'File not found');
            return;
        }

        // Check for PHP syntax errors
        $output = [];
        $returnVar = 0;
        exec("php -l " . escapeshellarg($templatePath) . " 2>&1", $output, $returnVar);

        $isValid = $returnVar === 0;

        $this->assert(
            'tags.php has valid PHP syntax',
            $isValid,
            $isValid ? '' : implode("\n", $output)
        );

        // Check for required template variables usage
        $content = file_get_contents($templatePath);

        $this->assert(
            'tags.php uses $tags variable',
            strpos($content, '$tags') !== false || strpos($content, '$sortedTags') !== false,
            'Template should reference tag data'
        );

        $this->assert(
            'tags.php includes header snippet',
            strpos($content, 'snippet(\'site-header\')') !== false,
            'Template should include header'
        );

        $this->assert(
            'tags.php includes footer snippet',
            strpos($content, 'snippet(\'site-footer\')') !== false,
            'Template should include footer'
        );
    }

    /**
     * Test: tag.php is valid PHP
     */
    private function testTagTemplateIsValidPHP() {
        $templatePath = kirby()->root('plugins') . '/tag-garden/templates/tag.php';

        if (!file_exists($templatePath)) {
            $this->assert('tag.php is valid PHP', false, 'File not found');
            return;
        }

        // Check for PHP syntax errors
        $output = [];
        $returnVar = 0;
        exec("php -l " . escapeshellarg($templatePath) . " 2>&1", $output, $returnVar);

        $isValid = $returnVar === 0;

        $this->assert(
            'tag.php has valid PHP syntax',
            $isValid,
            $isValid ? '' : implode("\n", $output)
        );

        // Check for required template variables usage
        $content = file_get_contents($templatePath);

        $this->assert(
            'tag.php uses $filterTags variable',
            strpos($content, '$filterTags') !== false,
            'Template should reference filter tags'
        );

        $this->assert(
            'tag.php uses $pages variable',
            strpos($content, '$pages') !== false,
            'Template should reference pages collection'
        );

        $this->assert(
            'tag.php includes header snippet',
            strpos($content, 'snippet(\'site-header\')') !== false,
            'Template should include header'
        );

        $this->assert(
            'tag.php includes footer snippet',
            strpos($content, 'snippet(\'site-footer\')') !== false,
            'Template should include footer'
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

// Auto-run if accessed directly
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    $test = new TemplatesTest();
    $test->runAll();
}
