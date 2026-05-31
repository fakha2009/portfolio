<?php

declare(strict_types=1);

function cv_admin_sections(): array
{
    return [
        'dashboard' => ['label' => cv_t('admin.dashboard'), 'icon' => 'grid'],
        'settings' => ['label' => cv_t('admin.settings'), 'icon' => 'settings'],
        'hero' => ['label' => cv_t('admin.hero'), 'icon' => 'sparkles'],
        'about' => ['label' => cv_t('admin.about'), 'icon' => 'user'],
        'skills' => ['label' => cv_t('admin.skills'), 'icon' => 'code'],
        'services' => ['label' => cv_t('admin.services'), 'icon' => 'briefcase'],
        'testimonials' => ['label' => cv_t('admin.testimonials'), 'icon' => 'quote'],
        'faq' => ['label' => cv_t('admin.faq'), 'icon' => 'help'],
        'categories' => ['label' => cv_t('admin.categories'), 'icon' => 'layers'],
        'projects' => ['label' => cv_t('admin.projects'), 'icon' => 'folder'],
        'blog' => ['label' => cv_t('admin.blog'), 'icon' => 'book'],
        'media' => ['label' => cv_t('admin.media'), 'icon' => 'image'],
        'messages' => ['label' => cv_t('admin.messages'), 'icon' => 'mail'],
        'seo' => ['label' => cv_t('admin.seo'), 'icon' => 'search'],
        'social' => ['label' => cv_t('admin.social'), 'icon' => 'at-sign'],
        'theme' => ['label' => cv_t('admin.theme'), 'icon' => 'palette'],
        'analytics' => ['label' => cv_t('admin.analytics'), 'icon' => 'chart'],
        'backup' => ['label' => cv_t('admin.backup'), 'icon' => 'database'],
        'security' => ['label' => cv_t('admin.security'), 'icon' => 'shield'],
    ];
}

function cv_admin_default_site_settings(?string $locale = null): array
{
    $isRu = ($locale ?? cv_current_locale()) === 'ru';
    $ownerName = $isRu ? 'Махмадхонзода Фахриддин' : 'Makhmadkhonzoda Fakhriddin';
    $siteTagline = $isRu
        ? 'Backend developer с фокусом на PHP, Go, API, админ-панели и прикладные веб-системы.'
        : 'Backend developer focused on PHP, Go, APIs, admin panels, and practical web systems.';
    $footerNotice = $isRu
        ? 'Премиальное portfolio backend developer: API, web-системы, админ-панели и продуманная реализация.'
        : 'Premium backend portfolio with APIs, admin systems, case studies, and shared-hosting-friendly implementation.';
    $location = $isRu ? 'Гиссар, Таджикистан' : 'Hisor, Tajikistan';

    return [
        'site' => [
            'site_name' => $ownerName,
            'site_tagline' => $siteTagline,
            'primary_cta_label' => $isRu ? 'Обсудить задачу' : 'Discuss your project',
            'primary_cta_url' => '#contact',
            'secondary_cta_label' => $isRu ? 'Смотреть кейсы' : 'View case studies',
            'secondary_cta_url' => '#projects',
            'contact_email' => 'fakhridinkon2009@gmail.com',
            'contact_phone' => '+992 881 845 151',
            'contact_telegram' => '@Fakhriddin_dev',
            'location' => $location,
            'footer_notice' => $footerNotice,
            'theme_default' => cv_config('app.theme_default', 'dark'),
        ],
        'social' => [
            'telegram' => 'https://t.me/Fakhriddin_dev',
            'email' => 'mailto:fakhridinkon2009@gmail.com',
            'phone' => 'tel:+992881845151',
            'linkedin' => 'https://www.linkedin.com/in/fakhriddin-go/',
            'github' => 'https://github.com/fakha2009',
            'instagram' => '',
        ],
        'seo' => [
            'meta_title' => $ownerName . ' | Backend Developer • Go / PHP / Python',
            'meta_description' => $isRu
                ? 'Backend developer: Go, PHP, Python, API, PostgreSQL, MySQL, Redis, Docker и прикладные веб-системы.'
                : 'Backend developer focused on Go, PHP, Python, APIs, admin panels, databases, and practical web systems.',
            'og_image' => 'assets/img/og-cover.svg',
            'twitter_card' => 'summary_large_image',
            'robots_index' => '1',
        ],
    ];
}

function cv_admin_settings_group(string $group, ?string $locale = null): array
{
    $defaults = cv_admin_default_site_settings($locale)[$group] ?? [];
    return array_replace($defaults, cv_get_settings_group($group, $locale));
}

function cv_admin_route_context(array $segments): array
{
    $locale = (string) cv_get('locale', cv_current_locale());
    if (!array_key_exists($locale, cv_available_locales())) {
        $locale = cv_current_locale();
    }

    return [
        'section' => $segments[0] ?? 'dashboard',
        'action' => $segments[1] ?? 'index',
        'id' => isset($segments[2]) ? (int) $segments[2] : 0,
        'locale' => $locale,
    ];
}

function cv_admin_project_defaults(): array
{
    return [
        'id' => 0,
        'category_id' => '',
        'title_ru' => '',
        'title_en' => '',
        'slug' => '',
        'short_description_ru' => '',
        'short_description_en' => '',
        'full_description_ru' => '',
        'full_description_en' => '',
        'role_ru' => '',
        'role_en' => '',
        'technologies' => '',
        'external_url' => '',
        'featured' => 0,
        'status' => 'draft',
        'sort_order' => 0,
        'cover_image' => '',
        'cover_alt_ru' => '',
        'cover_alt_en' => '',
        'og_image' => '',
        'client_ru' => '',
        'client_en' => '',
        'problem_ru' => '',
        'problem_en' => '',
        'process_ru' => '',
        'process_en' => '',
        'solution_ru' => '',
        'solution_en' => '',
        'result_ru' => '',
        'result_en' => '',
        'seo_title_ru' => '',
        'seo_title_en' => '',
        'seo_description_ru' => '',
        'seo_description_en' => '',
    ];
}

function cv_admin_blog_defaults(): array
{
    return [
        'id' => 0,
        'title_ru' => '',
        'title_en' => '',
        'slug' => '',
        'excerpt_ru' => '',
        'excerpt_en' => '',
        'body_ru' => '',
        'body_en' => '',
        'cover_image' => '',
        'tags' => '',
        'published_at' => '',
        'featured' => 0,
        'sort_order' => 0,
        'status' => 'draft',
    ];
}

function cv_admin_section_data(array $context): array
{
    $section = $context['section'];
    $locale = $context['locale'];
    $data = [
        'section' => $section,
        'action' => $context['action'],
        'id' => $context['id'],
        'locale' => $locale,
        'sections' => cv_admin_sections(),
        'metrics' => [
            'new_messages' => cv_count_rows('contact_messages', "status = 'new'"),
        ],
        'flashes' => cv_consume_flashes(),
        'admin_user' => cv_admin_user(),
        'current_path' => cv_admin_url($section),
    ];

    switch ($section) {
        case 'dashboard':
            $data['metrics'] = cv_admin_dashboard_metrics();
            $data['analytics_summary'] = cv_analytics_summary();
            $data['daily_series'] = cv_analytics_daily_series();
            $data['top_projects'] = cv_top_projects(6);
            $data['top_referrers'] = cv_top_referrers(6);
            $data['recent_messages'] = array_slice(cv_fetch_contact_messages(), 0, 6);
            $data['recent_projects'] = array_slice(cv_fetch_projects(['published_only' => false]), 0, 6);
            break;

        case 'hero':
        case 'about':
            $data['block'] = cv_content_block($section, $locale);
            break;

        case 'theme':
            $data['skills_intro_block'] = cv_content_block('skills_intro', $locale);
            $data['services_intro_block'] = cv_content_block('services_intro', $locale);
            $data['portfolio_intro_block'] = cv_content_block('portfolio_intro', $locale);
            $data['practice_block'] = cv_content_block('practice', $locale);
            $data['pricing_block'] = cv_content_block('pricing', $locale);
            $data['process_block'] = cv_content_block('process', $locale);
            $data['testimonials_intro_block'] = cv_content_block('testimonials_intro', $locale);
            $data['faq_intro_block'] = cv_content_block('faq_intro', $locale);
            $data['contact_block'] = cv_content_block('contact', $locale);
            $data['site_settings'] = cv_site_settings($locale);
            break;

        case 'settings':
            $data['settings'] = cv_admin_settings_group('site', $locale);
            break;

        case 'social':
            $data['settings'] = cv_admin_settings_group('social', $locale);
            break;

        case 'seo':
            $data['settings'] = cv_admin_settings_group('seo', $locale);
            break;

        case 'skills':
        case 'services':
        case 'testimonials':
        case 'faq':
        case 'categories':
        case 'blog':
            $data['records'] = cv_admin_entity_records($section);
            $table = cv_admin_entity_configs()[$section]['table'] ?? '';
            $data['edit_record'] = ($context['action'] === 'edit' && $context['id'] > 0 && $table !== '')
                ? cv_find_by_id($table, $context['id'])
                : null;
            $data['edit_record'] = array_replace(
                $section === 'blog' ? cv_admin_blog_defaults() : [],
                $data['edit_record'] ?: []
            );
            break;

        case 'projects':
            $data['categories'] = cv_fetch_categories(false);
            $data['records'] = cv_fetch_projects(['published_only' => false]);
            $projectRecord = ($context['action'] === 'edit' && $context['id'] > 0)
                ? cv_find_by_id('projects', $context['id'])
                : null;
            $data['edit_record'] = ($projectRecord && !empty($projectRecord['slug']))
                ? cv_fetch_project_by_slug((string) $projectRecord['slug'], true)
                : null;
            if (!$data['edit_record'] && $context['action'] === 'edit' && $context['id'] > 0) {
                $data['edit_record'] = $projectRecord;
            }
            $data['edit_record'] = array_replace(cv_admin_project_defaults(), $data['edit_record'] ?: []);
            if (($data['edit_record']['id'] ?? 0) > 0) {
                $data['edit_gallery'] = cv_fetch_project_images((int) $data['edit_record']['id']);
                $data['edit_video'] = cv_fetch_project_video((int) $data['edit_record']['id']);
            } else {
                $data['edit_gallery'] = [];
                $data['edit_video'] = null;
            }
            break;

        case 'media':
            $data['media'] = cv_fetch_media_library();
            break;

        case 'messages':
            $data['records'] = cv_fetch_contact_messages((string) cv_get('status', ''));
            $data['message_filter'] = (string) cv_get('status', '');
            $data['message_counts'] = [
                'all'      => cv_count_rows('contact_messages'),
                'new'      => cv_count_rows('contact_messages', "status = 'new'"),
                'replied'  => cv_count_rows('contact_messages', "status = 'replied'"),
                'archived' => cv_count_rows('contact_messages', "status = 'archived'"),
            ];
            $data['active_record'] = ($context['action'] === 'view' && $context['id'] > 0)
                ? cv_find_by_id('contact_messages', $context['id'])
                : ($data['records'][0] ?? null);
            break;

        case 'analytics':
            $data['analytics_summary'] = cv_analytics_summary();
            $data['daily_series'] = cv_analytics_daily_series();
            $data['top_projects'] = cv_top_projects(10);
            $data['top_referrers'] = cv_top_referrers(10);
            break;

        case 'backup':
            $data['backup_files'] = glob(cv_root('storage/backups/*.json')) ?: [];
            rsort($data['backup_files']);
            break;

        case 'security':
            $data['profile'] = cv_admin_user();
            break;
    }

    return $data;
}

function cv_admin_handle_post(array $context): void
{
    $section = $context['section'];
    $action = $context['action'];
    $id = $context['id'];
    $locale = $context['locale'];

    if (!cv_is_post()) {
        return;
    }

    $isAjax = cv_is_ajax_request();
    cv_require_csrf('admin');

    switch ($section) {
        case 'settings':
        case 'social':
        case 'seo':
            $saveError = null;
            try {
                $saved = cv_admin_save_settings_group($section, $_POST, $locale);
            } catch (Throwable $e) {
                $saved = false;
                $saveError = $e->getMessage();
                cv_log('admin', 'Settings save failed', ['section' => $section, 'error' => $saveError]);
            }
            $saveMessage = $saved ? cv_t('messages.saved') : ($saveError ? 'Save failed: database error.' : 'Save failed. Please try again.');
            cv_flash($saved ? 'success' : 'error', $saveMessage);
            if ($isAjax) {
                cv_json_response([
                    'ok' => $saved,
                    'message' => $saveMessage,
                    'redirect' => cv_admin_url($section) . '?locale=' . $locale,
                ], $saved ? 200 : 500);
            }
            cv_redirect(cv_admin_url($section) . '?locale=' . $locale);

        case 'hero':
            $payload = $_POST;
            unset($payload['_token']);
            $saved = cv_admin_save_content_block($section, $payload, $locale);
            cv_flash($saved ? 'success' : 'error', $saved ? cv_t('messages.saved') : 'Save failed. Please try again.');
            if ($isAjax) {
                cv_json_response([
                    'ok' => $saved,
                    'message' => $saved ? cv_t('messages.saved') : 'Save failed. Please try again.',
                    'redirect' => cv_admin_url($section) . '?locale=' . $locale,
                ], $saved ? 200 : 500);
            }
            cv_redirect(cv_admin_url($section) . '?locale=' . $locale);

        case 'about':
            $payload = $_POST;
            unset($payload['_token']);

            // Preserve existing photo so switching locale doesn't erase it
            $existing = cv_get_block('about', $locale, []);
            if (!empty($existing['photo'])) {
                $payload['photo'] = $existing['photo'];
            }

            $photoFile = $_FILES['about_photo'] ?? null;
            if ($photoFile && (int) ($photoFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $photoResult = cv_process_image_upload($photoFile, 'about-photo');
                if ($photoResult['ok']) {
                    $payload['photo'] = $photoResult['path'];
                } else {
                    cv_flash('error', (string) ($photoResult['error'] ?? 'Photo upload failed.'));
                    cv_redirect(cv_admin_url('about') . '?locale=' . $locale);
                }
            }

            cv_admin_save_content_block('about', $payload, $locale);
            cv_flash('success', cv_t('messages.saved'));
            cv_redirect(cv_admin_url('about') . '?locale=' . $locale);

        case 'theme':
            $skillsIntro = [
                'label' => (string) cv_post('skills_label', ''),
                'title' => (string) cv_post('skills_title', ''),
                'text' => (string) cv_post('skills_text', ''),
            ];
            $servicesIntro = [
                'label' => (string) cv_post('services_label', ''),
                'title' => (string) cv_post('services_title', ''),
                'text' => (string) cv_post('services_text', ''),
            ];
            $portfolioIntro = [
                'label' => (string) cv_post('portfolio_label', ''),
                'title' => (string) cv_post('portfolio_title', ''),
                'text' => (string) cv_post('portfolio_text', ''),
            ];
            $practice = [
                'title' => (string) cv_post('practice_title', ''),
                'intro' => (string) cv_post('practice_intro', ''),
                'items' => [
                    ['title' => (string) cv_post('practice_item_1_title', ''), 'text' => (string) cv_post('practice_item_1_text', '')],
                    ['title' => (string) cv_post('practice_item_2_title', ''), 'text' => (string) cv_post('practice_item_2_text', '')],
                    ['title' => (string) cv_post('practice_item_3_title', ''), 'text' => (string) cv_post('practice_item_3_text', '')],
                ],
            ];
            $pricing = [
                'label' => (string) cv_post('pricing_label', ''),
                'title' => (string) cv_post('pricing_title', ''),
                'text' => (string) cv_post('pricing_text', ''),
                'plans' => [
                    [
                        'name' => (string) cv_post('pricing_plan_1_name', ''),
                        'price' => (string) cv_post('pricing_plan_1_price', ''),
                        'description' => (string) cv_post('pricing_plan_1_description', ''),
                        'features' => (string) cv_post('pricing_plan_1_features', ''),
                    ],
                    [
                        'name' => (string) cv_post('pricing_plan_2_name', ''),
                        'price' => (string) cv_post('pricing_plan_2_price', ''),
                        'description' => (string) cv_post('pricing_plan_2_description', ''),
                        'features' => (string) cv_post('pricing_plan_2_features', ''),
                    ],
                    [
                        'name' => (string) cv_post('pricing_plan_3_name', ''),
                        'price' => (string) cv_post('pricing_plan_3_price', ''),
                        'description' => (string) cv_post('pricing_plan_3_description', ''),
                        'features' => (string) cv_post('pricing_plan_3_features', ''),
                    ],
                ],
            ];
            $process = [
                'title' => (string) cv_post('process_title', ''),
                'steps' => [
                    ['title' => (string) cv_post('process_step_1_title', ''), 'text' => (string) cv_post('process_step_1_text', '')],
                    ['title' => (string) cv_post('process_step_2_title', ''), 'text' => (string) cv_post('process_step_2_text', '')],
                    ['title' => (string) cv_post('process_step_3_title', ''), 'text' => (string) cv_post('process_step_3_text', '')],
                    ['title' => (string) cv_post('process_step_4_title', ''), 'text' => (string) cv_post('process_step_4_text', '')],
                ],
            ];
            $testimonialsIntro = [
                'label' => (string) cv_post('testimonials_label', ''),
                'title' => (string) cv_post('testimonials_title', ''),
                'text' => (string) cv_post('testimonials_text', ''),
            ];
            $faqIntro = [
                'title' => (string) cv_post('faq_title', ''),
                'text' => (string) cv_post('faq_text', ''),
            ];
            $contact = [
                'title' => (string) cv_post('contact_title', ''),
                'text' => (string) cv_post('contact_text', ''),
                'success_title' => (string) cv_post('success_title', ''),
                'success_text' => (string) cv_post('success_text', ''),
            ];

            $saved = true;
            $saved = cv_admin_save_content_block('skills_intro', $skillsIntro, $locale) && $saved;
            $saved = cv_admin_save_content_block('services_intro', $servicesIntro, $locale) && $saved;
            $saved = cv_admin_save_content_block('portfolio_intro', $portfolioIntro, $locale) && $saved;
            $saved = cv_admin_save_content_block('practice', $practice, $locale) && $saved;
            $saved = cv_admin_save_content_block('pricing', $pricing, $locale) && $saved;
            $saved = cv_admin_save_content_block('process', $process, $locale) && $saved;
            $saved = cv_admin_save_content_block('testimonials_intro', $testimonialsIntro, $locale) && $saved;
            $saved = cv_admin_save_content_block('faq_intro', $faqIntro, $locale) && $saved;
            $saved = cv_admin_save_content_block('contact', $contact, $locale) && $saved;
            cv_flash($saved ? 'success' : 'error', $saved ? cv_t('messages.saved') : 'Save failed. Please try again.');
            if ($isAjax) {
                cv_json_response([
                    'ok' => $saved,
                    'message' => $saved ? cv_t('messages.saved') : 'Save failed. Please try again.',
                    'redirect' => cv_admin_url('theme') . '?locale=' . $locale,
                ], $saved ? 200 : 500);
            }
            cv_redirect(cv_admin_url('theme') . '?locale=' . $locale);

        case 'skills':
        case 'services':
        case 'faq':
        case 'categories':
            if ($action === 'delete' && $id > 0) {
                cv_admin_delete_entity($section, $id);
                cv_flash('success', cv_t('messages.deleted'));
                if ($isAjax) {
                    cv_json_response([
                        'ok' => true,
                        'message' => cv_t('messages.deleted'),
                        'redirect' => cv_admin_url($section),
                    ]);
                }
            } else {
                cv_admin_save_entity($section, $_POST, $id);
                cv_flash('success', cv_t('messages.saved'));
                if ($isAjax) {
                    cv_json_response([
                        'ok' => true,
                        'message' => cv_t('messages.saved'),
                        'redirect' => cv_admin_url($section),
                    ]);
                }
            }
            cv_redirect(cv_admin_url($section));

        case 'blog':
            if ($action === 'delete' && $id > 0) {
                cv_admin_delete_entity($section, $id);
                cv_flash('success', cv_t('messages.deleted'));
                if ($isAjax) {
                    cv_json_response([
                        'ok' => true,
                        'message' => cv_t('messages.deleted'),
                        'redirect' => cv_admin_url($section),
                    ]);
                }
            } else {
                $result = cv_admin_save_blog_post($_POST, $_FILES, $id);
                cv_flash(($result['ok'] ?? false) ? 'success' : 'error', (string) ($result['message'] ?? cv_t('messages.upload_failed')));

                foreach (($result['errors'] ?? []) as $error) {
                    cv_flash('error', (string) $error);
                }

                $redirect = cv_admin_url('blog' . (($result['post_id'] ?? 0) > 0 ? '/edit/' . (int) $result['post_id'] : ''));
                if ($isAjax) {
                    cv_json_response([
                        'ok' => (bool) ($result['ok'] ?? false),
                        'message' => (string) ($result['message'] ?? cv_t('messages.upload_failed')),
                        'errors' => $result['errors'] ?? [],
                        'redirect' => $redirect,
                    ], ($result['ok'] ?? false) ? 200 : 422);
                }
                cv_redirect($redirect);
            }
            cv_redirect(cv_admin_url($section));

        case 'testimonials':
            if ($action === 'delete' && $id > 0) {
                cv_admin_delete_entity($section, $id);
                cv_flash('success', cv_t('messages.deleted'));
                if ($isAjax) {
                    cv_json_response([
                        'ok' => true,
                        'message' => cv_t('messages.deleted'),
                        'redirect' => cv_admin_url($section),
                    ]);
                }
                cv_redirect(cv_admin_url($section));
            }

            $payload = $_POST;
            if (!empty($_FILES['avatar_image']) && ($_FILES['avatar_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $upload = cv_process_image_upload($_FILES['avatar_image'], 'testimonial-avatar');
                if (($upload['ok'] ?? false) === true) {
                    $payload['avatar'] = $upload['path'];
                } else {
                    cv_flash('error', 'Avatar image: ' . (string) ($upload['error'] ?? cv_t('messages.upload_failed')));
                }
            } else {
                $payload['avatar'] = (string) cv_post('existing_avatar', '');
            }

            cv_admin_save_entity($section, $payload, $id);
            cv_flash('success', cv_t('messages.saved'));
            if ($isAjax) {
                cv_json_response([
                    'ok' => true,
                    'message' => cv_t('messages.saved'),
                    'redirect' => cv_admin_url($section),
                ]);
            }
            cv_redirect(cv_admin_url($section));

        case 'projects':
            if ($action === 'delete' && $id > 0) {
                if (cv_delete_by_id('projects', $id)) {
                    cv_cache_flush();
                }
                cv_flash('success', cv_t('messages.deleted'));
                cv_redirect(cv_admin_url('projects'));
            }

            $result = cv_admin_save_project($_POST, $_FILES, $id);
            foreach ($result['errors'] as $message) {
                cv_flash('error', $message);
            }
            foreach ($result['warnings'] as $message) {
                cv_flash('warning', $message);
            }
            cv_flash($result['ok'] ? 'success' : 'error', (string) $result['message']);
            $projectRedirect = cv_admin_url('projects' . (($result['project_id'] ?? 0) > 0 ? '/edit/' . $result['project_id'] : ''));
            if ($isAjax) {
                cv_json_response([
                    'ok' => $result['ok'],
                    'message' => $result['message'],
                    'errors' => $result['errors'],
                    'warnings' => $result['warnings'],
                    'redirect' => $projectRedirect,
                ]);
            }
            cv_redirect($projectRedirect);

        case 'media':
            $uploadedCount = 0;
            $errors = [];
            if (!empty($_FILES['images'])) {
                foreach (cv_normalize_uploads($_FILES['images']) as $imageFile) {
                    if (($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }

                    $upload = cv_process_image_upload($imageFile, 'media-library');
                    if (($upload['ok'] ?? false) === true) {
                        $uploadedCount++;
                        continue;
                    }

                    $errors[] = (string) ($upload['error'] ?? cv_t('messages.upload_failed'));
                }
            }

            if ($uploadedCount > 0) {
                cv_flash('success', 'Uploaded images: ' . $uploadedCount);
            }
            if ($uploadedCount === 0 && $errors === []) {
                cv_flash('warning', 'No images were selected for upload.');
            }
            foreach ($errors as $message) {
                cv_flash('error', $message);
            }
            if ($isAjax) {
                cv_json_response([
                    'ok' => $uploadedCount > 0,
                    'message' => $uploadedCount > 0 ? 'Uploaded images: ' . $uploadedCount : 'No images were selected for upload.',
                    'errors' => $errors,
                    'redirect' => cv_admin_url('media'),
                ]);
            }
            cv_redirect(cv_admin_url('media'));

        case 'messages':
            if ($id > 0) {
                cv_admin_mark_message($id, (string) cv_post('status', 'replied'), cv_boolean(cv_post('is_read', '1')));
            }
            cv_flash('success', cv_t('messages.saved'));
            $messageUrl = cv_admin_url('messages/view/' . $id);
            $statusFilter = trim((string) cv_get('status', ''));
            if ($statusFilter !== '') {
                $messageUrl .= '?status=' . rawurlencode($statusFilter);
            }
            if ($isAjax) {
                cv_json_response([
                    'ok' => true,
                    'message' => cv_t('messages.saved'),
                    'redirect' => $messageUrl,
                ]);
            }
            cv_redirect($messageUrl);

        case 'backup':
            if ($action === 'export-json') {
                $path = cv_write_backup_file();
                cv_flash('success', 'Backup written: ' . basename($path));
                if ($isAjax) {
                    cv_json_response([
                        'ok' => true,
                        'message' => 'Backup written: ' . basename($path),
                        'redirect' => cv_admin_url('backup'),
                    ]);
                }
                cv_redirect(cv_admin_url('backup'));
            }

            if ($action === 'import-json' && !empty($_FILES['import_file']) && ($_FILES['import_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo((string) ($_FILES['import_file']['name'] ?? ''), PATHINFO_EXTENSION));
                if ($extension !== 'json') {
                    cv_flash('error', 'Please upload a JSON backup file.');
                    if ($isAjax) {
                        cv_json_response([
                            'ok' => false,
                            'message' => 'Please upload a JSON backup file.',
                            'redirect' => cv_admin_url('backup'),
                        ], 400);
                    }
                    cv_redirect(cv_admin_url('backup'));
                }

                $raw = @file_get_contents((string) $_FILES['import_file']['tmp_name']);
                $payload = is_string($raw) ? json_decode($raw, true) : null;
                $result = is_array($payload) ? cv_import_site_payload($payload) : ['ok' => false, 'message' => 'Invalid JSON payload.'];
                cv_flash($result['ok'] ? 'success' : 'error', (string) $result['message']);
                if ($isAjax) {
                    cv_json_response([
                        'ok' => $result['ok'],
                        'message' => (string) $result['message'],
                        'redirect' => cv_admin_url('backup'),
                    ], $result['ok'] ? 200 : 400);
                }
                cv_redirect(cv_admin_url('backup'));
            }
            cv_flash('warning', 'Choose a JSON backup file before importing.');
            if ($isAjax) {
                cv_json_response([
                    'ok' => false,
                    'message' => 'Choose a JSON backup file before importing.',
                    'redirect' => cv_admin_url('backup'),
                ], 400);
            }
            cv_redirect(cv_admin_url('backup'));
            break;

        case 'security':
            if ($action === 'password') {
                $result = cv_admin_update_password((string) cv_post('current_password', ''), (string) cv_post('new_password', ''));
                cv_flash($result['ok'] ? 'success' : 'error', (string) $result['message']);
                if ($isAjax) {
                    cv_json_response([
                        'ok' => $result['ok'],
                        'message' => (string) $result['message'],
                        'redirect' => cv_admin_url('security'),
                    ], $result['ok'] ? 200 : 400);
                }
                cv_redirect(cv_admin_url('security'));
            }
            $result = cv_admin_update_profile($_POST);
            cv_flash($result['ok'] ? 'success' : 'error', (string) $result['message']);
            if ($isAjax) {
                cv_json_response([
                    'ok' => $result['ok'],
                    'message' => (string) $result['message'],
                    'redirect' => cv_admin_url('security'),
                ], $result['ok'] ? 200 : 400);
            }
            cv_redirect(cv_admin_url('security'));
    }
}
