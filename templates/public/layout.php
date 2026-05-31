<?php

declare(strict_types=1);

$meta = $meta ?? cv_page_meta('home');
$site = $site ?? cv_site_settings();
$social = $social ?? cv_social_settings();
$canonical = $canonical ?? cv_current_url();
$pageTitle = $meta['title'] ?? ($site['site_name'] ?? 'Portfolio');
$pageDescription = $meta['description'] ?? ($site['site_tagline'] ?? '');
$metaImage = $meta['image'] ?? 'assets/img/og-cover.svg';
$brandIcon = cv_asset('assets/img/logo-fm-final.png');
$metaImageUrl = str_starts_with((string) $metaImage, 'http')
    ? (string) $metaImage
    : rtrim((string) cv_config('app.url'), '/') . cv_base_path() . '/' . ltrim((string) $metaImage, '/');
$metaType = $meta['type'] ?? 'website';
$robots = $meta['robots'] ?? 'index,follow';
$isHome = isset($contentTemplate) && basename((string) $contentTemplate) === 'home.php';
?>
<!doctype html>
<html lang="<?= cv_e(cv_current_locale()) ?>" data-locale="<?= cv_e(cv_current_locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= cv_e($pageTitle) ?></title>
    <meta name="description" content="<?= cv_e($pageDescription) ?>">
    <meta name="robots" content="<?= cv_e($robots) ?>">
    <link rel="canonical" href="<?= cv_e($canonical) ?>">
    <meta property="og:type" content="<?= cv_e($metaType) ?>">
    <meta property="og:title" content="<?= cv_e($pageTitle) ?>">
    <meta property="og:description" content="<?= cv_e($pageDescription) ?>">
    <meta property="og:url" content="<?= cv_e($canonical) ?>">
    <meta property="og:image" content="<?= cv_e($metaImageUrl) ?>">
    <meta name="twitter:card" content="<?= cv_e(cv_seo_settings()['twitter_card'] ?? 'summary_large_image') ?>">
    <meta name="twitter:title" content="<?= cv_e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= cv_e($pageDescription) ?>">
    <meta name="twitter:image" content="<?= cv_e($metaImageUrl) ?>">
    <meta name="theme-color" content="#F7F4EF">
    <link rel="icon" type="image/png" sizes="170x153" href="<?= cv_e($brandIcon) ?>">
    <link rel="shortcut icon" href="<?= cv_e($brandIcon) ?>">
    <link rel="apple-touch-icon" href="<?= cv_e($brandIcon) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"></noscript>
    <link rel="stylesheet" href="<?= cv_asset('assets/css/public.css') ?>">
    <script src="<?= cv_asset('assets/js/theme.js') ?>"></script>
</head>
<body class="locale-<?= cv_e(cv_current_locale()) ?>">
<div class="bg-stage" aria-hidden="true">
    <div class="bg-grid"></div>
    <div class="bg-blob b1"></div>
    <div class="bg-blob b2"></div>
    <div class="bg-blob b3"></div>
</div>
<?php cv_partial('public/partials/header', ['site' => $site, 'social' => $social, 'is_home' => $isHome]); ?>
<main id="top">
    <?php require $contentTemplate; ?>
</main>
<?php cv_partial('public/partials/footer', ['site' => $site, 'social' => $social]); ?>
<script>
    window.CVF = {
        baseUrl: <?= json_encode(cv_base_path(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        locale: <?= json_encode(cv_current_locale(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        themeEndpoint: <?= json_encode(cv_url('theme-preference'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        themeToken: <?= json_encode(cv_csrf_token('theme_preference'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
    };
</script>
<script src="<?= cv_asset('assets/js/public.js') ?>" defer></script>
</body>
</html>
