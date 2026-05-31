<?php

declare(strict_types=1);

function cv_request_segments(): array
{
    $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
    $basePath = cv_base_path();

    if ($basePath !== '' && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath)) ?: '/';
    }

    $trimmed = trim($path, '/');
    return $trimmed === '' ? [] : array_values(array_filter(explode('/', $trimmed), 'strlen'));
}

function cv_render_setup_notice(): void
{
    $installMode = cv_boolean(cv_config('app.install_mode', false));
    $dbReady = cv_database_ready();
    $title = $installMode ? 'Setup Required' : 'Database Setup Required';
    $summary = $installMode
        ? 'Create <code>config/config.php</code>, then import <code>storage/schema.sql</code> and optionally <code>storage/seed_demo.sql</code>.'
        : 'The config file exists, but the site could not connect to the database or the schema is not ready.';
    $details = $installMode
        ? [
            'Copy <code>config/config.example.php</code> to <code>config/config.php</code>.',
            'Fill in the real PostgreSQL/Neon database name and password.',
            'Import <code>storage/schema.sql</code> in the PostgreSQL SQL console.',
            'Import <code>storage/seed_demo.sql</code> if you want starter content.',
        ]
        : [
            'Check <code>db.host</code>, <code>db.database</code>, <code>db.username</code>, and <code>db.password</code> in <code>config/config.php</code>.',
            'Make sure the PostgreSQL/Neon database exists and the schema from <code>storage/schema.sql</code> was imported.',
            'If you still use placeholder values like <code>replace-with-real-db-password</code>, replace them first.',
        ];

    http_response_code(503);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . cv_e($title) . '</title>';
    echo '<style>body{font-family:Arial,sans-serif;background:#0b1020;color:#e6edf8;padding:48px}.card{max-width:820px;margin:0 auto;background:#111932;border:1px solid #24314f;border-radius:24px;padding:32px;box-shadow:0 24px 80px rgba(0,0,0,.35)}h1{margin:0 0 18px}p,li{line-height:1.7}ul{margin:18px 0 0;padding-left:22px}code{background:#09101f;padding:2px 8px;border-radius:8px}.note{margin-top:20px;padding:14px 16px;border-radius:16px;background:#0d1527;border:1px solid #22304c;color:#a9b7d3}</style>';
    echo '</head><body><div class="card"><h1>' . cv_e($title) . '</h1><p>' . $summary . '</p><ul>';
    foreach ($details as $item) {
        echo '<li>' . $item . '</li>';
    }
    echo '</ul>';
    if (!$installMode && !$dbReady) {
        echo '<div class="note">Static assets can load, but dynamic pages stay unavailable until the database connection succeeds.</div>';
    }
    echo '</div></body></html>';
}

function cv_route_request(): void
{
    $rawSegments = cv_request_segments();
    $adminSlug = cv_admin_slug();

    if (($rawSegments[0] ?? '') === $adminSlug) {
        array_shift($rawSegments);
        cv_handle_admin_request($rawSegments);
        return;
    }

    [$locale, $segments] = cv_detect_locale($rawSegments);
    cv_set_locale($locale);

    $first = $segments[0] ?? '';

    if ($first === 'favicon.ico') {
        cv_redirect(cv_asset('assets/img/logo-fm-final.png'));
    }

    if (cv_config('app.install_mode', false) || !cv_database_ready()) {
        cv_render_setup_notice();
        return;
    }

    if ($first === 'img') {
        $mediaId = (int) cv_get('id', 0);
        $isThumb = cv_get('t', '') === '1';

        if ($mediaId <= 0) {
            http_response_code(404);
            exit;
        }

        $row = cv_fetch_one(
            'SELECT d.image_data, d.thumb_data, m.mime_type
             FROM media_data d
             JOIN media_library m ON m.id = d.id
             WHERE d.id = :id',
            ['id' => $mediaId]
        );

        if (!$row || empty($row['image_data'])) {
            http_response_code(404);
            exit;
        }

        $b64 = ($isThumb && !empty($row['thumb_data'])) ? (string) $row['thumb_data'] : (string) $row['image_data'];
        $mime = (string) ($row['mime_type'] ?? 'image/jpeg');

        // Strip data-URL prefix if somehow present in legacy records
        if (str_contains($b64, ',')) {
            $b64 = substr($b64, strpos($b64, ',') + 1);
        }

        $binary = base64_decode($b64, true);
        if ($binary === false || $binary === '') {
            http_response_code(422);
            exit;
        }

        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=31536000, immutable');
        header('Content-Length: ' . strlen($binary));
        echo $binary;
        exit;
    }

    if ($first === 'robots.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Disallow: /" . cv_admin_slug() . "/\n";
        echo 'Sitemap: ' . cv_url('sitemap.xml', null, true) . "\n";
        return;
    }

    if ($first === 'sitemap.xml') {
        header('Content-Type: application/xml; charset=utf-8');
        $projects = cv_fetch_projects(['published_only' => true]);
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        echo '<url><loc>' . cv_e(cv_url('', 'ru', true)) . '</loc></url>';
        echo '<url><loc>' . cv_e(cv_url('', 'en', true)) . '</loc></url>';
        echo '<url><loc>' . cv_e(cv_url('projects', 'ru', true)) . '</loc></url>';
        echo '<url><loc>' . cv_e(cv_url('projects', 'en', true)) . '</loc></url>';
        echo '<url><loc>' . cv_e(cv_url('blog', 'ru', true)) . '</loc></url>';
        echo '<url><loc>' . cv_e(cv_url('blog', 'en', true)) . '</loc></url>';
        foreach ($projects as $project) {
            echo '<url><loc>' . cv_e(cv_url('projects/' . $project['slug'], 'ru', true)) . '</loc></url>';
            echo '<url><loc>' . cv_e(cv_url('projects/' . $project['slug'], 'en', true)) . '</loc></url>';
        }
        foreach (cv_fetch_blog_posts(['published_only' => true]) as $post) {
            echo '<url><loc>' . cv_e(cv_url('blog/' . $post['slug'], 'ru', true)) . '</loc></url>';
            echo '<url><loc>' . cv_e(cv_url('blog/' . $post['slug'], 'en', true)) . '</loc></url>';
        }
        echo '</urlset>';
        return;
    }

    if ($first === 'theme-preference' && cv_is_post()) {
        cv_require_csrf('theme_preference');
        header('Content-Type: application/json; charset=utf-8');
        $theme = (string) cv_post('theme', cv_config('app.theme_default', 'dark'));
        cv_set_theme_cookie($theme);
        cv_track_event('theme_preference', ['theme_preference' => $theme, 'page_path' => cv_post('page', '/')]);
        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    if ($first === 'contact' && ($segments[1] ?? '') === 'submit') {
        cv_handle_contact_submit();
        return;
    }

    if ($first === 'go' && isset($segments[1])) {
        $project = cv_fetch_project_by_slug((string) $segments[1], true);
        if ($project && !empty($project['external_url'])) {
            cv_track_external_click((int) $project['id'], cv_url('go/' . $project['slug']));
            cv_redirect((string) $project['external_url']);
        }
    }

    if ($segments === []) {
        $home = cv_homepage_data($locale);
        cv_track_page_view(cv_url('', $locale), ['theme_preference' => cv_current_theme()]);
        cv_render('public/home', [
            'home' => $home,
            'site' => $home['site'],
            'social' => $home['social'],
            'meta' => cv_page_meta('home'),
            'canonical' => cv_url('', $locale, true),
        ]);
        return;
    }

    if ($first === 'projects' && !isset($segments[1])) {
        $categorySlug = (string) cv_get('category', '');
        $projectsPage = [
            'site' => cv_site_settings($locale),
            'social' => cv_social_settings($locale),
            'portfolio_intro' => cv_content_block('portfolio_intro', $locale),
            'categories' => cv_fetch_categories(true),
            'projects' => cv_fetch_projects([
                'published_only' => true,
                'category_slug' => $categorySlug !== '' ? $categorySlug : null,
            ]),
        ];
        cv_track_page_view(cv_url('projects', $locale), ['theme_preference' => cv_current_theme()]);
        cv_render('public/projects', [
            'projectsPage' => $projectsPage,
            'selectedCategory' => $categorySlug !== '' ? $categorySlug : 'all',
            'site' => $projectsPage['site'],
            'social' => $projectsPage['social'],
            'meta' => cv_page_meta('projects'),
            'canonical' => cv_url('projects', $locale, true),
        ]);
        return;
    }

    if ($first === 'projects' && isset($segments[1])) {
        $project = cv_fetch_project_by_slug((string) $segments[1], false);
        if ($project) {
            $pageData = cv_project_page_data($project, $locale);
            cv_track_project_view((int) $project['id'], cv_url('projects/' . $project['slug'], $locale));
            cv_render('public/project', array_merge($pageData, [
                'site' => $pageData['site'],
                'social' => $pageData['social'],
                'meta' => cv_page_meta('project', ['project' => $project]),
                'canonical' => cv_url('projects/' . $project['slug'], $locale, true),
            ]));
            return;
        }
    }

    if ($first === 'blog' && !isset($segments[1])) {
        $blogPage = [
            'site' => cv_site_settings($locale),
            'social' => cv_social_settings($locale),
            'blog_intro' => cv_content_block('blog_intro', $locale),
            'posts' => cv_fetch_blog_posts(['published_only' => true]),
        ];
        cv_track_page_view(cv_url('blog', $locale), ['theme_preference' => cv_current_theme()]);
        cv_render('public/blog', [
            'blogPage' => $blogPage,
            'site' => $blogPage['site'],
            'social' => $blogPage['social'],
            'meta' => cv_page_meta('blog'),
            'canonical' => cv_url('blog', $locale, true),
        ]);
        return;
    }

    if ($first === 'blog' && isset($segments[1])) {
        $post = cv_fetch_blog_post_by_slug((string) $segments[1], false);
        if ($post) {
            cv_track_page_view(cv_url('blog/' . $post['slug'], $locale), ['theme_preference' => cv_current_theme()]);
            cv_render('public/blog-post', [
                'post' => $post,
                'site' => cv_site_settings($locale),
                'social' => cv_social_settings($locale),
                'meta' => cv_page_meta('blog_post', ['post' => $post]),
                'canonical' => cv_url('blog/' . $post['slug'], $locale, true),
            ]);
            return;
        }
    }

    cv_render_public_404($locale);
}

function cv_handle_admin_request(array $segments): void
{
    cv_send_admin_cache_headers();

    if (cv_config('app.install_mode', false) || !cv_database_ready()) {
        cv_render_setup_notice();
        return;
    }

    $route = $segments[0] ?? 'dashboard';

    if ($route === 'logout') {
        cv_admin_logout();
        cv_flash('success', cv_t('messages.logout_success'));
        cv_redirect(cv_admin_url('login'));
    }

    if ($route === 'login') {
        if (cv_is_admin_authenticated()) {
            cv_redirect(cv_admin_url('dashboard'));
        }

        if (cv_is_post()) {
            cv_require_csrf('admin_login');
            $result = cv_admin_login((string) cv_post('identity', ''), (string) cv_post('password', ''));
            cv_flash($result['ok'] ? 'success' : 'error', (string) $result['message']);

            if ($result['ok']) {
                cv_redirect(cv_admin_url('dashboard'));
            }
        }

        cv_render('admin/login', ['flashes' => cv_consume_flashes()], 'admin/auth');
        return;
    }

    cv_require_admin_auth();
    $context = cv_admin_route_context($segments);

    if ($context['section'] === 'backup' && $context['action'] === 'export-csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="contact-messages.csv"');
        echo cv_export_messages_csv();
        return;
    }

    if ($context['section'] === 'dashboard' && $context['action'] === 'export-csv') {
        $days = (int) cv_get('days', 90);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="analytics-' . date('Y-m-d') . '.csv"');
        echo cv_export_analytics_csv($days);
        return;
    }

    // Gallery image delete
    if ($context['section'] === 'projects' && $context['action'] === 'gallery-delete' && $context['id'] > 0 && cv_is_post()) {
        cv_require_csrf('admin');
        $img = cv_find_by_id('project_images', $context['id']);
        if ($img) {
            cv_execute('DELETE FROM project_images WHERE id = :id', ['id' => $context['id']]);
            cv_cache_flush();
            cv_flash('success', 'Gallery image removed.');
            cv_redirect(cv_admin_url('projects/edit/' . (int) ($img['project_id'] ?? 0)));
        }
        cv_redirect(cv_admin_url('projects'));
    }

    cv_admin_handle_post($context);

    if ($context['section'] === 'messages' && $context['action'] === 'view' && $context['id'] > 0) {
        $message = cv_find_by_id('contact_messages', $context['id']);
        if ($message && (int) ($message['is_read'] ?? 0) === 0) {
            cv_admin_mark_message($context['id'], (string) ($message['status'] ?? 'new'), true);
        }
    }

    $data = cv_admin_section_data($context);
    cv_render('admin/index', $data, 'admin/layout');
}

function cv_render_public_404(string $locale): void
{
    http_response_code(404);
    cv_render('public/404', [
        'site' => cv_site_settings($locale),
        'social' => cv_social_settings($locale),
        'meta' => array_merge(cv_page_meta('home'), [
            'title' => '404 | ' . (cv_site_settings($locale)['site_name'] ?? 'Portfolio'),
            'description' => 'The requested page was not found.',
            'robots' => 'noindex,nofollow',
        ]),
        'canonical' => cv_current_url(),
    ]);
}
