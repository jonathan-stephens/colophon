<?php
// site/templates/metrics.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Metrics - <?= $site->title() ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        .metric-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .metric-card h3 {
            color: #495057;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-label {
            font-weight: 500;
            color: #6c757d;
        }

        .stat-value {
            font-weight: 700;
            color: #495057;
            font-size: 1.1rem;
        }

        .chart-container {
            margin-top: 1rem;
            height: 300px;
        }

        .freshness-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .fresh { background-color: #28a745; }
        .moderate { background-color: #ffc107; }
        .stale { background-color: #dc3545; }

        .summary-stats {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .summary-item {
            text-align: center;
            padding: 1rem;
            background: white;
            border-radius: 6px;
        }

        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            display: block;
        }

        .summary-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Content Metrics</h1>
            <p>Comprehensive analytics for <?= $site->title() ?></p>
            <p style="font-size: 0.9rem; margin-top: 1rem;">Last updated: <?= date('F j, Y \a\t g:i A') ?></p>
        </div>

        <!-- Summary Statistics -->
        <div style="padding: 2rem;">
            <div class="summary-stats">
                <h2 style="margin-bottom: 1.5rem; color: #495057;">📈 Overview</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-number"><?= $totalPages ?></span>
                        <div class="summary-label">Total Pages</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= number_format($totalWords) ?></span>
                        <div class="summary-label">Total Words</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= number_format($totalCharacters) ?></span>
                        <div class="summary-label">Total Characters</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= $totalReadingTime ?></span>
                        <div class="summary-label">Total Reading Time (min)</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= $contentTypes ?></span>
                        <div class="summary-label">Content Types</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= $averageWordsPerPage ?></span>
                        <div class="summary-label">Avg Words/Page</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= number_format($totalImages) ?></span>
                        <div class="summary-label">Total Images</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= $averageImagesPerPage ?></span>
                        <div class="summary-label">Avg Images/Page</div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-number"><?= $engagementScore ?>%</span>
                        <div class="summary-label">Engagement Score</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="metrics-grid">
            <!-- Content Type Breakdown -->
            <div class="metric-card">
                <h3>📝 Posts by Content Type</h3>
                <?php foreach ($contentTypeStats as $type => $stats): ?>
                <div class="stat-row">
                    <span class="stat-label"><?= ucfirst($type) ?></span>
                    <span class="stat-value"><?= $stats['count'] ?> posts</span>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Character Counts -->
            <div class="metric-card">
                <h3>🔤 Characters by Content Type</h3>
                <?php foreach ($contentTypeStats as $type => $stats): ?>
                <div class="stat-row">
                    <span class="stat-label"><?= ucfirst($type) ?></span>
                    <span class="stat-value"><?= number_format($stats['characters']) ?></span>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Word Counts -->
            <div class="metric-card">
                <h3>📖 Words by Content Type</h3>
                <?php foreach ($contentTypeStats as $type => $stats): ?>
                <div class="stat-row">
                    <span class="stat-label"><?= ucfirst($type) ?></span>
                    <span class="stat-value"><?= number_format($stats['words']) ?></span>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Reading Time -->
            <div class="metric-card">
                <h3>⏱️ Reading Time by Content Type</h3>
                <?php foreach ($contentTypeStats as $type => $stats): ?>
                <div class="stat-row">
                    <span class="stat-label"><?= ucfirst($type) ?></span>
                    <span class="stat-value"><?= $stats['readingTime'] ?> min</span>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Content Freshness -->
            <div class="metric-card">
                <h3>🌱 Content Freshness</h3>
                <?php foreach ($freshnessStats as $type => $freshness): ?>
                <div class="stat-row">
                    <span class="stat-label">
                        <span class="freshness-indicator <?= $freshness['class'] ?>"></span>
                        <?= ucfirst($type) ?>
                    </span>
                    <span class="stat-value"><?= $freshness['avgDays'] ?> days avg</span>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Statistical Summary -->
            <div class="metric-card">
                <h3>📊 Word Count Statistics</h3>
                <?php foreach ($contentTypeStats as $type => $stats): ?>
                    <?php if ($stats['count'] > 0): ?>
                    <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f8f9fa;">
                        <h4 style="color: #667eea; margin-bottom: 0.5rem;"><?= ucfirst($type) ?></h4>
                        <div class="stat-row">
                            <span class="stat-label">Min Words</span>
                            <span class="stat-value"><?= $stats['minWords'] ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Average Words</span>
                            <span class="stat-value"><?= $stats['avgWords'] ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Median Words</span>
                            <span class="stat-value"><?= $stats['medianWords'] ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Max Words</span>
                            <span class="stat-value"><?= $stats['maxWords'] ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">SEO Score</span>
                            <span class="stat-value"><?= $stats['avgSeoScore'] ?>/100</span>
                        </div>
                    </div>
                    <?php endif ?>
                <?php endforeach ?>
            </div>

            <!-- Content Quality Insights -->
            <div class="metric-card">
                <h3>🎯 Content Quality</h3>
                <div class="stat-row">
                    <span class="stat-label">Short Content (&lt;300 words)</span>
                    <span class="stat-value"><?= $shortContentPages ?> pages</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Medium Content (300-1500 words)</span>
                    <span class="stat-value"><?= $mediumContentPages ?> pages</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Long Content (&gt;1500 words)</span>
                    <span class="stat-value"><?= $longContentPages ?> pages</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Pages Without Images</span>
                    <span class="stat-value"><?= $pagesWithoutImages ?> pages</span>
                </div>
            </div>

            <!-- Author Statistics -->
            <?php if (!empty($authorStats)): ?>
            <div class="metric-card">
                <h3>👥 Top Authors</h3>
                <?php foreach (array_slice($authorStats, 0, 5, true) as $author => $stats): ?>
                <div class="stat-row">
                    <span class="stat-label"><?= $author ?></span>
                    <span class="stat-value"><?= $stats['count'] ?> posts</span>
                </div>
                <?php endforeach ?>
            </div>
            <?php endif ?>

            <!-- Popular Tags -->
            <?php if (!empty($tagStats)): ?>
            <div class="metric-card">
                <h3>🏷️ Popular Tags</h3>
                <?php foreach (array_slice($tagStats, 0, 10, true) as $tag => $count): ?>
                <div class="stat-row">
                    <span class="stat-label"><?= $tag ?></span>
                    <span class="stat-value"><?= $count ?> uses</span>
                </div>
                <?php endforeach ?>
            </div>
            <?php endif ?>
        </div>

        <!-- Charts Section -->
        <div style="padding: 2rem;">
            <h2 style="margin-bottom: 2rem; color: #495057;">📈 Visual Analytics</h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                <!-- Content Type Distribution -->
                <div class="metric-card">
                    <h3>Content Distribution</h3>
                    <div class="chart-container">
                        <canvas id="contentTypeChart"></canvas>
                    </div>
                </div>

                <!-- Word Count Distribution -->
                <div class="metric-card">
                    <h3>Word Count Distribution</h3>
                    <div class="chart-container">
                        <canvas id="wordCountChart"></canvas>
                    </div>
                </div>

                <!-- Publishing Timeline -->
                <div class="metric-card" style="grid-column: 1 / -1;">
                    <h3>Publishing Timeline (Last 12 Months)</h3>
                    <div class="chart-container">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Content Type Pie Chart
        const contentTypeCtx = document.getElementById('contentTypeChart').getContext('2d');
        new Chart(contentTypeCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($contentTypeStats)) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($contentTypeStats, 'count')) ?>,
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f093fb', '#f5576c',
                        '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
                        '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Word Count Bar Chart
        const wordCountCtx = document.getElementById('wordCountChart').getContext('2d');
        new Chart(wordCountCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($contentTypeStats)) ?>,
                datasets: [{
                    label: 'Total Words',
                    data: <?= json_encode(array_column($contentTypeStats, 'words')) ?>,
                    backgroundColor: '#667eea',
                    borderColor: '#764ba2',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Timeline Chart
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_keys($timelineData)) ?>,
                datasets: [{
                    label: 'Pages Published',
                    data: <?= json_encode(array_values($timelineData)) ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
