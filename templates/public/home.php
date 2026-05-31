<?php

declare(strict_types=1);

$home = $home ?? cv_homepage_data();
$site = $home['site'];
$hero = $home['hero'];
$about = $home['about'];
$skillsIntro = $home['skills_intro'];
$servicesIntro = $home['services_intro'];
$portfolioIntro = $home['portfolio_intro'];
$practice = $home['practice'];
$pricing = $home['pricing'];
$process = $home['process'];
$faqIntro = $home['faq_intro'];
$contactBlock = $home['contact_block'];
$flashes = cv_consume_flashes();
$featuredProject = $home['featured_project'] ?? null;
$gridProjects = $home['projects'];
$isRu = cv_current_locale() === 'ru';

if ($featuredProject) {
    $gridProjects = array_values(array_filter(
        $gridProjects,
        static fn (array $item): bool => (int) $item['id'] !== (int) $featuredProject['id']
    ));
}
if ($gridProjects === []) {
    $gridProjects = $home['projects'];
}

$heroTitle = trim((string) ($hero['headline'] ?? ''));
if ($heroTitle === '' || $heroTitle === ($site['site_name'] ?? '')) {
    $heroTitle = $isRu ? 'Backend, который держит продукт.' : 'Backend that holds the product together.';
}

$heroSignals = $isRu
    ? ['API и backend-логика', 'Админ-панели и бизнес-системы', 'Автоматизация и внутренние инструменты']
    : ['API and backend logic', 'Admin panels and business systems', 'Automation and internal tooling'];
$proofCards = [
    ['value' => (string) (int) ($home['project_count'] ?? 0), 'label' => $isRu ? 'Кейсов и demo-проектов с акцентом на backend' : 'Backend-focused case studies and demos'],
    ['value' => (string) (int) ($home['service_count'] ?? 0), 'label' => $isRu ? 'Практических направлений работы' : 'Practical areas of work'],
    ['value' => (string) (int) ($home['skill_group_count'] ?? 0), 'label' => $isRu ? 'Системных групп навыков и технологий' : 'System groups of skills and technologies'],
];
$aboutParagraphs = array_filter(array_map('trim', preg_split('/\n+|\.\s+/u', (string) ($about['body'] ?? ''), 3)));
$aboutHighlights = array_filter([
    $about['highlight_one'] ?? '',
    $about['highlight_two'] ?? '',
    $about['highlight_three'] ?? '',
]);
$coreStack = $hero['surface_one_value'] ?? 'Go, PHP, Python, PostgreSQL, MySQL, Redis';
$workFocus = $hero['surface_two_value'] ?? ($isRu ? 'API, dashboard-сценарии, бизнес-логика, внутренние инструменты' : 'APIs, dashboards, business logic, internal tooling');
$deliveryStyle = $hero['surface_three_value'] ?? ($isRu ? 'Поддерживаемый код, аккуратная структура, практичный результат' : 'Maintainable code, careful structure, practical implementation');
$pricingPlans = array_values(array_filter(
    (array) ($pricing['plans'] ?? []),
    static fn ($plan): bool => is_array($plan) && (trim((string) ($plan['name'] ?? '')) !== '' || trim((string) ($plan['price'] ?? '')) !== '')
));
?>

<section class="section hero">
    <div class="container">
        <div class="hero__grid">
            <div class="hero__col">
                <span class="hero__badge reveal"><span class="dot"></span><span><?= cv_e($hero['eyebrow'] ?? ($isRu ? 'Открыт к проектной работе' : 'Open to project work')) ?></span></span>
                <h1 class="reveal" data-d="1"><?= cv_e($heroTitle) ?></h1>
                <p class="hero__lead reveal" data-d="2"><?= cv_e($hero['subheadline'] ?? '') ?></p>
                <div class="hero__cta reveal" data-d="3">
                    <a href="#contact" class="btn btn--primary"><?= cv_e($hero['primary_cta'] ?? cv_t('actions.contact_me')) ?></a>
                    <a href="#work" class="btn btn--ghost"><?= cv_e($hero['secondary_cta'] ?? cv_t('actions.view_case')) ?><span class="ar">→</span></a>
                </div>
                <div class="hero__tags reveal" data-d="4">
                    <?php foreach ($heroSignals as $signal): ?>
                        <span class="tag"><?= cv_e($signal) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="hero__visual reveal" data-d="2">
                <div class="code-card">
                    <div class="code-card__bar">
                        <span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span>
                        <span class="code-card__file">finance/handler.go</span>
                    </div>
<pre><span class="ln">01</span><span class="tk-key">package</span> finance
<span class="ln">02</span>
<span class="ln">03</span><span class="tk-cm">// GET /api/transactions</span>
<span class="ln">04</span><span class="tk-key">func</span> (h *<span class="tk-typ">Handler</span>) <span class="tk-fn">Transactions</span>(w, r) {
<span class="ln">05</span>    uid := <span class="tk-fn">auth.UserID</span>(r.Context())
<span class="ln">06</span>    items, err := h.repo.<span class="tk-fn">List</span>(uid, <span class="tk-fn">filter</span>(r))
<span class="ln">07</span>    <span class="tk-key">if</span> err != <span class="tk-key">nil</span> {
<span class="ln">08</span>        respond.<span class="tk-fn">Error</span>(w, <span class="tk-num">500</span>, err)
<span class="ln">09</span>        <span class="tk-key">return</span>
<span class="ln">10</span>    }
<span class="ln">11</span>    respond.<span class="tk-fn">JSON</span>(w, <span class="tk-num">200</span>, items)
<span class="ln">12</span>}</pre>
                    <div class="code-card__glow"></div>
                </div>
            </div>
        </div>

        <div class="statstrip reveal">
            <?php foreach ($proofCards as $card): ?>
                <div class="stat">
                    <div class="stat__num"><span data-count="<?= cv_e($card['value']) ?>"><?= cv_e($card['value']) ?></span></div>
                    <div class="stat__label"><?= cv_e($card['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="panel posn reveal">
            <div class="posn__left">
                <span class="eyebrow"><?= cv_e($hero['panel_label'] ?? ($isRu ? 'Позиционирование' : 'Positioning')) ?></span>
                <p class="posn__lead"><?= cv_e($hero['panel_title'] ?? '') ?></p>
                <ul class="posn__list">
                    <?php foreach ([$hero['point_one'] ?? '', $hero['point_two'] ?? '', $hero['point_three'] ?? ''] as $point): ?>
                        <?php if (trim((string) $point) !== ''): ?>
                            <li><?= cv_e((string) $point) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="posn__right">
                <div class="kv"><span class="kv__k"><?= cv_e($hero['surface_one_label'] ?? ($isRu ? 'Основной стек' : 'Core stack')) ?></span><span class="kv__v"><?= cv_e($coreStack) ?></span></div>
                <div class="kv"><span class="kv__k"><?= cv_e($hero['surface_two_label'] ?? ($isRu ? 'Рабочий фокус' : 'Working focus')) ?></span><span class="kv__v"><?= cv_e($workFocus) ?></span></div>
                <div class="kv"><span class="kv__k"><?= cv_e($hero['surface_three_label'] ?? ($isRu ? 'Стиль реализации' : 'Implementation style')) ?></span><span class="kv__v"><?= cv_e($deliveryStyle) ?></span></div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="container">
        <div class="about__grid">
            <div class="about__body">
                <span class="eyebrow reveal"><?= cv_e(cv_t('nav.about')) ?></span>
                <h2 class="reveal" data-d="1"><?= cv_e($about['title'] ?? '') ?></h2>
                <?php foreach ($aboutParagraphs as $index => $paragraph): ?>
                    <p class="reveal" data-d="2"><?= cv_e(rtrim($paragraph, '.') . '.') ?></p>
                <?php endforeach; ?>
                <dl class="about__meta reveal" data-d="3">
                    <?php foreach ($aboutHighlights as $index => $highlight): ?>
                        <div><dt><?= cv_e($isRu ? ['Фокус', 'Стек', 'Принцип'][$index] ?? 'Факт' : ['Focus', 'Stack', 'Principle'][$index] ?? 'Fact') ?></dt><dd><?= cv_e((string) $highlight) ?></dd></div>
                    <?php endforeach; ?>
                    <div><dt><?= cv_e($isRu ? 'Локация' : 'Location') ?></dt><dd><?= cv_e($site['location'] ?? ($isRu ? 'Гиссар, Таджикистан' : 'Hisor, Tajikistan')) ?></dd></div>
                </dl>
            </div>
            <div class="portrait reveal" data-d="2">
                <div class="portrait__frame">
                    <?php $aboutPhoto = trim((string) ($about['photo'] ?? '')); ?>
                    <?php if ($aboutPhoto !== ''): ?>
                        <img src="<?= cv_e($aboutPhoto) ?>"
                             alt="<?= cv_e($site['site_name'] ?? 'Fakhriddin') ?>"
                             style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block"
                             loading="lazy">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:var(--ink-2,#4C443C);background:var(--accent-soft,#FBEADF);border-radius:inherit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;opacity:.4"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span style="font-size:12px;opacity:.5"><?= cv_e($isRu ? 'Добавьте фото в Admin → About' : 'Add photo in Admin → About') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="portrait__card">
                    <span class="portrait__avatar">FM</span>
                    <span>
                        <b><?= cv_e($site['site_name'] ?? 'Fakhriddin') ?></b>
                        <span><?= cv_e($isRu ? 'Backend Engineer' : 'Backend Engineer') ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="skills">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow reveal"><?= cv_e($skillsIntro['label'] ?? ($isRu ? 'Навыки / стек' : 'Skills / stack')) ?></span>
            <h2 class="reveal" data-d="1"><?= cv_e($skillsIntro['title'] ?? '') ?></h2>
            <p class="reveal" data-d="2"><?= cv_e($skillsIntro['text'] ?? '') ?></p>
        </div>
        <div class="skillgroups">
            <?php $groupIndex = 1; ?>
            <?php foreach ($home['skill_groups'] as $groupLabel => $items): ?>
                <article class="skillgroup reveal">
                    <button class="skillgroup__head" type="button">
                        <span class="skillgroup__idx"><?= cv_e(str_pad((string) $groupIndex, 2, '0', STR_PAD_LEFT)) ?></span>
                        <span class="skillgroup__title"><?= cv_e((string) $groupLabel) ?></span>
                        <span class="skillgroup__meta">
                            <span class="skillgroup__chips">
                                <?php foreach (array_slice($items, 0, 4) as $skill): ?>
                                    <span><?= cv_e(cv_localized_value($skill, 'title')) ?></span>
                                <?php endforeach; ?>
                            </span>
                            <span class="skillgroup__toggle">+</span>
                        </span>
                    </button>
                    <div class="skillgroup__body">
                        <div class="skillgroup__inner">
                            <div class="skillgroup__cards">
                                <?php foreach (array_slice($items, 0, 3) as $skill): ?>
                                    <article class="skillcard">
                                        <span class="skillcard__tag"><?= cv_e((string) ($skill['level_label'] ?? 'Core')) ?></span>
                                        <h4><?= cv_e(cv_localized_value($skill, 'title')) ?></h4>
                                        <p><?= cv_e(cv_localized_value($skill, 'description')) ?></p>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </article>
                <?php $groupIndex++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow reveal"><?= cv_e($servicesIntro['label'] ?? cv_t('nav.services')) ?></span>
            <h2 class="reveal" data-d="1"><?= cv_e($servicesIntro['title'] ?? '') ?></h2>
            <p class="reveal" data-d="2"><?= cv_e($servicesIntro['text'] ?? '') ?></p>
        </div>
        <div class="services__list reveal">
            <?php foreach ($home['services'] as $index => $service): ?>
                <article class="service">
                    <span class="service__idx"><?= cv_e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                    <h3 class="service__title"><?= cv_e(cv_localized_value($service, 'title')) ?></h3>
                    <p class="service__desc"><?= cv_e(cv_localized_value($service, 'description')) ?></p>
                    <a class="service__tag" href="#contact"><?= cv_e(cv_localized_value($service, 'cta') ?: cv_t('actions.contact_me')) ?><span class="ar">→</span></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="work">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow reveal"><?= cv_e($portfolioIntro['label'] ?? 'Portfolio') ?></span>
            <h2 class="reveal" data-d="1"><?= cv_e($portfolioIntro['title'] ?? '') ?></h2>
            <p class="reveal" data-d="2"><?= cv_e($portfolioIntro['text'] ?? '') ?></p>
        </div>

        <?php if ($featuredProject): ?>
            <article class="featured reveal">
                <a class="featured__media" href="<?= cv_url('projects/' . $featuredProject['slug']) ?>">
                    <?php if (!empty($featuredProject['cover_image'])): ?>
                        <img src="<?= cv_e(cv_upload_url((string) $featuredProject['cover_image'])) ?>" alt="<?= cv_e(cv_localized_value($featuredProject, 'cover_alt')) ?>" loading="lazy">
                    <?php endif; ?>
                    <span class="featured__badge"><?= cv_e($isRu ? 'Выделенный кейс' : 'Spotlight case') ?></span>
                </a>
                <div class="featured__body">
                    <span class="featured__role"><?= cv_e(cv_localized_value($featuredProject, 'role')) ?></span>
                    <h3><?= cv_e(cv_localized_value($featuredProject, 'title')) ?></h3>
                    <p><?= cv_e(cv_localized_value($featuredProject, 'short_description')) ?></p>
                    <div class="featured__tags">
                        <?php foreach (array_slice(array_filter(array_map('trim', explode(',', (string) ($featuredProject['technologies'] ?? '')))), 0, 4) as $tech): ?>
                            <span><?= cv_e($tech) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <a class="featured__link" href="<?= cv_url('projects/' . $featuredProject['slug']) ?>"><?= cv_e(cv_t('actions.view_case')) ?><span class="ar">→</span></a>
                </div>
            </article>
        <?php endif; ?>

        <div class="filters reveal">
            <button class="filter is-active" type="button" data-filter="all"><?= cv_e(cv_t('actions.all')) ?></button>
            <?php foreach ($home['categories'] as $category): ?>
                <button class="filter" type="button" data-filter="<?= cv_e((string) $category['slug']) ?>"><?= cv_e(cv_localized_value($category, 'name')) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="proj-grid">
            <?php foreach ($gridProjects as $project): ?>
                <?php cv_partial('public/partials/project-card', ['project' => $project]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow reveal"><?= cv_e($isRu ? 'Процесс' : 'Process') ?></span>
            <h2 class="reveal" data-d="1"><?= cv_e($process['title'] ?? '') ?></h2>
        </div>
        <div class="process-grid">
            <?php foreach (($process['steps'] ?? []) as $index => $step): ?>
                <article class="step reveal">
                    <div class="step__num"><?= cv_e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></div>
                    <h3><?= cv_e((string) ($step['title'] ?? '')) ?></h3>
                    <p><?= cv_e((string) ($step['text'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--tight" id="pricing">
    <div class="container">
        <div class="pricing reveal">
            <div class="pricing__head">
                <span class="eyebrow"><?= cv_e((string) ($pricing['label'] ?? ($isRu ? 'Тарифы и услуги' : 'Pricing & services'))) ?></span>
                <h2><?= cv_e((string) ($pricing['title'] ?? '')) ?></h2>
                <p><?= cv_e((string) ($pricing['text'] ?? '')) ?></p>
            </div>
            <?php if ($pricingPlans !== []): ?>
                <div class="pricing__grid">
                    <?php foreach ($pricingPlans as $index => $plan): ?>
                        <?php
                        $features = array_filter(array_map(
                            'trim',
                            preg_split('/\r\n|\r|\n|;/', (string) ($plan['features'] ?? '')) ?: []
                        ));
                        ?>
                        <article class="price-card <?= $index === 1 ? 'is-featured' : '' ?>">
                            <div class="price-card__top">
                                <span class="price-card__idx"><?= cv_e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                                <h3><?= cv_e((string) ($plan['name'] ?? '')) ?></h3>
                            </div>
                            <div class="price-card__price"><?= cv_e((string) ($plan['price'] ?? '')) ?></div>
                            <p><?= cv_e((string) ($plan['description'] ?? '')) ?></p>
                            <?php if ($features !== []): ?>
                                <ul class="price-card__features">
                                    <?php foreach ($features as $feature): ?>
                                        <li><?= cv_e($feature) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <a class="btn btn--primary btn--sm" href="#contact"><?= cv_e(cv_t('actions.contact_me')) ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="faq">
    <div class="container">
        <div class="faq__grid">
            <div class="section-head">
                <span class="eyebrow reveal">FAQ</span>
                <h2 class="reveal" data-d="1"><?= cv_e($faqIntro['title'] ?? '') ?></h2>
                <p class="reveal" data-d="2"><?= cv_e($faqIntro['text'] ?? '') ?></p>
            </div>
            <div class="faq__list reveal">
                <?php foreach ($home['faqs'] as $index => $faq): ?>
                    <div class="faq__item <?= $index === 0 ? 'is-open' : '' ?>">
                        <button class="faq__q" type="button"><span><?= cv_e(cv_localized_value($faq, 'question')) ?></span><span class="ic"></span></button>
                        <div class="faq__a"><div class="faq__a-inner"><p><?= cv_e(cv_localized_value($faq, 'answer')) ?></p></div></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section" id="contact">
    <div class="container">
        <div class="section-head section-head--center contact-heading">
            <span class="eyebrow reveal"><?= cv_e(cv_t('nav.contact')) ?></span>
            <h2 class="reveal" data-d="1"><?= cv_e($contactBlock['title'] ?? '') ?></h2>
        </div>
        <div class="contact-wrap reveal">
            <div class="contact-info">
                <span class="eyebrow"><?= cv_e($isRu ? 'Связаться' : 'Get in touch') ?></span>
                <h2><?= cv_e($isRu ? 'Открыт к сотрудничеству и сильным прикладным задачам.' : 'Open to collaboration and strong applied work.') ?></h2>
                <p><?= cv_e($contactBlock['text'] ?? '') ?></p>
                <div class="contact-list">
                    <a href="mailto:<?= cv_e($site['contact_email'] ?? 'fakhridinkon2009@gmail.com') ?>"><span class="ck">Email</span><span><?= cv_e($site['contact_email'] ?? 'fakhridinkon2009@gmail.com') ?></span></a>
                    <a href="<?= cv_e($home['social']['phone'] ?? 'tel:+992881845151') ?>"><span class="ck"><?= cv_e($isRu ? 'Телефон' : 'Phone') ?></span><span><?= cv_e($site['contact_phone'] ?? '+992 881 845 151') ?></span></a>
                    <a href="<?= cv_e($home['social']['telegram'] ?? 'https://t.me/Fakhriddin_dev') ?>" target="_blank" rel="noopener"><span class="ck">Telegram</span><span><?= cv_e($site['contact_telegram'] ?? '@Fakhriddin_dev') ?></span></a>
                    <div><span class="ck"><?= cv_e($isRu ? 'Локация' : 'Location') ?></span><span><?= cv_e($site['location'] ?? '') ?></span></div>
                </div>
                <?php foreach ($flashes as $flash): ?>
                    <div class="flash flash-<?= cv_e($flash['type']) ?>"><?= cv_e($flash['message']) ?></div>
                <?php endforeach; ?>
            </div>
            <?php cv_partial('public/partials/contact-form'); ?>
        </div>
    </div>
</section>
