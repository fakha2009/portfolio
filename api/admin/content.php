<?php

declare(strict_types=1);

// Minimal admin API: content
$root = dirname(__DIR__, 2);
chdir($root);
require $root . '/app/bootstrap.php';

if (!cv_is_admin_authenticated()) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$locale = (string) cv_get('locale', cv_current_locale());

$content = [
    'site' => cv_site_settings($locale),
    'social' => cv_social_settings($locale),
    'hero' => cv_content_block('hero', $locale),
    'about' => cv_content_block('about', $locale),
    'contacts' => cv_content_block('contact', $locale),
    'projects' => cv_fetch_projects(['published_only' => false]),
    'posts' => cv_fetch_blog_posts(['published_only' => false]),
];

cv_json_response(['ok' => true, 'content' => $content]);
