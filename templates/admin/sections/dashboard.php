<?php

declare(strict_types=1);
?>
<section class="admin-grid two-thirds">
    <div class="admin-stack">
        <div class="metric-strip">
            <article class="metric-panel" data-metric="projects">
                <span>Total projects</span>
                <strong><?= (int) ($metrics['projects'] ?? 0) ?></strong>
            </article>
            <article class="metric-panel" data-metric="published_projects">
                <span>Published</span>
                <strong><?= (int) ($metrics['published_projects'] ?? 0) ?></strong>
            </article>
            <article class="metric-panel" data-metric="new_messages">
                <span>Inbox</span>
                <strong><?= (int) ($metrics['new_messages'] ?? 0) ?></strong>
            </article>
            <article class="metric-panel" data-metric="page_views">
                <span>Page views</span>
                <strong><?= (int) ($metrics['page_views'] ?? 0) ?></strong>
            </article>
            <article class="metric-panel" data-metric="unique_visitors">
                <span>Unique visitors</span>
                <strong><?= (int) ($analytics_summary['unique_visitors'] ?? 0) ?></strong>
            </article>
        </div>

        <div class="admin-card">
            <div class="admin-card-head">
                <div>
                    <h2>Traffic estimate</h2>
                    <p>First-party request-based data collected without cron jobs or third-party trackers.</p>
                </div>
                <div class="traffic-card-actions">
                    <div class="range" id="traffic-range">
                        <button data-days="7">7d</button>
                        <button data-days="30" class="active">30d</button>
                        <button data-days="90">90d</button>
                    </div>
                    <a href="<?= cv_admin_url('dashboard/export-csv?days=90') ?>" class="admin-button admin-button-secondary admin-button-small" title="Export analytics CSV">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        CSV
                    </a>
                </div>
            </div>
            <div class="chart-wrap chart-sm" id="traffic-chart-wrap">
                <canvas id="dailyTrafficChart"></canvas>
            </div>
            <div class="traffic-legend" id="traffic-legend">
                <span class="traffic-legend-item"><span class="traffic-legend-dot" style="background:#E4581F"></span>Page views</span>
                <span class="traffic-legend-item"><span class="traffic-legend-dot traffic-legend-dot--dashed"></span>Unique visitors</span>
            </div>
            <div class="empty-state" id="traffic-empty" style="display:none;margin:0 22px 22px">
                <strong>No traffic data yet</strong>
                <p>Data will appear here once visitors start accessing your site.</p>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-head">
                <h2>Recent projects</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_projects as $project): ?>
                            <tr>
                                <td><?= cv_e(cv_localized_value($project, 'title')) ?></td>
                                <td><span class="status-pill status-<?= cv_e((string) $project['status']) ?>"><?= cv_e((string) $project['status']) ?></span></td>
                                <td><?= cv_e((string) $project['updated_at']) ?></td>
                                <td><a href="<?= cv_admin_url('projects/edit/' . $project['id']) ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin-stack">
        <div class="admin-card">
            <div class="admin-card-head">
                <h2>Top viewed projects</h2>
            </div>
            <div class="mini-list">
                <?php foreach ($top_projects as $project): ?>
                    <div class="mini-list-item">
                        <div>
                            <strong><?= cv_e(cv_localized_value($project, 'title')) ?></strong>
                            <span><?= cv_e((string) $project['slug']) ?></span>
                        </div>
                        <strong><?= (int) $project['view_count'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-head">
                <h2>Recent inbox</h2>
            </div>
            <div class="mini-list">
                <?php foreach ($recent_messages as $message): ?>
                    <a class="mini-list-item" href="<?= cv_admin_url('messages/view/' . $message['id']) ?>">
                        <div>
                            <strong><?= cv_e((string) $message['name']) ?></strong>
                            <span><?= cv_e((string) $message['email']) ?></span>
                        </div>
                        <span class="status-pill status-<?= cv_e((string) $message['status']) ?>"><?= cv_e((string) $message['status']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-head">
                <h2>Traffic mix</h2>
            </div>
            <div class="mini-list">
                <div class="mini-list-item"><div><strong>Dark theme</strong></div><strong><?= (int) ($analytics_summary['dark_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Light theme</strong></div><strong><?= (int) ($analytics_summary['light_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Desktop</strong></div><strong><?= (int) ($analytics_summary['desktop_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Mobile</strong></div><strong><?= (int) ($analytics_summary['mobile_hits'] ?? 0) ?></strong></div>
                <div class="mini-list-item"><div><strong>Tablet</strong></div><strong><?= (int) ($analytics_summary['tablet_hits'] ?? 0) ?></strong></div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-head">
                <h2>Top referrers</h2>
            </div>
            <div class="mini-list">
                <?php foreach ($top_referrers as $referrer): ?>
                    <div class="mini-list-item">
                        <div>
                            <strong><?= cv_e((string) $referrer['referrer_host']) ?></strong>
                        </div>
                        <strong><?= (int) $referrer['hit_count'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
