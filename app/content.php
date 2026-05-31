<?php

declare(strict_types=1);

function cv_setting_value(string $storedValue, string $type): mixed
{
    return match ($type) {
        'json' => json_decode($storedValue, true) ?: [],
        'bool' => cv_boolean($storedValue),
        'int' => (int) $storedValue,
        'float' => (float) $storedValue,
        default => $storedValue,
    };
}

function cv_store_setting(string $group, string $key, mixed $value, ?string $locale = null, string $type = 'text'): bool
{
    $localeCode = $locale ?? '*';
    $storedValue = $type === 'json'
        ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        : (string) $value;

    $saved = cv_execute(
        'INSERT INTO site_settings (group_key, setting_key, locale_code, value_long, value_type, updated_at)
         VALUES (:group_key, :setting_key, :locale_code, :value_long, :value_type, :updated_at)
         ON CONFLICT (group_key, setting_key, locale_code) DO UPDATE SET
             value_long = EXCLUDED.value_long,
             value_type = EXCLUDED.value_type,
             updated_at = EXCLUDED.updated_at',
        [
            'group_key' => $group,
            'setting_key' => $key,
            'locale_code' => $localeCode,
            'value_long' => $storedValue,
            'value_type' => $type,
            'updated_at' => cv_now(),
        ]
    );

    if ($saved) {
        cv_cache_flush();
    }

    return $saved;
}

function cv_get_setting(string $group, string $key, ?string $locale = null, mixed $default = null): mixed
{
    $localeCode = $locale ?? cv_current_locale();
    $rows = cv_fetch_all(
        'SELECT locale_code, value_long, value_type
         FROM site_settings
         WHERE group_key = :group_key AND setting_key = :setting_key AND (locale_code = :locale_code OR locale_code = \'*\')
         ORDER BY CASE WHEN locale_code = :locale_order THEN 0 ELSE 1 END ASC
         LIMIT 1',
        [
            'group_key' => $group,
            'setting_key' => $key,
            'locale_code' => $localeCode,
            'locale_order' => $localeCode,
        ]
    );

    if ($rows === []) {
        return $default;
    }

    return cv_setting_value((string) $rows[0]['value_long'], (string) $rows[0]['value_type']);
}

function cv_get_settings_group(string $group, ?string $locale = null): array
{
    $localeCode = $locale ?? cv_current_locale();
    $rows = cv_fetch_all(
        'SELECT setting_key, locale_code, value_long, value_type
         FROM site_settings
         WHERE group_key = :group_key AND (locale_code = :locale_code OR locale_code = \'*\')
         ORDER BY CASE WHEN locale_code = \'*\' THEN 0 ELSE 1 END ASC',
        [
            'group_key' => $group,
            'locale_code' => $localeCode,
        ]
    );

    $output = [];

    foreach ($rows as $row) {
        $output[$row['setting_key']] = cv_setting_value((string) $row['value_long'], (string) $row['value_type']);
    }

    return $output;
}

function cv_store_block(string $blockKey, mixed $payload, ?string $locale = null, string $status = 'published'): bool
{
    $localeCode = $locale ?? cv_current_locale();
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $saved = cv_execute(
        'INSERT INTO content_blocks (block_key, locale_code, payload_json, status, updated_at)
         VALUES (:block_key, :locale_code, :payload_json, :status, :updated_at)
         ON CONFLICT (block_key, locale_code) DO UPDATE SET
             payload_json = EXCLUDED.payload_json,
             status       = EXCLUDED.status,
             updated_at   = EXCLUDED.updated_at',
        [
            'block_key' => $blockKey,
            'locale_code' => $localeCode,
            'payload_json' => $payloadJson,
            'status' => $status,
            'updated_at' => cv_now(),
        ]
    );

    if ($saved) {
        cv_cache_flush();
    }

    return $saved;
}

function cv_get_block(string $blockKey, ?string $locale = null, array $default = []): array
{
    $localeCode = $locale ?? cv_current_locale();
    $rows = cv_fetch_all(
        'SELECT locale_code, payload_json
         FROM content_blocks
         WHERE block_key = :block_key AND (locale_code = :locale_code OR locale_code = \'*\')
         ORDER BY CASE WHEN locale_code = :locale_order THEN 0 ELSE 1 END ASC
         LIMIT 1',
        [
            'block_key' => $blockKey,
            'locale_code' => $localeCode,
            'locale_order' => $localeCode,
        ]
    );

    if ($rows === []) {
        return $default;
    }

    $payload = json_decode((string) $rows[0]['payload_json'], true);

    return is_array($payload) ? $payload : $default;
}

function cv_localized_value(array $row, string $base, ?string $locale = null, string $fallback = ''): string
{
    $locale = $locale ?? cv_current_locale();
    $defaultLocale = cv_default_locale();

    return (string) ($row[$base . '_' . $locale] ?? $row[$base . '_' . $defaultLocale] ?? $fallback);
}

function cv_fetch_skills(bool $publishedOnly = true): array
{
    $cacheKey = 'skills:' . ($publishedOnly ? 'published' : 'all');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $where = $publishedOnly ? "WHERE status = 'published'" : '';
    $rows = cv_fetch_all("SELECT * FROM skills {$where} ORDER BY sort_order ASC, id ASC");
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_grouped_skills(array $skills, ?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();
    $groups = [];

    foreach ($skills as $skill) {
        $group = cv_localized_value($skill, 'group', $locale, 'Stack');
        if (!isset($groups[$group])) {
            $groups[$group] = [];
        }
        $groups[$group][] = $skill;
    }

    return $groups;
}

function cv_fetch_services(bool $publishedOnly = true): array
{
    $cacheKey = 'services:' . ($publishedOnly ? 'published' : 'all');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $where = $publishedOnly ? "WHERE status = 'published'" : '';
    $rows = cv_fetch_all("SELECT * FROM services {$where} ORDER BY sort_order ASC, id ASC");
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_fetch_testimonials(bool $publishedOnly = true): array
{
    $cacheKey = 'testimonials:' . ($publishedOnly ? 'published' : 'all');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $where = $publishedOnly ? "WHERE status = 'published'" : '';
    $rows = cv_fetch_all("SELECT * FROM testimonials {$where} ORDER BY sort_order ASC, id ASC");
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_fetch_faqs(bool $publishedOnly = true): array
{
    $cacheKey = 'faqs:' . ($publishedOnly ? 'published' : 'all');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $where = $publishedOnly ? "WHERE status = 'published'" : '';
    $rows = cv_fetch_all("SELECT * FROM faqs {$where} ORDER BY sort_order ASC, id ASC");
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_fetch_categories(bool $publishedOnly = true): array
{
    $cacheKey = 'categories:' . ($publishedOnly ? 'published' : 'all');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $where = $publishedOnly ? "WHERE status = 'published'" : '';
    $rows = cv_fetch_all("SELECT * FROM project_categories {$where} ORDER BY sort_order ASC, id ASC");
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_fetch_projects(array $options = []): array
{
    $cacheKey = 'projects:' . hash('sha256', json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $conditions = [];
    $params = [];

    if (($options['published_only'] ?? true) === true) {
        $conditions[] = "p.status = 'published'";
    }

    if (!empty($options['category_slug'])) {
        $conditions[] = 'c.slug = :category_slug';
        $params['category_slug'] = $options['category_slug'];
    }

    if (!empty($options['featured_only'])) {
        $conditions[] = 'p.featured = 1';
    }

    $where = $conditions === [] ? '' : ('WHERE ' . implode(' AND ', $conditions));

    $rows = cv_fetch_all(
        'SELECT p.*, c.name_ru AS category_name_ru, c.name_en AS category_name_en, c.slug AS category_slug
         FROM projects p
         LEFT JOIN project_categories c ON c.id = p.category_id
         ' . $where . '
         ORDER BY p.sort_order ASC, p.updated_at DESC',
        $params
    );
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_fetch_blog_posts(array $options = []): array
{
    $cacheKey = 'blog_posts:' . hash('sha256', json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');
    $cached = cv_cache_get($cacheKey);
    if (is_array($cached)) {
        return $cached;
    }

    $conditions = [];
    $params = [];

    if (($options['published_only'] ?? true) === true) {
        $conditions[] = "status = 'published'";
    }

    if (!empty($options['tag'])) {
        $conditions[] = ':tag = ANY(string_to_array(tags, \',\'))';
        $params['tag'] = trim((string) $options['tag']);
    }

    $where = $conditions === [] ? '' : ('WHERE ' . implode(' AND ', $conditions));

    $rows = cv_fetch_all(
        'SELECT * FROM blog_posts ' . $where . ' ORDER BY featured DESC, sort_order ASC, published_at DESC, created_at DESC',
        $params
    );
    cv_cache_set($cacheKey, $rows, 60);

    return $rows;
}

function cv_fetch_blog_post_by_slug(string $slug, bool $includeDraft = false): ?array
{
    $cacheKey = 'blog_post_by_slug:' . $slug . ':' . ($includeDraft ? 'draft' : 'public');
    $cached = cv_cache_get($cacheKey, false);
    if ($cached !== false) {
        return is_array($cached) ? $cached : null;
    }

    $statusSql = $includeDraft ? '' : "AND status = 'published'";

    $row = cv_fetch_one(
        'SELECT * FROM blog_posts WHERE slug = :slug ' . $statusSql . ' LIMIT 1',
        ['slug' => $slug]
    );
    cv_cache_set($cacheKey, $row, 60);

    return $row;
}

function cv_fetch_project_by_slug(string $slug, bool $includeDraft = false): ?array
{
    $cacheKey = 'project_by_slug:' . $slug . ':' . ($includeDraft ? 'draft' : 'public');
    $cached = cv_cache_get($cacheKey, false);
    if ($cached !== false) {
        return is_array($cached) ? $cached : null;
    }

    $statusSql = $includeDraft ? '' : "AND p.status = 'published'";

    $row = cv_fetch_one(
        'SELECT p.*, c.name_ru AS category_name_ru, c.name_en AS category_name_en, c.slug AS category_slug
         FROM projects p
         LEFT JOIN project_categories c ON c.id = p.category_id
         WHERE p.slug = :slug ' . $statusSql . ' LIMIT 1',
        ['slug' => $slug]
    );
    cv_cache_set($cacheKey, $row, 60);

    return $row;
}

function cv_fetch_project_images(int $projectId): array
{
    return cv_fetch_all(
        'SELECT * FROM project_images WHERE project_id = :project_id ORDER BY sort_order ASC, id ASC',
        ['project_id' => $projectId]
    );
}

function cv_fetch_project_video(int $projectId): ?array
{
    return cv_fetch_one(
        'SELECT * FROM project_videos WHERE project_id = :project_id ORDER BY id DESC LIMIT 1',
        ['project_id' => $projectId]
    );
}

function cv_fetch_media_library(?string $type = null): array
{
    if ($type === null || $type === '') {
        return cv_fetch_all('SELECT * FROM media_library ORDER BY created_at DESC, id DESC');
    }

    return cv_fetch_all(
        'SELECT * FROM media_library WHERE type = :type ORDER BY created_at DESC, id DESC',
        ['type' => $type]
    );
}

function cv_fetch_contact_messages(?string $status = null): array
{
    if ($status) {
        return cv_fetch_all(
            'SELECT * FROM contact_messages WHERE status = :status ORDER BY created_at DESC',
            ['status' => $status]
        );
    }

    return cv_fetch_all('SELECT * FROM contact_messages ORDER BY created_at DESC');
}

function cv_find_by_id(string $table, int $id): ?array
{
    return cv_fetch_one("SELECT * FROM {$table} WHERE id = :id LIMIT 1", ['id' => $id]);
}

function cv_delete_by_id(string $table, int $id): bool
{
    return cv_execute("DELETE FROM {$table} WHERE id = :id", ['id' => $id]);
}

function cv_admin_dashboard_metrics(): array
{
    return [
        'projects' => cv_count_rows('projects'),
        'published_projects' => cv_count_rows('projects', "status = 'published'"),
        'messages' => cv_count_rows('contact_messages'),
        'new_messages' => cv_count_rows('contact_messages', "status = 'new'"),
        'categories' => cv_count_rows('project_categories'),
        'services' => cv_count_rows('services'),
        'testimonials' => cv_count_rows('testimonials'),
        'faqs' => cv_count_rows('faqs'),
        'media_items' => cv_count_rows('media_library'),
        'page_views' => cv_count_rows('analytics_events', "event_type = 'page_view'"),
    ];
}

function cv_count_rows(string $table, string $where = '1 = 1', array $params = []): int
{
    $row = cv_fetch_one("SELECT COUNT(*) AS aggregate FROM {$table} WHERE {$where}", $params);
    return (int) ($row['aggregate'] ?? 0);
}
