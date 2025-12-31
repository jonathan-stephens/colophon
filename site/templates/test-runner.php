<?php
/**
 * Test Runner Template
 *
 * Create a page with this template to run all Tag Garden plugin tests.
 *
 * Setup:
 * 1. Save this as site/templates/test-runner.php
 * 2. Create site/blueprints/pages/test-runner.yml with title: Test Runner
 * 3. Create a page in your Kirby Panel using this template
 * 4. Visit the page to run tests
 */

snippet('header') ?>

<style>
    .test-container {
        max-width: 900px;
        margin: 40px auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .test-container h1 {
        border-bottom: 3px solid #333;
        padding-bottom: 10px;
    }
    .test-section {
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    code {
        background: #e9ecef;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.9em;
    }
</style>

<div class="test-container">
    <h1>🧪 Tag Garden Plugin Tests</h1>

    <div class="test-section">
        <p><strong>Test Environment:</strong></p>
        <ul>
            <li>Kirby Version: <?= kirby()->version() ?></li>
            <li>PHP Version: <?= phpversion() ?></li>
            <li>Total Pages: <?= site()->index()->count() ?></li>
            <li>Tagged Pages: <?= site()->index()->filterBy('tags', '!=', '')->count() ?></li>
        </ul>
    </div>

    <?php
    // Load and run Plugin Tests
    $pluginTestPath = kirby()->root('plugins') . '/tag-garden/tests/PluginTest.php';
    if (file_exists($pluginTestPath)) {
        require_once $pluginTestPath;
        $pluginTest = new PluginTest();
        $pluginTest->runAll();
    } else {
        echo "<p style='color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "❌ Could not find PluginTest.php at: <code>{$pluginTestPath}</code>";
        echo "</p>";
    }
    ?>

    <?php
    // Load and run Page Methods Tests
    $pageMethodsTestPath = kirby()->root('plugins') . '/tag-garden/tests/PageMethodsTest.php';
    if (file_exists($pageMethodsTestPath)) {
        require_once $pageMethodsTestPath;
        $pageMethodsTest = new PageMethodsTest();
        $pageMethodsTest->runAll($page); // Use current page or finds test page
    } else {
        echo "<p style='color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "❌ Could not find PageMethodsTest.php at: <code>{$pageMethodsTestPath}</code>";
        echo "</p>";
    }
    ?>

    <?php
    // Load and run Helper Functions Tests
    $helpersTestPath = kirby()->root('plugins') . '/tag-garden/tests/HelpersTest.php';
    if (file_exists($helpersTestPath)) {
        require_once $helpersTestPath;
        $helpersTest = new HelpersTest();
        $helpersTest->runAll();
    } else {
        echo "<p style='color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "❌ Could not find HelpersTest.php at: <code>{$helpersTestPath}</code>";
        echo "</p>";
    }
    ?>


    <div class="test-section">
        <h3>📝 Test Page Setup</h3>
        <p>For comprehensive testing, create a test page with:</p>
        <ul>
            <li>A <code>text</code> field with at least 200 words of content</li>
            <li>2-3 tags in the <code>tags</code> field</li>
            <li>Other tag-garden fields populated (growth status, dates, etc.)</li>
        </ul>
        <p>Create multiple pages with overlapping tags to test related tags/pages functionality.</p>
    </div>

    <div class="test-section">
        <h3>🔍 Manual Tests</h3>
        <p>After automated tests pass, manually verify:</p>
        <ol>
            <li><strong>Panel Fields:</strong> Create a page and check all tag-garden fields appear correctly</li>
            <li><strong>Tag Autocomplete:</strong> Start typing in tags field - should show existing tags</li>
            <li><strong>Reading Time:</strong> Check if reading time displays correctly on pages</li>
            <li><strong>Related Content:</strong> Pages with shared tags show related content</li>
        </ol>
    </div>

    <div class="test-section">
        <h3>🐛 Debugging</h3>
        <p>If tests fail, check:</p>
        <ul>
            <li>Plugin is in <code>site/plugins/tag-garden/</code></li>
            <li>All plugin files exist and have correct names</li>
            <li>PHP error logs for syntax errors</li>
            <li>Kirby debug mode is enabled in config.php</li>
        </ul>

        <?php if (option('debug')): ?>
            <p style="color: #155724; background: #d4edda; padding: 10px; border-radius: 5px;">
                ✅ Debug mode is enabled
            </p>
        <?php else: ?>
            <p style="color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px;">
                ⚠️ Debug mode is disabled. Enable it in config.php: <code>'debug' => true</code>
            </p>
        <?php endif ?>
    </div>

    <div class="test-section">
        <h3>➡️ Next Steps</h3>
        <p>Once all tests pass:</p>
        <ol>
            <li>Proceed to build <code>config/options.php</code></li>
            <li>Create test files for each new component</li>
            <li>Build collections, models, and other components</li>
            <li>Test each component before moving to the next</li>
        </ol>
    </div>

</div>


<?php snippet('footer') ?>
