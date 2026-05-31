<?php

declare(strict_types=1);

function cv_site_settings(?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();

    return cv_cache_remember('settings:site:' . $locale, 60, static fn (): array => cv_admin_settings_group('site', $locale));
}

function cv_social_settings(?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();

    return cv_cache_remember('settings:social:' . $locale, 60, static fn (): array => cv_admin_settings_group('social', $locale));
}

function cv_seo_settings(?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();

    return cv_cache_remember('settings:seo:' . $locale, 60, static fn (): array => cv_admin_settings_group('seo', $locale));
}

function cv_block_defaults(string $key, ?string $locale = null): array
{
    $isRu = ($locale ?? cv_current_locale()) === 'ru';

    return match ($key) {
        'hero' => $isRu ? [
            'eyebrow' => 'Backend Developer • Go / PHP / Python',
            'headline' => 'Махмадхонзода Фахриддин',
            'subheadline' => 'Проектирую и собираю backend-логику, API и прикладные веб-системы, где важны надежность, ясная структура и спокойное развитие продукта.',
            'primary_cta' => 'Обсудить задачу',
            'secondary_cta' => 'Посмотреть кейсы',
            'metric_one_label' => 'Фокус',
            'metric_one_value' => 'Backend и API',
            'metric_two_label' => 'Стек',
            'metric_two_value' => 'Go / PHP / SQL',
            'metric_three_label' => 'Формат',
            'metric_three_value' => 'Web-системы и admin-панели',
            'panel_label' => 'Позиционирование',
            'panel_title' => 'Backend и web developer с упором на API, админ-панели, data flows и аккуратную реализацию прикладных систем.',
            'point_one' => 'Go, PHP, Python, PostgreSQL, MySQL, Redis',
            'point_two' => 'API, backend-логика, админ-панели, интеграции',
            'point_three' => 'От структуры данных до чистой выдачи интерфейса',
            'surface_one_label' => 'Основной стек',
            'surface_one_value' => 'Go, PHP, Python, PostgreSQL, MySQL, Redis',
            'surface_two_label' => 'Рабочий фокус',
            'surface_two_value' => 'API, dashboard-сценарии, бизнес-логика, внутренние инструменты',
            'surface_three_label' => 'Стиль реализации',
            'surface_three_value' => 'Поддерживаемый код, аккуратная структура, практичный результат',
        ] : [
            'eyebrow' => 'Backend Developer • Go / PHP / Python',
            'headline' => 'Makhmadkhonzoda Fakhriddin',
            'subheadline' => 'I design and build backend logic, APIs, and practical web systems where reliability, structure, and long-term maintainability matter.',
            'primary_cta' => 'Discuss your project',
            'secondary_cta' => 'View case studies',
            'metric_one_label' => 'Focus',
            'metric_one_value' => 'Backend & APIs',
            'metric_two_label' => 'Stack',
            'metric_two_value' => 'Go / PHP / SQL',
            'metric_three_label' => 'Format',
            'metric_three_value' => 'Web systems & admin panels',
            'panel_label' => 'Positioning',
            'panel_title' => 'Backend and web developer focused on APIs, admin panels, data flows, and well-structured applied systems.',
            'point_one' => 'Go, PHP, Python, PostgreSQL, MySQL, Redis',
            'point_two' => 'APIs, backend logic, admin panels, integrations',
            'point_three' => 'From data structure to clear product delivery',
            'surface_one_label' => 'Core stack',
            'surface_one_value' => 'Go, PHP, Python, PostgreSQL, MySQL, Redis',
            'surface_two_label' => 'Work focus',
            'surface_two_value' => 'APIs, dashboards, business logic, internal tooling',
            'surface_three_label' => 'Delivery style',
            'surface_three_value' => 'Maintainable code, careful structure, practical implementation',
        ],
        'about' => $isRu ? [
            'title' => 'Сильный backend-фокус без лишнего шума.',
            'body' => 'Мне близки задачи, где нужно не просто написать код, а выстроить понятную систему: продумать данные, API, логику сценариев, административную часть и устойчивость решения в реальной эксплуатации. Работаю на стыке backend-разработки, прикладных веб-систем и автоматизации, уделяя внимание читаемости, поддерживаемости и спокойной технической структуре проекта.',
            'highlight_one' => 'API, backend-логика и интеграции',
            'highlight_two' => 'PHP, Go, Python, SQL, Docker, Git',
            'highlight_three' => 'Решения, которые можно развивать, а не переписывать',
        ] : [
            'title' => 'A backend-focused profile built around clarity, systems thinking, and useful delivery.',
            'body' => 'I am most comfortable in projects where the goal is not just to write code, but to shape a clean system: data structure, API behavior, application logic, admin workflows, and maintainability in real use. My work sits at the intersection of backend development, practical web systems, and automation, with careful attention to readability, supportability, and technical calm.',
            'highlight_one' => 'APIs, backend logic, and integrations',
            'highlight_two' => 'PHP, Go, Python, SQL, Docker, Git',
            'highlight_three' => 'Solutions built to evolve, not to be rebuilt',
        ],
        'skills_intro' => $isRu ? [
            'label' => 'Навыки / стек',
            'title' => 'Технологический стек, собранный вокруг backend-разработки, API и прикладных веб-систем.',
            'text' => 'Здесь показаны не просто названия технологий, а тот рабочий набор, на котором строятся API, бизнес-логика, админ-панели, интеграции и data-driven сценарии.',
        ] : [
            'label' => 'Skills / stack',
            'title' => 'A technical stack shaped around backend development, APIs, and practical web systems.',
            'text' => 'This is more than a tool list. It is the working stack behind APIs, business logic, admin panels, integrations, and data-driven application flows.',
        ],
        'services_intro' => $isRu ? [
            'label' => 'Услуги',
            'title' => 'Практичные направления работы: от backend-слоя до готовой web-системы с управлением.',
            'text' => 'Фокус на тех задачах, где важны логика, структура, интеграции, формы, данные и аккуратная серверная реализация без лишней перегруженности.',
        ] : [
            'label' => 'Services',
            'title' => 'Practical delivery areas, from backend execution to complete web systems with control layers.',
            'text' => 'The focus is on work where logic, structure, integrations, forms, data handling, and careful server-side implementation matter.',
        ],
        'portfolio_intro' => $isRu ? [
            'label' => 'Портфолио',
            'title' => 'Подборка кейсов, где важны не шаблоны, а логика продукта, системное мышление и чистая реализация.',
            'text' => 'Ниже собраны live reference, demo и concept-проекты. Каждый кейс оформлен как инженерная история: контекст, роль, задача, решение и итог реализации.',
        ] : [
            'label' => 'Portfolio',
            'title' => 'A set of case studies shaped by product logic, systems thinking, and clean execution.',
            'text' => 'Below are live references, demo projects, and concept work. Each entry is framed as an engineering story with context, role, problem, solution, and implementation outcome.',
        ],
        'practice' => $isRu ? [
            'title' => 'Практика и прикладная разработка',
            'intro' => 'Мой рабочий вектор сегодня строится вокруг REST API, мини-сервисов, backend-логики, админ-панелей, связки фронтенда и бэкенда, а также небольших инструментов для автоматизации.',
            'items' => [
                [
                    'title' => 'REST API и backend-сценарии',
                    'text' => 'Прорабатываю маршруты, обработку запросов, модели данных, структуру ответов и поведение системы в типовых и граничных сценариях.',
                ],
                [
                    'title' => 'Базы данных и рабочая логика',
                    'text' => 'Работаю с PostgreSQL, MySQL и Redis, проектирую прикладные сценарии хранения, фильтрации и выдачи данных.',
                ],
                [
                    'title' => 'Утилиты и автоматизация',
                    'text' => 'Пишу небольшие решения на Go и Python, когда нужно убрать ручную рутину и сделать процесс понятнее и стабильнее.',
                ],
            ],
        ] : [
            'title' => 'Practice and applied development',
            'intro' => 'My current direction is centered on REST APIs, mini-services, backend logic, admin panels, frontend/backend interaction, and smaller tools for automation.',
            'items' => [
                [
                    'title' => 'REST APIs and backend flows',
                    'text' => 'I work through routing, request handling, data models, response structure, and predictable behavior across common and edge-case scenarios.',
                ],
                [
                    'title' => 'Databases and working logic',
                    'text' => 'I use PostgreSQL, MySQL, and Redis for practical storage, filtering, and data-serving workflows.',
                ],
                [
                    'title' => 'Utilities and automation',
                    'text' => 'I build small Go and Python tools whenever the goal is to remove manual repetition and make the process cleaner and more stable.',
                ],
            ],
        ],
        'education' => $isRu ? [
            'title' => 'Обучение и сфокусированная практика',
            'course_name' => 'Курс «Разработка на Golang»',
            'provider' => 'Alif Academy',
            'period' => 'Июнь 2025 — Октябрь 2025',
            'summary' => 'Прошел интенсив по Go, клиент-серверной архитектуре, базам данных, Docker и Git. В процессе учебной практики работал над REST API, мини-приложениями и финальным проектом.',
            'outcome_title' => 'Финальный результат',
            'outcome_text' => 'Финансовый трекер с регистрацией пользователей, транзакциями, фильтрацией, PostgreSQL и Docker как основой учебного case study.',
        ] : [
            'title' => 'Education and focused training',
            'course_name' => 'Golang Development Course',
            'provider' => 'Alif Academy',
            'period' => 'June 2025 — October 2025',
            'summary' => 'Completed focused training in Go, client-server architecture, databases, Docker, and Git. The practice phase included REST APIs, mini-applications, and a final case-study project.',
            'outcome_title' => 'Final outcome',
            'outcome_text' => 'A finance tracker with user registration, transactions, filtering, PostgreSQL, and Docker as the core study case.',
        ],
        'pricing' => $isRu ? [
            'label' => 'Тарифы и услуги',
            'title' => 'Форматы работы под разные задачи',
            'text' => 'Три понятных варианта для старта: небольшая задача, средний проект или индивидуальный объем с оценкой после обсуждения.',
            'plans' => [
                [
                    'name' => 'Начальная',
                    'price' => '$200',
                    'description' => 'Для небольших задач, лендинга, формы, правок, базовой интеграции или аккуратного улучшения существующего проекта.',
                    'features' => 'Анализ задачи; Реализация; Базовая проверка; Короткая инструкция',
                ],
                [
                    'name' => 'Средняя',
                    'price' => '$500',
                    'description' => 'Для мини-проекта с backend/API, админской логикой, несколькими связанными экранами или полноценным рабочим сценарием.',
                    'features' => 'Структура данных; Backend/API; Админка; Проверка сценариев',
                ],
                [
                    'name' => 'Договорная',
                    'price' => 'Договорная',
                    'description' => 'Для нестандартных систем, нескольких этапов, интеграций, поддержки или задач, где объем нужно оценить отдельно.',
                    'features' => 'Оценка объема; План работ; Поэтапная сдача; Поддержка',
                ],
            ],
        ] : [
            'label' => 'Pricing & services',
            'title' => 'Work formats for different project scopes',
            'text' => 'Three clear starting points: a small task, a standard project, or a custom scope estimated after discussion.',
            'plans' => [
                [
                    'name' => 'Starter',
                    'price' => '$200',
                    'description' => 'For small tasks, landing pages, forms, fixes, basic integrations, or careful improvements to an existing project.',
                    'features' => 'Scope review; Implementation; Basic QA; Short handoff note',
                ],
                [
                    'name' => 'Standard',
                    'price' => '$500',
                    'description' => 'For a mini-project with backend/API work, admin logic, several connected screens, or a complete working flow.',
                    'features' => 'Data structure; Backend/API; Admin flow; Scenario QA',
                ],
                [
                    'name' => 'Custom',
                    'price' => 'Negotiable',
                    'description' => 'For non-standard systems, staged work, integrations, support, or tasks where the scope needs a separate estimate.',
                    'features' => 'Scope estimate; Work plan; Milestone delivery; Support',
                ],
            ],
        ],
        'process' => $isRu ? [
            'title' => 'Спокойный инженерный процесс: понять задачу, собрать решение, довести до чистого состояния.',
            'steps' => [
                ['title' => 'Понять задачу', 'text' => 'Сначала разбираю цель, ограничения, логику данных и то, что действительно должно работать надежно.'],
                ['title' => 'Спроектировать решение', 'text' => 'Продумываю API, структуру данных, связку слоев и общую схему реализации до того, как проект начнет разрастаться.'],
                ['title' => 'Собрать аккуратно', 'text' => 'Реализую backend-логику, БД, формы, админские сценарии и нужный интерфейс без лишней технической перегрузки.'],
                ['title' => 'Уточнить и усилить', 'text' => 'После базовой реализации усиливаю валидацию, UX, детали интерфейса и общую понятность решения.'],
            ],
        ] : [
            'title' => 'A calm engineering process: understand the task, build the solution, and refine it into a cleaner result.',
            'steps' => [
                ['title' => 'Understand the task', 'text' => 'I start by clarifying the goal, the constraints, the data logic, and what truly needs to work reliably.'],
                ['title' => 'Design the solution', 'text' => 'I think through the API, data structure, layer interaction, and implementation shape before the project grows unnecessarily.'],
                ['title' => 'Build carefully', 'text' => 'I implement the backend logic, database layer, forms, admin workflows, and supporting UI without technical clutter.'],
                ['title' => 'Refine and strengthen', 'text' => 'Once the core flow is working, I improve validation, UX details, and the overall clarity of the solution.'],
            ],
        ],
        'testimonials_intro' => $isRu ? [
            'label' => 'Отзывы',
            'title' => 'Несколько коротких сигналов о стиле работы, аккуратности и техническом подходе.',
            'text' => 'Когда есть обратная связь, она помогает быстрее понять, как строится коммуникация, насколько спокойно идет реализация и как выглядит общий рабочий процесс.',
        ] : [
            'label' => 'Testimonials',
            'title' => 'A few short signals about communication style, technical care, and the way the work is delivered.',
            'text' => 'When feedback is available, it helps show how collaboration feels in practice and how carefully the implementation is handled.',
        ],
        'faq_intro' => $isRu ? [
            'title' => 'Коротко о стеке, формате работы и типах задач.',
            'text' => 'Здесь собраны самые практичные вопросы, которые обычно появляются перед началом работы над backend или web-проектом.',
        ] : [
            'title' => 'A quick view of the stack, collaboration format, and the kinds of tasks I take on.',
            'text' => 'This section covers the practical questions that usually come up before starting backend or web-oriented project work.',
        ],
        'contact' => $isRu ? [
            'title' => 'Нужен разработчик для backend, API или web-системы с админской логикой?',
            'text' => 'Если вам нужен разработчик для проектной работы, API, админ-панели, внутреннего инструмента или аккуратной серверной реализации, отправьте задачу и контекст. Дальше можно быстро понять объем, формат и ближайшие шаги.',
            'success_title' => 'Сообщение получено.',
            'success_text' => 'Я изучу запрос и вернусь с ответом, как только смогу.',
        ] : [
            'title' => 'Need a developer for backend work, API delivery, or a web system with an admin layer?',
            'text' => 'If you need support with project work, APIs, admin panels, internal tools, or a clean server-side implementation, send the task and context. From there, it is easy to clarify scope, format, and the next step.',
            'success_title' => 'Message received.',
            'success_text' => 'I will review the request and get back to you as soon as possible.',
        ],
        default => [],
    };
}

function cv_content_block(string $key, ?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();

    return cv_cache_remember('block:' . $key . ':' . $locale, 60, static fn (): array => array_replace(cv_block_defaults($key, $locale), cv_get_block($key, $locale, [])));
}

function cv_fetch_blocks_batch(array $keys, string $locale): array
{
    $defaultLocale = cv_default_locale();
    $rows = cv_fetch_all(
        'SELECT block_key, locale_code, payload_json FROM content_blocks
         WHERE block_key = ANY(:keys) AND (locale_code = :locale OR locale_code = :default OR locale_code = \'*\')
         ORDER BY CASE WHEN locale_code = :locale THEN 0 WHEN locale_code = :default THEN 1 ELSE 2 END ASC',
        ['keys' => '{' . implode(',', $keys) . '}', 'locale' => $locale, 'default' => $defaultLocale]
    );

    $map = [];
    foreach ($rows as $row) {
        $key = (string) $row['block_key'];
        if (!isset($map[$key])) {
            $decoded = json_decode((string) $row['payload_json'], true);
            $map[$key] = is_array($decoded) ? $decoded : [];
        }
    }

    return $map;
}

function cv_fetch_settings_batch(array $groups, string $locale): array
{
    $defaultLocale = cv_default_locale();
    $rows = cv_fetch_all(
        'SELECT group_key, setting_key, locale_code, value_long, value_type FROM site_settings
         WHERE group_key = ANY(:groups) AND (locale_code = :locale OR locale_code = :default OR locale_code = \'*\')
         ORDER BY CASE WHEN locale_code = :locale THEN 0 WHEN locale_code = :default THEN 1 ELSE 2 END ASC',
        ['groups' => '{' . implode(',', $groups) . '}', 'locale' => $locale, 'default' => $defaultLocale]
    );

    $map = [];
    foreach ($rows as $row) {
        $g = (string) $row['group_key'];
        $k = (string) $row['setting_key'];
        if (!isset($map[$g][$k])) {
            $map[$g][$k] = cv_setting_value((string) $row['value_long'], (string) $row['value_type']);
        }
    }

    return $map;
}

function cv_homepage_data(?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();

    return cv_cache_remember('homepage:' . $locale, 60, static function () use ($locale): array {

    // Batch: all content blocks in 1 query instead of ~10
    $blockKeys = ['hero', 'about', 'skills_intro', 'services_intro', 'portfolio_intro',
                  'practice', 'pricing', 'process', 'testimonials_intro',
                  'faq_intro', 'contact'];
    $rawBlocks = cv_fetch_blocks_batch($blockKeys, $locale);
    $blocks = [];
    foreach ($blockKeys as $k) {
        $blocks[$k] = array_replace(cv_block_defaults($k, $locale), $rawBlocks[$k] ?? []);
    }

    // Batch: all settings groups in 1 query instead of ~3
    $rawSettings = cv_fetch_settings_batch(['site', 'social', 'seo'], $locale);
    $site   = array_replace(cv_admin_default_site_settings($locale)['site']   ?? [], $rawSettings['site']   ?? []);
    $social = array_replace(cv_admin_default_site_settings($locale)['social'] ?? [], $rawSettings['social'] ?? []);
    $seo    = array_replace(cv_admin_default_site_settings($locale)['seo']    ?? [], $rawSettings['seo']    ?? []);

    $skills   = cv_fetch_skills(true);
    $skillGroups = cv_grouped_skills($skills, $locale);
    $services = cv_fetch_services(true);
    $projects = cv_fetch_projects(['published_only' => true]);

    $featuredProject = null;
    foreach ($projects as $project) {
        if ((int) ($project['featured'] ?? 0) === 1) {
            $featuredProject = $project;
            break;
        }
    }
    if ($featuredProject === null) {
        $featuredProject = $projects[0] ?? null;
    }

    return [
        'site'               => $site,
        'social'             => $social,
        'seo'                => $seo,
        'hero'               => $blocks['hero'],
        'about'              => $blocks['about'],
        'skills_intro'       => $blocks['skills_intro'],
        'services_intro'     => $blocks['services_intro'],
        'portfolio_intro'    => $blocks['portfolio_intro'],
        'practice'           => $blocks['practice'],
        'pricing'            => $blocks['pricing'],
        'process'            => $blocks['process'],
        'testimonials_intro' => $blocks['testimonials_intro'],
        'faq_intro'          => $blocks['faq_intro'],
        'contact_block'      => $blocks['contact'],
        'skills'             => $skills,
        'skill_groups'       => $skillGroups,
        'services'           => $services,
        'projects'           => $projects,
        'featured_project'   => $featuredProject,
        'project_count'      => count($projects),
        'service_count'      => count($services),
        'skill_group_count'  => count($skillGroups),
        'categories'         => cv_fetch_categories(true),
        'testimonials'       => cv_fetch_testimonials(true),
        'faqs'               => cv_fetch_faqs(true),
    ];
    });
}

function cv_project_page_data(array $project, ?string $locale = null): array
{
    $locale = $locale ?? cv_current_locale();

    return [
        'site' => cv_site_settings($locale),
        'social' => cv_social_settings($locale),
        'seo' => cv_seo_settings($locale),
        'project' => $project,
        'gallery' => cv_fetch_project_images((int) $project['id']),
        'video' => cv_fetch_project_video((int) $project['id']),
        'related_projects' => array_filter(
            cv_fetch_projects(['published_only' => true]),
            static fn (array $item): bool => (int) $item['id'] !== (int) $project['id']
        ),
    ];
}
