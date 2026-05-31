<?php

declare(strict_types=1);

// Minimal admin API: overview/analytics
$root = dirname(__DIR__, 2);
chdir($root);
require $root . '/app/bootstrap.php';

if (!cv_is_admin_authenticated()) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$range = (int) cv_get('range', 30);

$summary = cv_analytics_summary();
$daily = cv_analytics_daily_series($range);
$topProjects = cv_top_projects(10);
$topReferrers = cv_top_referrers(10);

$msgCounts = [
    'all' => cv_count_rows('contact_messages'),
    'new' => cv_count_rows('contact_messages', 'status = "new"'),
    'replied' => cv_count_rows('contact_messages', 'status = "replied"'),
    'archived' => cv_count_rows('contact_messages', 'status = "archived"'),
];

cv_json_response([
    'ok' => true,
    'summary' => $summary,
    'daily' => $daily,
    'top_projects' => $topProjects,
    'top_referrers' => $topReferrers,
    'message_counts' => $msgCounts,
]);
