<div class="theme-switcher" data-timekeeper>
    <select class="theme-select" onchange="switchTheme(this.value)">
        <option value="">Auto (Based on Time)</option>
        <?php foreach (TimeKeeper::getPeriodData() as $period => $times): ?>
        <option value="<?= $period ?>" <?= TimeTheme::getCurrentTheme() === $period ? 'selected' : '' ?>>
            <?= ucwords(str_replace('-', ' ', $period)) ?>
        </option>
        <?php endforeach ?>
    </select>
</div>

<script>
function switchTheme(theme) {
    fetch('/api/timekeeper/switch-theme', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ theme: theme })
    })
    .then(response => response.json())
    .then(() => {
        if (!theme) {
            // If auto mode, remove cookie and reload
            document.cookie = 'timekeeper-theme=; Max-Age=0; path=/;';
        }
        location.reload();
    });
}

// Optional: Auto-update theme without page reload
setInterval(() => {
    if (!document.cookie.includes('timekeeper-theme')) {
        fetch('/api/timekeeper/current-theme')
            .then(response => response.json())
            .then(data => {
                if (data.theme !== currentTheme) {
                    location.reload();
                }
            });
    }
}, 60000); // Check every minute
</script>
