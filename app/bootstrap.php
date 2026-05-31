<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$GLOBALS['cv_root'] = $root;

$exampleConfig = require $root . '/config/config.example.php';
$configPath = $root . '/config/config.php';
$userConfig = is_file($configPath) ? require $configPath : [];
$config = array_replace_recursive($exampleConfig, is_array($userConfig) ? $userConfig : []);
$config['app']['install_mode'] = !is_file($configPath);
$GLOBALS['cv_config'] = $config;

date_default_timezone_set((string) ($config['app']['timezone'] ?? 'UTC'));
$debugEnabled = filter_var($config['app']['debug'] ?? false, FILTER_VALIDATE_BOOL);
error_reporting(E_ALL);
ini_set('display_errors', $debugEnabled ? '1' : '0');
ini_set('display_startup_errors', $debugEnabled ? '1' : '0');
ini_set('log_errors', '1');

$files = [
    'helpers.php',
    'cache.php',
    'db.php',
    'localization.php',
    'security.php',
    'auth.php',
    'content.php',
    'analytics.php',
    'uploads.php',
    'cloudinary.php',
    'mailer.php',
    'telegram.php',
    'seo.php',
    'contact.php',
    'public_site.php',
    'export.php',
    'admin_crud.php',
    'admin.php',
    'router.php',
];

foreach ($files as $file) {
    $path = $root . '/app/' . $file;
    if (is_file($path)) {
        require_once $path;
    }
}

cv_ensure_directory(cv_root('storage'));
cv_ensure_directory(cv_root('storage/logs'));
cv_ensure_directory(cv_root('storage/backups'));
cv_ensure_directory(cv_root('storage/exports'));
cv_ensure_directory(cv_root('storage/tmp'));
cv_ensure_directory(cv_root('uploads'));
cv_ensure_directory(cv_root('uploads/images'));
cv_ensure_directory(cv_root('uploads/thumbs'));
cv_ensure_directory(cv_root('uploads/videos'));

cv_register_pg_session_handler();
cv_boot_session();
cv_set_locale(cv_default_locale());
cv_send_security_headers();

// Gzip output compression for HTML/JSON responses
if (!headers_sent() && extension_loaded('zlib')) {
    $zlibVal = ini_get('zlib.output_compression');
    if ($zlibVal === '' || $zlibVal === '0' || $zlibVal === false) {
        ini_set('zlib.output_compression', '4096');
    }
}
