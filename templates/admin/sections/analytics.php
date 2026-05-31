<?php

declare(strict_types=1);
?>
<section class="admin-stack">
    <div class="metric-strip">
        <article class="metric-panel"><span>Page views</span><strong><?= (int) ($analytics_summary['page_views'] ?? 0) ?></strong></article>
        <article class="metric-panel"><span>Unique visitors</span><strong><?= (int) ($analytics_summary['unique_visitors'] ?? 0) ?></strong></article>
        <article class="metric-panel"><span>Project views</span><strong><?= (int) ($analytics_summary['project_views'] ?? 0) ?></strong></article>
        <article class="metric-panel"><span>External clicks</span><strong><?= (int) ($analytics_summary['external_clicks'] ?? 0) ?></strong></article>
        <article class="metric-panel"><span>Contact submissions</span><strong><?= (int) ($analytics_summary['contact_submissions'] ?? 0) ?></strong></article>
    </div>
    <div class="admin-card">
        <div class="admin-card-head"><h2>Daily visits</h2></div>
        <canvas id="analyticsChart" height="120"></canvas>
    </div>
    <div class="admin-card">
        <div class="admin-card-head"><h2>Top viewed projects</h2></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Project</th><th>Slug</th><th>Views</th></tr></thead>
                <tbody>
                    <?php foreach ($top_projects as $project): ?>
                        <tr>
                            <td><?= cv_e(cv_localized_value($project, 'title')) ?></td>
                            <td><?= cv_e((string) $project['slug']) ?></td>
                            <td><?= (int) $project['view_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-grid two-thirds">
        <div class="admin-card">
            <div class="admin-card-head"><h2>Theme and device distribution</h2></div>
            <div class="mini-list">
                <div class="mini-list-item"><div><strong>Dark theme</strong></div><strong><?= (int) ($analytics_summary['dark_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Light theme</strong></div><strong><?= (int) ($analytics_summary['light_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Desktop</strong></div><strong><?= (int) ($analytics_summary['desktop_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Mobile</strong></div><strong><?= (int) ($analytics_summary['mobile_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Tablet</strong></div><strong><?= (int) ($analytics_summary['tablet_hits'] ?? 0) ?></strong></div>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-head"><h2>Top referrers</h2></div>
            <div class="mini-list">
                <?php foreach ($top_referrers as $referrer): ?>
                    <div class="mini-list-item">
                        <div><strong><?= cv_e((string) $referrer['referrer_host']) ?></strong></div>
                        <strong><?= (int) $referrer['hit_count'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
