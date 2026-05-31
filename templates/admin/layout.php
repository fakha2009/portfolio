<?php

declare(strict_types=1);

$needsCharts = in_array((string) ($section ?? ''), ['dashboard', 'analytics'], true);
?>
<!doctype html>
<html lang="<?= cv_e(cv_current_locale()) ?>" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= cv_e(($sections[$section]['label'] ?? 'Admin') . ' · FM Control Panel') ?></title>
    <meta name="robots" content="noindex,nofollow">
    <?php $brandIcon = cv_asset('assets/img/logo-fm-final.png'); ?>
    <link rel="icon" type="image/png" href="<?= cv_e($brandIcon) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= cv_asset('assets/css/admin.css') ?>">
    <script src="<?= cv_asset('assets/js/theme.js') ?>"></script>
</head>
<body>
<div class="app" id="app">
    <?php cv_partial('admin/partials/sidebar', get_defined_vars()); ?>
    <div class="scrim" id="scrim"></div>
    <div class="main">
        <?php cv_partial('admin/partials/topbar', get_defined_vars()); ?>
        <div class="admin-content" data-admin-content>
            <?php cv_partial('admin/partials/flash-stack', get_defined_vars()); ?>
            <?php require $contentTemplate; ?>
        </div>
    </div>
</div>
<div class="toast-stack" id="toast-stack"></div>
<?php if ($needsCharts): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js" defer></script>
<?php endif; ?>
<script>
    window.CVF = {
        baseUrl: <?= json_encode(cv_base_path(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
    };
    <?php if ($needsCharts): ?>
    window.CVF_ADMIN = {
        charts: {
            dailySeries: <?= json_encode($daily_series ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            topProjects: <?= json_encode($top_projects ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        }
    };
    <?php endif; ?>
</script>
<script src="<?= cv_asset('assets/js/admin.js') ?>" defer></script>
</body>
</html>
