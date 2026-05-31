<?php

declare(strict_types=1);

$flashes = $flashes ?? cv_consume_flashes();
?>
<!doctype html>
<html lang="<?= cv_e(cv_current_locale()) ?>" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Owner Access</title>
    <meta name="robots" content="noindex,nofollow">
    <?php $brandIcon = cv_asset('assets/img/logo-fm-final.png'); ?>
    <link rel="icon" type="image/png" sizes="170x153" href="<?= cv_e($brandIcon) ?>">
    <link rel="shortcut icon" href="<?= cv_e($brandIcon) ?>">
    <link rel="apple-touch-icon" href="<?= cv_e($brandIcon) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= cv_asset('assets/css/admin.css') ?>">
    <script src="<?= cv_asset('assets/js/theme.js') ?>"></script>
</head>
<body>
    <?php require $contentTemplate; ?>
</body>
</html>
