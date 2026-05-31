<?php

declare(strict_types=1);

function cv_backup_tables(): array
{
    return [
        'site_settings',
        'content_blocks',
        'skills',
        'services',
        'faqs',
        'testimonials',
        'project_categories',
        'projects',
        'project_images',
        'project_videos',
    ];
}

function cv_export_site_payload(): array
{
    $payload = [
        'exported_at' => cv_now(),
        'app' => [
            'name' => cv_config('app.name'),
            'default_locale' => cv_default_locale(),
        ],
        'tables' => [],
    ];

    foreach (cv_backup_tables() as $table) {
        $payload['tables'][$table] = cv_fetch_all("SELECT * FROM {$table}");
    }

    return $payload;
}

function cv_export_site_json(): string
{
    return json_encode(cv_export_site_payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
}

function cv_write_backup_file(): string
{
    $fileName = 'backup-' . date('Ymd-His') . '.json';
    cv_ensure_directory(cv_root('storage/backups'));
    $path = cv_root('storage/backups/' . $fileName);
    file_put_contents($path, cv_export_site_json());

    return $path;
}

function cv_import_site_payload(array $payload): array
{
    $pdo = cv_db();

    if (!$pdo) {
        return ['ok' => false, 'message' => 'Database is not available.'];
    }

    try {
        $pdo->beginTransaction();

        foreach (cv_backup_tables() as $table) {
            $rows = $payload['tables'][$table] ?? null;

            if (!is_array($rows)) {
                continue;
            }

            $pdo->exec("DELETE FROM {$table}");

            foreach ($rows as $row) {
                if (!is_array($row) || $row === []) {
                    continue;
                }

                $columns = array_keys($row);
                $columnSql = implode(', ', $columns);
                $placeholderSql = implode(', ', array_map(static fn (string $column) => ':' . $column, $columns));
                $statement = $pdo->prepare("INSERT INTO {$table} ({$columnSql}) VALUES ({$placeholderSql})");
                $statement->execute($row);
            }
        }

        $pdo->commit();

        return ['ok' => true, 'message' => 'Import completed.'];
    } catch (\Throwable $throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        cv_log('backup', 'Import failed', ['error' => $throwable->getMessage()]);

        return ['ok' => false, 'message' => $throwable->getMessage()];
    }
}

function cv_export_analytics_csv(int $days = 90): string
{
    $days = max(7, min(365, $days));
    $rows = cv_fetch_all(
        "SELECT * FROM analytics_daily WHERE event_date >= CURRENT_DATE - INTERVAL '{$days} days' ORDER BY event_date ASC"
    );
    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, ['date', 'page_views', 'unique_visitors', 'project_views', 'external_clicks', 'contact_submissions', 'mobile_visits', 'tablet_visits', 'desktop_visits', 'dark_theme_hits', 'light_theme_hits']);
    foreach ($rows as $row) {
        fputcsv($handle, [
            $row['event_date'],
            $row['page_views'],
            $row['unique_visitors'],
            $row['project_views'],
            $row['external_clicks'],
            $row['contact_submissions'],
            $row['mobile_visits'],
            $row['tablet_visits'],
            $row['desktop_visits'],
            $row['dark_theme_hits'],
            $row['light_theme_hits'],
        ]);
    }
    rewind($handle);
    return (string) stream_get_contents($handle);
}

function cv_export_messages_csv(): string
{
    $messages = cv_fetch_all('SELECT * FROM contact_messages ORDER BY created_at DESC');
    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, ['id', 'name', 'email', 'phone', 'company', 'budget', 'message', 'status', 'created_at']);

    foreach ($messages as $message) {
        fputcsv($handle, [
            $message['id'],
            $message['name'],
            $message['email'],
            $message['phone'],
            $message['company'],
            $message['budget'],
            $message['message'],
            $message['status'],
            $message['created_at'],
        ]);
    }

    rewind($handle);

    return (string) stream_get_contents($handle);
}
