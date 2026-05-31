<?php

declare(strict_types=1);

function cv_admin_entity_configs(): array
{
    return [
        'skills' => [
            'table' => 'skills',
            'fields' => ['group_ru', 'group_en', 'title_ru', 'title_en', 'description_ru', 'description_en', 'skill_level', 'icon', 'sort_order', 'status'],
        ],
        'services' => [
            'table' => 'services',
            'fields' => ['title_ru', 'title_en', 'description_ru', 'description_en', 'cta_ru', 'cta_en', 'accent', 'sort_order', 'status'],
        ],
        'testimonials' => [
            'table' => 'testimonials',
            'fields' => ['name', 'role_ru', 'role_en', 'company', 'quote_ru', 'quote_en', 'avatar', 'rating', 'sort_order', 'status'],
        ],
        'faq' => [
            'table' => 'faqs',
            'fields' => ['question_ru', 'question_en', 'answer_ru', 'answer_en', 'sort_order', 'status'],
        ],
        'categories' => [
            'table' => 'project_categories',
            'fields' => ['name_ru', 'name_en', 'slug', 'description_ru', 'description_en', 'sort_order', 'status'],
        ],
        'blog' => [
            'table' => 'blog_posts',
            'fields' => ['title_ru', 'title_en', 'slug', 'excerpt_ru', 'excerpt_en', 'body_ru', 'body_en', 'cover_image', 'tags', 'published_at', 'featured', 'sort_order', 'status'],
        ],
    ];
}

function cv_admin_entity_records(string $section): array
{
    return match ($section) {
        'skills' => cv_fetch_skills(false),
        'services' => cv_fetch_services(false),
        'testimonials' => cv_fetch_testimonials(false),
        'faq' => cv_fetch_faqs(false),
        'categories' => cv_fetch_categories(false),
        'blog' => cv_fetch_blog_posts(['published_only' => false]),
        default => [],
    };
}

function cv_admin_save_entity(string $section, array $input, int $id = 0): int
{
    $configs = cv_admin_entity_configs();
    $config = $configs[$section] ?? null;

    if (!$config) {
        return 0;
    }

    $fields = [];

    foreach ($config['fields'] as $field) {
        if (in_array($field, ['sort_order', 'rating'], true)) {
            $fields[$field] = (int) ($input[$field] ?? 0);
            continue;
        }

        if ($field === 'slug') {
            $fields[$field] = cv_slugify((string) ($input[$field] ?? $input['name_en'] ?? $input['name_ru'] ?? 'item'));
            continue;
        }

        $fields[$field] = trim((string) ($input[$field] ?? ''));
    }

    $fields['updated_at'] = cv_now();

    if ($id > 0) {
        $assignments = [];
        foreach (array_keys($fields) as $column) {
            $assignments[] = $column . ' = :' . $column;
        }

        $fields['id'] = $id;
        cv_execute(
            'UPDATE ' . $config['table'] . ' SET ' . implode(', ', $assignments) . ' WHERE id = :id',
            $fields
        );

        cv_cache_flush();

        return $id;
    }

    $fields['created_at'] = cv_now();
    $columns = array_keys($fields);
    $columnSql = implode(', ', $columns);
    $placeholderSql = implode(', ', array_map(static fn (string $column): string => ':' . $column, $columns));
    cv_execute(
        'INSERT INTO ' . $config['table'] . ' (' . $columnSql . ') VALUES (' . $placeholderSql . ')',
        $fields
    );

    $newId = cv_last_insert_id();
    cv_cache_flush();

    return $newId;
}

function cv_admin_blog_result(int $postId = 0, string $message = ''): array
{
    return [
        'ok' => $postId > 0,
        'post_id' => $postId,
        'message' => $message !== '' ? $message : ($postId > 0 ? cv_t('messages.saved') : 'Blog post could not be saved.'),
        'errors' => [],
        'cover_saved' => false,
    ];
}

function cv_admin_blog_database_error(Throwable $exception): string
{
    $message = strtolower($exception->getMessage());
    $code = (string) $exception->getCode();

    if ($code === '23505' || $code === '23000' || str_contains($message, 'duplicate') || str_contains($message, 'unique')) {
        return 'Slug is already used. Choose another slug.';
    }

    if (str_contains($message, 'published_at') || str_contains($message, 'timestamp')) {
        return 'Published date is invalid. Use the date picker or leave it empty.';
    }

    return 'Blog post could not be saved. Check the database schema and try again.';
}

function cv_admin_normalize_datetime(?string $value): array
{
    $raw = trim((string) $value);

    if ($raw === '') {
        return ['ok' => true, 'value' => null];
    }

    $timestamp = strtotime(str_replace('T', ' ', $raw));

    if ($timestamp === false) {
        return ['ok' => false, 'value' => null];
    }

    return ['ok' => true, 'value' => date('Y-m-d H:i:s', $timestamp)];
}

function cv_admin_save_blog_post(array $input, array $files, int $id = 0): array
{
    $fields = [
        'title_ru' => trim((string) ($input['title_ru'] ?? '')),
        'title_en' => trim((string) ($input['title_en'] ?? '')),
        'slug' => cv_slugify((string) ($input['slug'] ?? $input['title_en'] ?? $input['title_ru'] ?? 'blog-post')),
        'excerpt_ru' => trim((string) ($input['excerpt_ru'] ?? '')),
        'excerpt_en' => trim((string) ($input['excerpt_en'] ?? '')),
        'body_ru' => trim((string) ($input['body_ru'] ?? '')),
        'body_en' => trim((string) ($input['body_en'] ?? '')),
        'cover_image' => trim((string) ($input['existing_cover_image'] ?? '')),
        'tags' => trim((string) ($input['tags'] ?? '')),
        'published_at' => null,
        'featured' => cv_boolean($input['featured'] ?? false) ? 1 : 0,
        'sort_order' => (int) ($input['sort_order'] ?? 0),
        'status' => trim((string) ($input['status'] ?? 'draft')),
        'updated_at' => cv_now(),
    ];

    $result = cv_admin_blog_result($id);

    if ($fields['title_ru'] === '' && $fields['title_en'] === '') {
        $result['errors'][] = 'Add at least one blog title.';
    }

    if (!in_array($fields['status'], ['draft', 'published'], true)) {
        $fields['status'] = 'draft';
    }

    $publishedAt = cv_admin_normalize_datetime((string) ($input['published_at'] ?? ''));
    if (($publishedAt['ok'] ?? false) !== true) {
        $result['errors'][] = 'Published date is invalid. Use the date picker or leave it empty.';
    } else {
        $fields['published_at'] = $publishedAt['value'];
    }

    $coverFile = $files['cover_image_file'] ?? null;
    if (is_array($coverFile)) {
        $coverError = (int) ($coverFile['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($coverError !== UPLOAD_ERR_NO_FILE) {
            if ($coverError !== UPLOAD_ERR_OK) {
                $result['errors'][] = 'Cover image: ' . cv_upload_error_message($coverError, 'image');
            } else {
                $upload = cv_process_database_image_upload($coverFile, 'blog-cover');

                if (($upload['ok'] ?? false) === true) {
                    $fields['cover_image'] = (string) ($upload['path'] ?? '');
                    $result['cover_saved'] = true;
                } else {
                    $result['errors'][] = 'Cover image: ' . (string) ($upload['error'] ?? cv_t('messages.upload_failed'));
                }
            }
        }
    }

    if ($result['errors'] !== []) {
        $result['ok'] = false;
        $result['message'] = 'Blog post was not saved. Review the highlighted issues and try again.';

        return $result;
    }

    try {
        if ($id > 0) {
            $assignments = [];
            foreach (array_keys($fields) as $column) {
                $assignments[] = $column . ' = :' . $column;
            }

            $fields['id'] = $id;
            cv_execute('UPDATE blog_posts SET ' . implode(', ', $assignments) . ' WHERE id = :id', $fields);
        } else {
            $fields['created_at'] = cv_now();
            $columns = array_keys($fields);
            $columnSql = implode(', ', $columns);
            $placeholderSql = implode(', ', array_map(static fn (string $column): string => ':' . $column, $columns));
            cv_execute('INSERT INTO blog_posts (' . $columnSql . ') VALUES (' . $placeholderSql . ')', $fields);
            $id = cv_last_insert_id();
        }
    } catch (Throwable $exception) {
        cv_log('admin', 'Blog post save failed', [
            'id' => $id,
            'error' => $exception->getMessage(),
        ]);

        $result['ok'] = false;
        $result['message'] = cv_admin_blog_database_error($exception);
        $result['errors'][] = $result['message'];

        return $result;
    }

    if ($id <= 0) {
        return cv_admin_blog_result(0, 'Blog post could not be saved.');
    }

    $result['post_id'] = $id;
    $result['ok'] = true;
    $result['message'] = $result['cover_saved']
        ? cv_t('messages.saved') . ' (cover saved)'
        : cv_t('messages.saved');

    cv_cache_flush();

    return $result;
}

function cv_admin_delete_entity(string $section, int $id): bool
{
    $configs = cv_admin_entity_configs();
    $config = $configs[$section] ?? null;

    $deleted = $config ? cv_delete_by_id($config['table'], $id) : false;
    if ($deleted) {
        cv_cache_flush();
    }

    return $deleted;
}

function cv_admin_save_content_block(string $blockKey, array $payload, ?string $locale = null): bool
{
    return cv_store_block($blockKey, $payload, $locale, (string) ($payload['status'] ?? 'published'));
}

function cv_admin_save_settings_group(string $group, array $fields, ?string $locale = null): bool
{
    $saved = true;

    foreach ($fields as $key => $value) {
        if ($key === '_token' || str_starts_with($key, '_')) {
            continue;
        }
        $type = is_array($value) ? 'json' : 'text';
        $saved = cv_store_setting($group, $key, $value, $locale, $type) && $saved;
    }

    return $saved;
}

function cv_admin_project_result(int $projectId = 0, string $message = ''): array
{
    return [
        'ok' => $projectId > 0,
        'project_id' => $projectId,
        'message' => $message !== '' ? $message : ($projectId > 0 ? cv_t('messages.saved') : cv_t('messages.upload_failed')),
        'errors' => [],
        'warnings' => [],
        'saved_gallery_count' => 0,
        'saved_video' => false,
    ];
}

function cv_admin_allowed_message_statuses(): array
{
    return ['new', 'replied', 'archived'];
}

function cv_admin_save_project(array $input, array $files, int $id = 0): array
{
    $fields = [
        'category_id' => (int) ($input['category_id'] ?? 0) ?: null,
        'title_ru' => trim((string) ($input['title_ru'] ?? '')),
        'title_en' => trim((string) ($input['title_en'] ?? '')),
        'slug' => cv_slugify((string) ($input['slug'] ?? $input['title_en'] ?? $input['title_ru'] ?? 'project')),
        'short_description_ru' => trim((string) ($input['short_description_ru'] ?? '')),
        'short_description_en' => trim((string) ($input['short_description_en'] ?? '')),
        'full_description_ru' => trim((string) ($input['full_description_ru'] ?? '')),
        'full_description_en' => trim((string) ($input['full_description_en'] ?? '')),
        'role_ru' => trim((string) ($input['role_ru'] ?? '')),
        'role_en' => trim((string) ($input['role_en'] ?? '')),
        'technologies' => trim((string) ($input['technologies'] ?? '')),
        'external_url' => trim((string) ($input['external_url'] ?? '')),
        'featured' => cv_boolean($input['featured'] ?? false) ? 1 : 0,
        'status' => trim((string) ($input['status'] ?? 'draft')),
        'sort_order' => (int) ($input['sort_order'] ?? 0),
        'client_ru' => trim((string) ($input['client_ru'] ?? '')),
        'client_en' => trim((string) ($input['client_en'] ?? '')),
        'problem_ru' => trim((string) ($input['problem_ru'] ?? '')),
        'problem_en' => trim((string) ($input['problem_en'] ?? '')),
        'process_ru' => trim((string) ($input['process_ru'] ?? '')),
        'process_en' => trim((string) ($input['process_en'] ?? '')),
        'solution_ru' => trim((string) ($input['solution_ru'] ?? '')),
        'solution_en' => trim((string) ($input['solution_en'] ?? '')),
        'result_ru' => trim((string) ($input['result_ru'] ?? '')),
        'result_en' => trim((string) ($input['result_en'] ?? '')),
        'seo_title_ru' => trim((string) ($input['seo_title_ru'] ?? '')),
        'seo_title_en' => trim((string) ($input['seo_title_en'] ?? '')),
        'seo_description_ru' => trim((string) ($input['seo_description_ru'] ?? '')),
        'seo_description_en' => trim((string) ($input['seo_description_en'] ?? '')),
        'updated_at' => cv_now(),
    ];

    $result = cv_admin_project_result($id);

    if ($fields['title_ru'] === '' && $fields['title_en'] === '') {
        $result['errors'][] = 'Add at least one project title.';
    }

    if ($fields['external_url'] !== '' && !filter_var($fields['external_url'], FILTER_VALIDATE_URL)) {
        $result['errors'][] = 'External URL must be a valid absolute URL.';
    }

    if (!in_array($fields['status'], ['draft', 'published'], true)) {
        $fields['status'] = 'draft';
    }

    if (!empty($files['cover_image']) && ($files['cover_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $upload = cv_process_image_upload(
            $files['cover_image'],
            'project-cover',
            (string) ($input['cover_alt_ru'] ?? ''),
            (string) ($input['cover_alt_en'] ?? '')
        );

        if (($upload['ok'] ?? false) === true) {
            $fields['cover_image'] = $upload['path'];
            $fields['og_image'] = $upload['path'];
        } else {
            $result['errors'][] = 'Cover image: ' . (string) ($upload['error'] ?? cv_t('messages.upload_failed'));
        }
    }

    $fields['cover_alt_ru'] = trim((string) ($input['cover_alt_ru'] ?? ''));
    $fields['cover_alt_en'] = trim((string) ($input['cover_alt_en'] ?? ''));
    if (!isset($fields['cover_image'])) {
        $fields['cover_image'] = trim((string) ($input['existing_cover_image'] ?? ''));
        $fields['og_image'] = trim((string) ($input['existing_og_image'] ?? $fields['cover_image']));
    }

    if ($result['errors'] !== []) {
        $result['ok'] = false;
        $result['message'] = 'Project could not be saved yet. Review the highlighted issues and try again.';

        return $result;
    }

    if ($id > 0) {
        $assignments = [];
        foreach (array_keys($fields) as $column) {
            $assignments[] = $column . ' = :' . $column;
        }
        $fields['id'] = $id;
        cv_execute('UPDATE projects SET ' . implode(', ', $assignments) . ' WHERE id = :id', $fields);
    } else {
        $fields['created_at'] = cv_now();
        $columns = array_keys($fields);
        $columnSql = implode(', ', $columns);
        $placeholderSql = implode(', ', array_map(static fn (string $column): string => ':' . $column, $columns));
        cv_execute('INSERT INTO projects (' . $columnSql . ') VALUES (' . $placeholderSql . ')', $fields);
        $id = cv_last_insert_id();
    }

    if ($id <= 0) {
        return cv_admin_project_result(0, cv_t('messages.upload_failed'));
    }

    $result['project_id'] = $id;
    $result['ok'] = true;

    if (isset($files['gallery_images'])) {
        foreach (cv_normalize_uploads($files['gallery_images']) as $index => $galleryFile) {
            if (($galleryFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $galleryUpload = cv_process_image_upload(
                $galleryFile,
                'project-gallery',
                trim((string) ($input['gallery_alt_ru'] ?? '')),
                trim((string) ($input['gallery_alt_en'] ?? ''))
            );

            if (($galleryUpload['ok'] ?? false) !== true) {
                $result['errors'][] = 'Gallery image: ' . (string) ($galleryUpload['error'] ?? cv_t('messages.upload_failed'));
                continue;
            }

            cv_execute(
                'INSERT INTO project_images (project_id, file_path, thumb_path, alt_ru, alt_en, is_cover, sort_order, created_at)
                 VALUES (:project_id, :file_path, :thumb_path, :alt_ru, :alt_en, 0, :sort_order, :created_at)',
                [
                    'project_id' => $id,
                    'file_path' => $galleryUpload['path'],
                    'thumb_path' => $galleryUpload['thumb_path'],
                    'alt_ru' => trim((string) ($input['gallery_alt_ru'] ?? '')),
                    'alt_en' => trim((string) ($input['gallery_alt_en'] ?? '')),
                    'sort_order' => $index,
                    'created_at' => cv_now(),
                ]
            );
            $result['saved_gallery_count']++;
        }
    }

    $existingVideo = cv_fetch_project_video($id);
    $posterPath = trim((string) ($input['existing_video_poster'] ?? ($existingVideo['poster_image'] ?? '')));

    if (!empty($files['video_poster']) && ($files['video_poster']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $posterUpload = cv_process_image_upload(
            $files['video_poster'],
            'video-poster',
            trim((string) ($input['video_poster_alt_ru'] ?? '')),
            trim((string) ($input['video_poster_alt_en'] ?? ''))
        );
        if (($posterUpload['ok'] ?? false) === true) {
            $posterPath = $posterUpload['path'];
        } else {
            $result['errors'][] = 'Video poster: ' . (string) ($posterUpload['error'] ?? cv_t('messages.upload_failed'));
        }
    }

    if (!empty($files['showcase_video']) && ($files['showcase_video']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $videoResponse = cv_upload_video_to_cloudinary($files['showcase_video'], [
            'public_id' => 'project-' . $id . '-' . date('YmdHis'),
        ]);

        if (($videoResponse['ok'] ?? false) === true) {
            $videoData = $videoResponse['data'];
            $payload = [
                'project_id' => $id,
                'cloudinary_public_id' => (string) ($videoData['public_id'] ?? ''),
                'secure_url' => (string) ($videoData['secure_url'] ?? ''),
                'format' => (string) ($videoData['format'] ?? ''),
                'duration' => (float) ($videoData['duration'] ?? 0),
                'bytes' => (int) ($videoData['bytes'] ?? 0),
                'width' => (int) ($videoData['width'] ?? 0),
                'height' => (int) ($videoData['height'] ?? 0),
                'poster_image' => $posterPath,
                'poster_alt_ru' => trim((string) ($input['video_poster_alt_ru'] ?? '')),
                'poster_alt_en' => trim((string) ($input['video_poster_alt_en'] ?? '')),
                'updated_at' => cv_now(),
            ];

            if ($existingVideo) {
                $payload['id'] = $existingVideo['id'];
                cv_execute(
                    'UPDATE project_videos SET
                     cloudinary_public_id = :cloudinary_public_id,
                     secure_url = :secure_url,
                     format = :format,
                     duration = :duration,
                     bytes = :bytes,
                     width = :width,
                     height = :height,
                     poster_image = :poster_image,
                     poster_alt_ru = :poster_alt_ru,
                     poster_alt_en = :poster_alt_en,
                     updated_at = :updated_at
                     WHERE id = :id',
                    $payload
                );
            } else {
                $payload['created_at'] = cv_now();
                cv_execute(
                    'INSERT INTO project_videos
                     (project_id, cloudinary_public_id, secure_url, format, duration, bytes, width, height, poster_image, poster_alt_ru, poster_alt_en, created_at, updated_at)
                     VALUES
                     (:project_id, :cloudinary_public_id, :secure_url, :format, :duration, :bytes, :width, :height, :poster_image, :poster_alt_ru, :poster_alt_en, :created_at, :updated_at)',
                    $payload
                );
            }
            $result['saved_video'] = true;
        } else {
            $result['errors'][] = 'Showcase video: ' . (string) ($videoResponse['error'] ?? cv_t('messages.upload_failed'));
        }
    } elseif ($existingVideo && $posterPath !== (string) ($existingVideo['poster_image'] ?? '')) {
        cv_execute(
            'UPDATE project_videos SET poster_image = :poster_image, poster_alt_ru = :poster_alt_ru, poster_alt_en = :poster_alt_en, updated_at = :updated_at WHERE id = :id',
            [
                'poster_image' => $posterPath,
                'poster_alt_ru' => trim((string) ($input['video_poster_alt_ru'] ?? (string) ($existingVideo['poster_alt_ru'] ?? ''))),
                'poster_alt_en' => trim((string) ($input['video_poster_alt_en'] ?? (string) ($existingVideo['poster_alt_en'] ?? ''))),
                'updated_at' => cv_now(),
                'id' => $existingVideo['id'],
            ]
        );
    } elseif (!$existingVideo && !empty($files['video_poster']) && ($files['video_poster']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $result['warnings'][] = 'Poster image was uploaded, but no project video is attached yet.';
    }

    $summary = [];
    if (isset($fields['cover_image']) && $fields['cover_image'] !== '') {
        $summary[] = 'cover saved';
    }
    if ($result['saved_gallery_count'] > 0) {
        $summary[] = 'gallery +' . $result['saved_gallery_count'];
    }
    if ($result['saved_video']) {
        $summary[] = 'video attached';
    }

    $result['message'] = $summary === []
        ? cv_t('messages.saved')
        : (cv_t('messages.saved') . ' (' . implode(', ', $summary) . ')');

    cv_cache_flush();

    return $result;
}

function cv_admin_mark_message(int $id, string $status, bool $isRead = true): bool
{
    if (!in_array($status, cv_admin_allowed_message_statuses(), true)) {
        $status = 'new';
    }

    return cv_execute(
        'UPDATE contact_messages SET status = :status, is_read = :is_read, updated_at = :updated_at WHERE id = :id',
        [
            'status' => $status,
            'is_read' => $isRead ? 1 : 0,
            'updated_at' => cv_now(),
            'id' => $id,
        ]
    );
}
