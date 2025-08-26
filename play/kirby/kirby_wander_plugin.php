<?php

// site/plugins/wander/index.php

use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Http\Response;

Kirby::plugin('yourname/wander', [
    'options' => [
        'enabled' => true,
        'defaultTypes' => ['page'], // Default content types
        'excludeTemplates' => ['home', 'error'], // Templates to exclude from wandering
    ],
    
    'routes' => [
        // API route for getting random page
        [
            'pattern' => 'wander/random',
            'method' => 'GET',
            'action' => function () {
                $selectedTypes = get('types');
                $types = $selectedTypes ? explode(',', $selectedTypes) : option('yourname.wander.defaultTypes', ['page']);
                
                $pages = site()->index()->listed();
                $excludeTemplates = option('yourname.wander.excludeTemplates', ['home', 'error']);
                
                // Filter pages by selected types and exclude certain templates
                $filteredPages = $pages->filter(function ($page) use ($types, $excludeTemplates) {
                    return in_array($page->intendedTemplate()->name(), $types) && 
                           !in_array($page->intendedTemplate()->name(), $excludeTemplates);
                });
                
                if ($filteredPages->count() === 0) {
                    return Response::json(['error' => 'No pages found for wandering'], 404);
                }
                
                $randomPage = $filteredPages->shuffle()->first();
                
                return Response::json([
                    'url' => $randomPage->url(),
                    'title' => $randomPage->title()->value(),
                    'template' => $randomPage->intendedTemplate()->name()
                ]);
            }
        ],
        
        // Virtual page route
        [
            'pattern' => 'wander',
            'action' => function () {
                $page = new Page([
                    'slug' => 'wander',
                    'template' => 'wander',
                    'model' => 'wander',
                    'content' => [
                        'title' => 'Wander & Discover',
                        'description' => 'Discover random content from this website'
                    ]
                ]);
                
                return site()->visit($page);
            }
        ]
    ],
    
    'pageModels' => [
        'wander' => 'WanderPage'
    ],
    
    'templates' => [
        'wander' => __DIR__ . '/templates/wander.php'
    ],
    
    'snippets' => [
        'wander-button' => __DIR__ . '/snippets/wander-button.php'
    ],
    
    'fields' => [
        'wander-types' => __DIR__ . '/fields/wander-types.php'
    ],
    
    'api' => [
        'routes' => [
            [
                'pattern' => 'wander/types',
                'method' => 'GET',
                'action' => function () {
                    $templates = [];
                    $pages = site()->index();
                    
                    foreach ($pages as $page) {
                        $template = $page->intendedTemplate()->name();
                        if (!in_array($template, $templates)) {
                            $templates[] = $template;
                        }
                    }
                    
                    return array_values($templates);
                }
            ]
        ]
    ]
]);

// site/plugins/wander/models/WanderPage.php
class WanderPage extends Page
{
    public function getAvailableTypes()
    {
        $templates = [];
        $pages = site()->index()->listed();
        $excludeTemplates = option('yourname.wander.excludeTemplates', ['home', 'error']);
        
        foreach ($pages as $page) {
            $template = $page->intendedTemplate()->name();
            if (!in_array($template, $templates) && !in_array($template, $excludeTemplates)) {
                $templates[$template] = ucfirst($template);
            }
        }
        
        return $templates;
    }
    
    public function getEnabledTypes()
    {
        return site()->content()->get('wander_enabled_types')->split(',') ?: ['page'];
    }
}

// site/plugins/wander/templates/wander.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page->title() ?> | <?= $site->title() ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        
        .wander-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .wander-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .wander-description {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        
        .controls {
            margin-bottom: 3rem;
        }
        
        .type-selector {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 1rem;
            color: white;
            font-size: 1rem;
            margin-bottom: 2rem;
            min-width: 200px;
        }
        
        .type-selector option {
            background: #333;
            color: white;
        }
        
        .wander-button {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            border-radius: 50px;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            display: inline-block;
            min-width: 200px;
        }
        
        .wander-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        
        .wander-button:active {
            transform: translateY(0);
        }
        
        .wander-button.loading {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .home-link {
            margin-top: 2rem;
        }
        
        .home-link a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 1rem;
        }
        
        .home-link a:hover {
            color: white;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }
            
            .wander-container {
                padding: 2rem;
            }
            
            .wander-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="wander-container">
        <h1 class="wander-title">🎲 <?= $page->title() ?></h1>
        <p class="wander-description"><?= $page->description() ?></p>
        
        <div class="controls">
            <select id="typeSelector" class="type-selector" multiple>
                <?php foreach ($page->getAvailableTypes() as $template => $label): ?>
                    <option value="<?= $template ?>" 
                            <?= in_array($template, $page->getEnabledTypes()) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        
        <button id="wanderButton" class="wander-button">
            🚀 Take Me Somewhere Random!
        </button>
        
        <div class="home-link">
            <a href="<?= $site->url() ?>">← Back to Home</a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wanderButton = document.getElementById('wanderButton');
            const typeSelector = document.getElementById('typeSelector');
            
            wanderButton.addEventListener('click', function() {
                const selectedTypes = Array.from(typeSelector.selectedOptions).map(option => option.value);
                
                if (selectedTypes.length === 0) {
                    alert('Please select at least one content type!');
                    return;
                }
                
                wanderButton.textContent = '🔄 Finding something amazing...';
                wanderButton.classList.add('loading');
                wanderButton.disabled = true;
                
                const url = `<?= $site->url() ?>/wander/random?types=${selectedTypes.join(',')}`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert('No content found for the selected types!');
                            resetButton();
                        } else {
                            window.location.href = data.url;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again!');
                        resetButton();
                    });
            });
            
            function resetButton() {
                wanderButton.textContent = '🚀 Take Me Somewhere Random!';
                wanderButton.classList.remove('loading');
                wanderButton.disabled = false;
            }
        });
    </script>
</body>
</html>

<?php
// site/plugins/wander/snippets/wander-button.php
?>

<div class="wander-button-container">
    <style>
        .wander-button-container {
            margin: 1rem 0;
        }
        
        .wander-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .wander-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .wander-btn:active {
            transform: translateY(0);
        }
    </style>
    
    <button id="wanderBtn" class="wander-btn">
        🎲 Discover Something Random
    </button>
    
    <script>
        (function() {
            const wanderBtn = document.getElementById('wanderBtn');
            
            if (wanderBtn) {
                wanderBtn.addEventListener('click', function() {
                    const originalText = this.textContent;
                    this.textContent = '🔄 Wandering...';
                    this.disabled = true;
                    
                    fetch('<?= $site->url() ?>/wander/random')
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert('No content available for wandering!');
                                this.textContent = originalText;
                                this.disabled = false;
                            } else {
                                window.location.href = data.url;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Something went wrong. Please try again!');
                            this.textContent = originalText;
                            this.disabled = false;
                        });
                });
            }
        })();
    </script>
</div>

<?php
// site/plugins/wander/fields/wander-types.php

use Kirby\Cms\App as Kirby;

return [
    'props' => [
        'value' => function ($value = []) {
            return $value;
        }
    ],
    'computed' => [
        'options' => function () {
            $templates = [];
            $pages = kirby()->site()->index()->listed();
            $excludeTemplates = kirby()->option('yourname.wander.excludeTemplates', ['home', 'error']);
            
            foreach ($pages as $page) {
                $template = $page->intendedTemplate()->name();
                if (!isset($templates[$template]) && !in_array($template, $excludeTemplates)) {
                    $templates[$template] = [
                        'text' => ucfirst($template),
                        'value' => $template
                    ];
                }
            }
            
            return array_values($templates);
        }
    ]
];

// Blueprint for site.yml - Add this to your site/blueprints/site.yml
/*
title: Site
tabs:
  wander:
    label: Wander Settings
    icon: shuffle
    columns:
      main:
        width: 2/3
        sections:
          wander:
            type: fields
            fields:
              wander_enabled_types:
                label: Enabled Content Types
                type: multiselect
                options:
                  page: Page
                  article: Article
                  project: Project
                  # Add your custom template types here
                help: Select which content types should be available for random wandering
              wander_exclude_pages:
                label: Excluded Pages
                type: pages
                multiple: true
                help: Select specific pages to exclude from wandering (optional)
      sidebar:
        width: 1/3
        sections:
          info:
            type: info
            text: |
              ## Wander Plugin
              
              Configure which content types visitors can discover through the random content feature.
              
              ### Usage in Templates:
              ```php
              <?= snippet('wander-button') ?>
              ```
              
              ### Wander Page:
              Visit `/wander` to see the full interface.
*/
?>