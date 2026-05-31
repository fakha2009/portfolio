<?php

declare(strict_types=1);

$projectsPage = $projectsPage ?? [];
$selectedCategory = $selectedCategory ?? 'all';
$portfolioIntro = $projectsPage['portfolio_intro'] ?? [];
$isRu = cv_current_locale() === 'ru';
$projectCount = count($projectsPage['projects'] ?? []);
$categoryCount = count($projectsPage['categories'] ?? []);
$countText = $isRu
    ? sprintf('%d опубликованных кейсов и concept-работ.', $projectCount)
    : sprintf('%d published case studies and concept projects.', $projectCount);
$noteText = $isRu
    ? 'Фокус на backend, API, web-системах и практичной продуктовой логике.'
    : 'Focused on backend, APIs, web systems, and practical product logic.';
$panelTitle = $isRu
    ? 'Каждый проект оформлен как структурный кейс, а не просто как визуальная витрина.'
    : 'Each project is presented as a structured case study, not just a visual tile.';
$statProjectLabel = $isRu ? 'кейсов' : 'case studies';
$statCategoryLabel = $isRu ? 'категории' : 'categories';
$supportPoints = $isRu
    ? ['Контекст и роль', 'Задача и решение', 'Стек и итог']
    : ['Context and role', 'Problem and solution', 'Stack and outcome'];
?>
<section class="section section--tight page-hero projects-page-hero">
    <div class="container page-hero-grid projects-page-hero-grid">
        <div class="section-head projects-hero-copy">
            <span class="eyebrow"><?= cv_e($portfolioIntro['label'] ?? 'Portfolio') ?></span>
            <h1><?= cv_e($portfolioIntro['title'] ?? '') ?></h1>
            <p><?= cv_e($portfolioIntro['text'] ?? '') ?></p>
        </div>
        <aside class="projects-hero-aside">
            <div class="page-hero-note projects-hero-note">
                <div class="projects-hero-note-head">
                    <strong><?= cv_e($countText) ?></strong>
                    <span><?= cv_e($noteText) ?></span>
                </div>
                <div class="projects-hero-stats" aria-label="<?= cv_e($isRu ? 'Краткая статистика проектов' : 'Project overview stats') ?>">
                    <div class="projects-hero-stat">
                        <strong><?= cv_e((string) $projectCount) ?></strong>
                        <span><?= cv_e($statProjectLabel) ?></span>
                    </div>
                    <div class="projects-hero-stat">
                        <strong><?= cv_e((string) $categoryCount) ?></strong>
                        <span><?= cv_e($statCategoryLabel) ?></span>
                    </div>
                </div>
                <div class="projects-hero-note-foot">
                    <p><?= cv_e($panelTitle) ?></p>
                    <div class="projects-hero-points">
                        <?php foreach ($supportPoints as $point): ?>
                            <span class="projects-hero-point"><?= cv_e($point) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>

<section class="section section--tight projects-browser-section">
    <div class="container">
        <div class="filters reveal" data-filter-row>
            <a class="filter <?= $selectedCategory === 'all' ? 'is-active' : '' ?>" href="<?= cv_url('projects') ?>" data-filter="all"><?= cv_e(cv_t('actions.all')) ?></a>
            <?php foreach ($projectsPage['categories'] as $category): ?>
                <a class="filter <?= $selectedCategory === $category['slug'] ? 'is-active' : '' ?>" href="<?= cv_url('projects') . '?category=' . urlencode((string) $category['slug']) ?>" data-filter="<?= cv_e($category['slug']) ?>"><?= cv_e(cv_localized_value($category, 'name')) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="proj-grid" data-project-grid>
            <?php foreach ($projectsPage['projects'] as $project): ?>
                <?php cv_partial('public/partials/project-card', ['project' => $project]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
