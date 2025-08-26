<?php
// site/config/config.php
return [
    // ... your existing config ...
    
    // GitHub integration (optional)
    'github' => [
        'token' => env('GITHUB_TOKEN'),
        'repo' => env('GITHUB_REPO')
    ],
    
    // Cache settings for metrics
    'cache' => [
        'pages' => [
            'active' => true,
            'type' => 'file'
        ],
        'metrics' => [
            'active' => true,
            'type' => 'file'
        ]
    ]
];

// ===================================
// ENHANCED USAGE FOR /METRICS PAGE
// ===================================

// 1. Complete /metrics page template (site/templates/metrics.php)
/*
<?php snippet('header') ?>

<main class="metrics-page">
    <header class="metrics-header">
        <h1>Website Metrics</h1>
        <p>Comprehensive data about content, development, and site activity.</p>
    </header>

    <section class="metrics-grid">
        <!-- Activity Heatmap with full accessibility -->
        <article class="metric-card" id="activity-card">
            <h2>Development Activity</h2>
            <p>Content updates and code contributions over the past year.</p>
            
            <?php
            $heatmap = new ActivityHeatmap(
                option('github.token'),
                option('github.repo')
            );
            echo $heatmap->render();
            
            // Store metrics data for other components
            $activityMetrics = $heatmap->getMetrics();
            ?>
            
            <div class="metric-details">
                <dl>
                    <dt>Total Contributions</dt>
                    <dd><?= number_format($activityMetrics['summary']['total_contributions']) ?></dd>
                    
                    <dt>Active Days</dt>
                    <dd><?= $activityMetrics['summary']['active_days'] ?></dd>
                    
                    <dt>Average Daily</dt>
                    <dd><?= $activityMetrics['summary']['avg_contributions_per_active_day'] ?></dd>
                </dl>
            </div>
        </article>

        <!-- Placeholder for future metrics -->
        <article class="metric-card">
            <h2>Content Analytics</h2>
            <p>Word counts, reading times, and content distribution.</p>
            <!-- Content metrics will be implemented separately -->
        </article>

        <article class="metric-card">
            <h2>Link Analysis</h2>  
            <p>Top-level domains and external link patterns.</p>
            <!-- Link metrics will be implemented separately -->
        </article>

        <article class="metric-card">
            <h2>Tag Insights</h2>
            <p>Most popular tags and content categorization.</p>
            <!-- Tag metrics will be implemented separately -->
        </article>
    </section>
</main>

<script>
// Metrics interoperability system
class MetricsManager {
    constructor() {
        this.data = new Map();
        this.listeners = new Map();
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Listen for metrics data from components
        document.addEventListener('metricsDataReady', (e) => {
            this.registerMetric(e.detail.type, e.detail.data);
        });
        
        // Listen for user interactions with metrics
        document.addEventListener('metricsActivitySelect', (e) => {
            this.handleActivitySelection(e.detail);
        });
    }
    
    registerMetric(type, data) {
        this.data.set(type, data);
        console.log(`Metrics registered: ${type}`, data.summary);
        
        // Notify other components that new data is available
        this.notifyListeners(type, data);
    }
    
    handleActivitySelection(detail) {
        // Cross-reference with other metrics when available
        const activityData = this.data.get('activity_heatmap');
        
        // Example: Show detailed breakdown for selected day
        this.showDayDetails(detail);
    }
    
    showDayDetails(detail) {
        // Create or update detail panel
        let detailPanel = document.getElementById('metric-detail-panel');
        if (!detailPanel) {
            detailPanel = document.createElement('div');
            detailPanel.id = 'metric-detail-panel';
            detailPanel.className = 'metric-detail-panel';
            document.querySelector('.metrics-page').appendChild(detailPanel);
        }
        
        let content = `<h3>Activity for ${new Date(detail.date).toLocaleDateString()}</h3>`;
        content += `<p>Total contributions: ${detail.activity}</p>`;
        
        if (detail.details) {
            if (detail.details.kirby) {
                content += '<h4>CMS Activity</h4><ul>';
                if (detail.details.kirby.created) {
                    detail.details.kirby.created.forEach(item => {
                        content += `<li>Created: <a href="${item.url}">${item.title}</a> (${item.template})</li>`;
                    });
                }
                if (detail.details.kirby.modified) {
                    detail.details.kirby.modified.forEach(item => {
                        content += `<li>Modified: <a href="${item.url}">${item.title}</a> (${item.template})</li>`;
                    });
                }
                content += '</ul>';
            }
            
            if (detail.details.github) {
                content += '<h4>Code Commits</h4><ul>';
                detail.details.github.commits.forEach(commit => {
                    content += `<li>${commit.sha}: ${commit.message} (${commit.author})</li>`;
                });
                content += '</ul>';
            }
        }
        
        detailPanel.innerHTML = content;
        detailPanel.style.display = 'block';
        
        // Announce to screen readers
        detailPanel.setAttribute('aria-live', 'polite');
    }
    
    notifyListeners(type, data) {
        if (this.listeners.has(type)) {
            this.listeners.get(type).forEach(callback => callback(data));
        }
    }
    
    subscribe(type, callback) {
        if (!this.listeners.has(type)) {
            this.listeners.set(type, []);
        }
        this.listeners.get(type).push(callback);
    }
    
    // Public API for other metrics components
    getMetricData(type) {
        return this.data.get(type);
    }
    
    getAllMetrics() {
        return Object.fromEntries(this.data);
    }
    
    // Export data for external use
    exportMetrics(format = 'json') {
        const allData = this.getAllMetrics();
        
        if (format === 'csv') {
            // Convert to CSV for spreadsheet analysis
            return this.toCSV(allData);
        }
        
        return JSON.stringify(allData, null, 2);
    }
    
    toCSV(data) {
        if (!data.activity_heatmap) return '';
        
        const csv = ['Date,Activity,Source_CMS,Source_GitHub'];
        const activityData = data.activity_heatmap;
        
        Object.entries(activityData.daily_counts).forEach(([date, count]) => {
            const details = activityData.detailed_activity[date];
            const cmsActivity = details?.kirby ? 
                (details.kirby.created?.length || 0) + (details.kirby.modified?.length || 0) : 0;
            const githubActivity = details?.github?.commits?.length || 0;
            
            csv.push(`${date},${count},${cmsActivity},${githubActivity}`);
        });
        
        return csv.join('\n');
    }
}

// Initialize metrics manager
const metricsManager = new MetricsManager();

// Example: Subscribe to activity data updates
metricsManager.subscribe('activity_heatmap', (data) => {
    console.log('Activity heatmap updated:', data.summary);
    
    // Update other UI components based on activity data
    updateActivitySummary(data.summary);
});

function updateActivitySummary(summary) {
    // Update summary cards or other UI elements
    const summaryElements = document.querySelectorAll('[data-metric-summary]');
    summaryElements.forEach(el => {
        const metric = el.dataset.metricSummary;
        if (summary[metric] !== undefined) {
            el.textContent = summary[metric];
        }
    });
}
</script>

<style>
.metrics-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.metrics-header {
    text-align: center;
    margin-bottom: 3rem;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
}

.metric-card {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e1e5e9;
}

.metric-card h2 {
    margin: 0 0 0.5rem 0;
    color: #24292f;
}

.metric-card p {
    color: #656d76;
    margin-bottom: 1.5rem;
}

.metric-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e1e5e9;
}

.metric-details dl {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
    margin: 0;
}

.metric-details dt {
    font-weight: 600;
    color: #24292f;
    font-size: 0.875rem;
}

.metric-details dd {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0969da;
    margin: 0;
}

.metric-detail-panel {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    z-index: 1000;
    border: 1px solid #e1e5e9;
}

.metric-detail-panel h3 {
    margin: 0 0 1rem 0;
    color: #24292f;
}

.metric-detail-panel h4 {
    margin: 1rem 0 0.5rem 0;
    color: #656d76;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.metric-detail-panel ul {
    margin: 0;
    padding-left: 1rem;
}

.metric-detail-panel li {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.metric-detail-panel a {
    color: #0969da;
    text-decoration: none;
}

.metric-detail-panel a:hover {
    text-decoration: underline;
}

@media (prefers-color-scheme: dark) {
    .metric-card {
        background: #21262d;
        border-color: #30363d;
        color: #f0f6fc;
    }
    
    .metric-card h2 {
        color: #f0f6fc;
    }
    
    .metric-details {
        border-color: #30363d;
    }
    
    .metric-details dt {
        color: #f0f6fc;
    }
    
    .metric-detail-panel {
        background: #21262d;
        border-color: #30363d;
        color: #f0f6fc;
    }
    
    .metric-detail-panel h3 {
        color: #f0f6fc;
    }
}

@media (max-width: 768px) {
    .metrics-page {
        padding: 1rem;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .metric-details dl {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .metric-detail-panel {
        max-width: 90vw;
        margin: 1rem;
    }
}
</style>

<?php snippet('footer') ?>
*/

// ===================================
// ACCESSIBILITY HELPER FUNCTIONS
// ===================================

// 2. Accessibility testing snippet (site/snippets/a11y-test.php)
/*
<?php
// Test accessibility of metrics components
function testMetricsAccessibility() {
    $issues = [];
    
    // Check for proper ARIA labels
    $heatmap = new ActivityHeatmap();
    $html = $heatmap->render();
    
    if (strpos($html, 'aria-label') === false) {
        $issues[] = 'Missing ARIA labels on interactive elements';
    }
    
    if (strpos($html, 'role="img"') === false) {
        $issues[] = 'Missing image role for screen readers';
    }
    
    if (strpos($html, 'sr-only') === false) {
        $issues[] = 'Missing screen reader only content';
    }
    
    return $issues;
}
?>
*/

// 3. Content metrics integration example
/*
<?php
// site/classes/ContentMetrics.php
class ContentMetrics {
    public function getMetrics() {
        $pages = site()->index();
        $metrics = [
            'type' => 'content_analysis',
            'summary' => [
                'total_pages' => $pages->count(),
                'total_words' => 0,
                'avg_reading_time' => 0,
                'content_types' => []
            ],
            'by_template' => [],
            'by_date' => []
        ];
        
        foreach ($pages as $page) {
            $template = $page->template()->name();
            $wordCount = str_word_count(strip_tags($page->text()->value()));
            $readingTime = ceil($wordCount / 200); // 200 words per minute
            
            $metrics['summary']['total_words'] += $wordCount;
            $metrics['by_template'][$template] = ($metrics['by_template'][$template] ?? 0) + 1;
            
            $date = date('Y-m-d', $page->published());
            if (!isset($metrics['by_date'][$date])) {
                $metrics['by_date'][$date] = ['pages' => 0, 'words' => 0];
            }
            $metrics['by_date'][$date]['pages']++;
            $metrics['by_date'][$date]['words'] += $wordCount;
        }
        
        $metrics['summary']['avg_reading_time'] = $metrics['summary']['total_words'] > 0 
            ? ceil($metrics['summary']['total_words'] / 200) 
            : 0;
            
        return $metrics;
    }
    
    // Cross-reference with activity data
    public function correlateWithActivity($activityMetrics) {
        $contentMetrics = $this->getMetrics();
        $correlation = [];
        
        foreach ($activityMetrics['daily_counts'] as $date => $activity) {
            if (isset($contentMetrics['by_date'][$date])) {
                $correlation[$date] = [
                    'activity' => $activity,
                    'content_created' => $contentMetrics['by_date'][$date]['pages'],
                    'words_written' => $contentMetrics['by_date'][$date]['words']
                ];
            }
        }
        
        return $correlation;
    }
}
?>
*/

// 4. Export functionality for external analysis
/*
<?php
// site/templates/metrics-export.php
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="metrics-export.json"');

$heatmap = new ActivityHeatmap(option('github.token'), option('github.repo'));
$activityData = $heatmap->getMetrics();

// Add other metrics as they're implemented
$exportData = [
    'exported_at' => date('c'),
    'site_url' => site()->url(),
    'metrics' => [
        'activity_heatmap' => $activityData
        // Add content_analysis, link_analysis, tag_insights as implemented
    ]
];

echo json_encode($exportData, JSON_PRETTY_PRINT);
?>
*/

// ===================================
// INTEGRATION WITH KIRBY PANEL
// ===================================

// 5. Kirby Panel integration (site/plugins/metrics-dashboard/index.php)
/*
<?php
Kirby::plugin('yourname/metrics-dashboard', [
    'areas' => [
        'metrics' => function ($kirby) {
            return [
                'label' => 'Metrics',
                'icon' => 'chart',
                'menu' => true,
                'link' => 'metrics',
                'views' => [
                    [
                        'pattern' => 'metrics',
                        'action' => function () {
                            $heatmap = new ActivityHeatmap(
                                option('github.token'),
                                option('github.repo')
                            );
                            
                            $data = $heatmap->getMetrics();
                            
                            return [
                                'component' => 'k-metrics-view',
                                'title' => 'Site Metrics',
                                'props' => [
                                    'activity' => $data,
                                    'heatmapHtml' => $heatmap->render()
                                ]
                            ];
                        }
                    ]
                ]
            ];
        }
    ]
]);
?>
*/
?>