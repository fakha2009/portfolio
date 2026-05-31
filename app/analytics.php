<?php

declare(strict_types=1);

function cv_visitor_token(): string
{
    $cookieName = (string) cv_config('app.analytics_cookie', 'cvf_vid');
    $token = trim((string) ($_COOKIE[$cookieName] ?? ''));

    if ($token === '') {
        $token = cv_random_string(16);
        setcookie($cookieName, $token, [
            'expires' => time() + (86400 * 365),
            'path' => '/',
            'secure' => cv_is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[$cookieName] = $token;
    }

    return $token;
}

function cv_current_theme(): string
{
    $theme = $_COOKIE['cv_theme'] ?? cv_config('app.theme_default', 'dark');
    return in_array($theme, ['light', 'dark'], true) ? $theme : 'dark';
}

function cv_set_theme_cookie(string $theme): void
{
    $theme = in_array($theme, ['light', 'dark'], true) ? $theme : (string) cv_config('app.theme_default', 'dark');
    setcookie('cv_theme', $theme, [
        'expires' => time() + (86400 * 365),
        'path' => '/',
        'secure' => cv_is_https(),
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
    $_COOKIE['cv_theme'] = $theme;
}

function cv_track_event(string $eventType, array $data = []): void
{
    if (!cv_database_ready()) {
        return;
    }

    $eventDate = date('Y-m-d');
    $visitorToken = cv_visitor_token();
    $deviceType = cv_device_type();
    $theme = $data['theme_preference'] ?? cv_current_theme();
    $pagePath = $data['page_path'] ?? (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
    $projectId = $data['project_id'] ?? null;
    $referrerHost = '';

    if (!empty($_SERVER['HTTP_REFERER'])) {
        $referrerHost = (string) parse_url((string) $_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    }

    $isUniqueVisitor = false;

    if ($eventType === 'page_view') {
        $existing = cv_fetch_one(
            "SELECT id FROM analytics_events WHERE event_date = :event_date AND visitor_token = :visitor_token AND event_type = 'page_view' LIMIT 1",
            [
                'event_date' => $eventDate,
                'visitor_token' => $visitorToken,
            ]
        );
        $isUniqueVisitor = $existing === null;
    }

    cv_execute(
        'INSERT INTO analytics_events (event_date, visitor_token, event_type, page_path, project_id, referrer_host, device_type, theme_preference, metadata_json, created_at)
         VALUES (:event_date, :visitor_token, :event_type, :page_path, :project_id, :referrer_host, :device_type, :theme_preference, :metadata_json, :created_at)',
        [
            'event_date' => $eventDate,
            'visitor_token' => $visitorToken,
            'event_type' => $eventType,
            'page_path' => $pagePath,
            'project_id' => $projectId,
            'referrer_host' => $referrerHost,
            'device_type' => $deviceType,
            'theme_preference' => $theme,
            'metadata_json' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => cv_now(),
        ]
    );

    cv_execute(
        'INSERT INTO analytics_daily
            (event_date, page_views, unique_visitors, project_views, external_clicks, contact_submissions, mobile_visits, tablet_visits, desktop_visits, dark_theme_hits, light_theme_hits, updated_at)
         VALUES
            (:event_date, :page_views, :unique_visitors, :project_views, :external_clicks, :contact_submissions, :mobile_visits, :tablet_visits, :desktop_visits, :dark_theme_hits, :light_theme_hits, :updated_at)
         ON CONFLICT (event_date) DO UPDATE SET
            page_views          = analytics_daily.page_views          + EXCLUDED.page_views,
            unique_visitors     = analytics_daily.unique_visitors     + EXCLUDED.unique_visitors,
            project_views       = analytics_daily.project_views       + EXCLUDED.project_views,
            external_clicks     = analytics_daily.external_clicks     + EXCLUDED.external_clicks,
            contact_submissions = analytics_daily.contact_submissions + EXCLUDED.contact_submissions,
            mobile_visits       = analytics_daily.mobile_visits       + EXCLUDED.mobile_visits,
            tablet_visits       = analytics_daily.tablet_visits       + EXCLUDED.tablet_visits,
            desktop_visits      = analytics_daily.desktop_visits      + EXCLUDED.desktop_visits,
            dark_theme_hits     = analytics_daily.dark_theme_hits     + EXCLUDED.dark_theme_hits,
            light_theme_hits    = analytics_daily.light_theme_hits    + EXCLUDED.light_theme_hits,
            updated_at          = EXCLUDED.updated_at',
        [
            'event_date' => $eventDate,
            'page_views' => $eventType === 'page_view' ? 1 : 0,
            'unique_visitors' => $isUniqueVisitor ? 1 : 0,
            'project_views' => $eventType === 'project_view' ? 1 : 0,
            'external_clicks' => $eventType === 'external_click' ? 1 : 0,
            'contact_submissions' => $eventType === 'contact_submit' ? 1 : 0,
            'mobile_visits' => $deviceType === 'mobile' ? 1 : 0,
            'tablet_visits' => $deviceType === 'tablet' ? 1 : 0,
            'desktop_visits' => $deviceType === 'desktop' ? 1 : 0,
            'dark_theme_hits' => $theme === 'dark' ? 1 : 0,
            'light_theme_hits' => $theme === 'light' ? 1 : 0,
            'updated_at' => cv_now(),
        ]
    );
}

function cv_track_page_view(string $pagePath, array $data = []): void
{
    $data['page_path'] = $pagePath;
    cv_track_event('page_view', $data);
}

function cv_track_project_view(int $projectId, string $pagePath): void
{
    cv_track_event('project_view', [
        'project_id' => $projectId,
        'page_path' => $pagePath,
    ]);
}

function cv_track_external_click(int $projectId, string $pagePath): void
{
    cv_track_event('external_click', [
        'project_id' => $projectId,
        'page_path' => $pagePath,
    ]);
}

function cv_track_contact_submit(string $pagePath): void
{
    cv_track_event('contact_submit', [
        'page_path' => $pagePath,
    ]);
}

function cv_analytics_summary(): array
{
    return cv_cache_remember('analytics:summary', 60, static function (): array {
    $themeRow = cv_fetch_one(
        "SELECT
            SUM(CASE WHEN theme_preference = 'dark'    THEN 1 ELSE 0 END) AS dark_hits,
            SUM(CASE WHEN theme_preference = 'light'   THEN 1 ELSE 0 END) AS light_hits,
            SUM(CASE WHEN device_type      = 'desktop' THEN 1 ELSE 0 END) AS desktop_hits,
            SUM(CASE WHEN device_type      = 'mobile'  THEN 1 ELSE 0 END) AS mobile_hits,
            SUM(CASE WHEN device_type      = 'tablet'  THEN 1 ELSE 0 END) AS tablet_hits
         FROM analytics_events"
    ) ?? [];

    return [
        'page_views' => cv_count_rows('analytics_events', "event_type = 'page_view'"),
        'unique_visitors' => (int) ((cv_fetch_one("SELECT COUNT(DISTINCT visitor_token) AS aggregate FROM analytics_events WHERE event_type = 'page_view'") ?? [])['aggregate'] ?? 0),
        'project_views' => cv_count_rows('analytics_events', "event_type = 'project_view'"),
        'external_clicks' => cv_count_rows('analytics_events', "event_type = 'external_click'"),
        'contact_submissions' => cv_count_rows('analytics_events', "event_type = 'contact_submit'"),
        'dark_hits' => (int) ($themeRow['dark_hits'] ?? 0),
        'light_hits' => (int) ($themeRow['light_hits'] ?? 0),
        'desktop_hits' => (int) ($themeRow['desktop_hits'] ?? 0),
        'mobile_hits' => (int) ($themeRow['mobile_hits'] ?? 0),
        'tablet_hits' => (int) ($themeRow['tablet_hits'] ?? 0),
    ];
    });
}

function cv_top_referrers(int $limit = 8): array
{
    $limit = max(1, $limit);

    return cv_cache_remember('analytics:top_referrers:' . $limit, 60, static fn (): array => cv_fetch_all(
        "SELECT referrer_host, COUNT(*) AS hit_count
         FROM analytics_events
         WHERE referrer_host IS NOT NULL AND referrer_host <> ''
         GROUP BY referrer_host
         ORDER BY hit_count DESC
         LIMIT {$limit}"
    ));
}

function cv_analytics_daily_series(int $days = 30): array
{
    $days = max(7, min(365, $days));
    return cv_cache_remember('analytics:daily:' . $days, 60, static fn (): array => cv_fetch_all(
        "SELECT * FROM analytics_daily WHERE event_date >= CURRENT_DATE - INTERVAL '{$days} days' ORDER BY event_date ASC"
    ));
}

function cv_top_projects(int $limit = 5): array
{
    $limit = max(1, $limit);

    return cv_cache_remember('analytics:top_projects:' . $limit, 60, static fn (): array => cv_fetch_all(
        "SELECT p.id, p.slug, p.title_ru, p.title_en, COUNT(a.id) AS view_count
         FROM projects p
         INNER JOIN analytics_events a ON a.project_id = p.id AND a.event_type = 'project_view'
         GROUP BY p.id, p.slug, p.title_ru, p.title_en
         ORDER BY view_count DESC
         LIMIT {$limit}"
    ));
}
