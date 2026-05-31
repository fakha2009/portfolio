<?php

declare(strict_types=1);

$project = $project ?? [];
$gallery = $gallery ?? [];
$video = $video ?? null;
$relatedProjects = array_slice($related_projects ?? [], 0, 3);
$projectRole = cv_localized_value($project, 'role');
$projectCategory = cv_localized_value([
    'name_ru' => $project['category_name_ru'] ?? '',
    'name_en' => $project['category_name_en'] ?? '',
], 'name');
$techList = array_filter(array_map('trim', explode(',', (string) ($project['technologies'] ?? ''))));
?>
<section class="project-hero">
    <div class="container project-hero-grid">
        <div class="project-copy reveal">
            <a class="breadcrumb" href="<?= cv_url('projects') ?>">&larr; <?= cv_e(cv_t('project.back_to_projects')) ?></a>
            <div class="section-kicker"><?= cv_e($projectCategory) ?></div>
            <h1><?= cv_e(cv_localized_value($project, 'title')) ?></h1>
            <p><?= cv_e(cv_localized_value($project, 'short_description')) ?></p>

            <div class="project-fact-strip">
                <?php if ($projectRole !== ''): ?>
                    <span class="fact-pill"><strong><?= cv_e(cv_t('project.role')) ?>:</strong> <?= cv_e($projectRole) ?></span>
                <?php endif; ?>
                <?php if ($projectCategory !== ''): ?>
                    <span class="fact-pill"><strong><?= cv_e(cv_t('project.category')) ?>:</strong> <?= cv_e($projectCategory) ?></span>
                <?php endif; ?>
                <?php if ($techList !== []): ?>
                    <span class="fact-pill"><strong><?= cv_e(cv_t('project.stack')) ?>:</strong> <?= cv_e(implode(', ', array_slice($techList, 0, 3))) ?></span>
                <?php endif; ?>
            </div>

            <?php if ($techList !== []): ?>
                <div class="chip-row">
                    <?php foreach ($techList as $tech): ?>
                        <span class="pill"><?= cv_e($tech) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['external_url'])): ?>
                <div class="hero-actions">
                    <a class="btn btn--primary" href="<?= cv_url('go/' . $project['slug']) ?>" target="_blank" rel="noopener noreferrer"><?= cv_e(cv_t('project.open_project')) ?></a>
                </div>
            <?php endif; ?>
        </div>
        <div class="project-cover reveal">
            <?php if (!empty($project['cover_image'])): ?>
                <img src="<?= cv_e(cv_upload_url((string) $project['cover_image'])) ?>" alt="<?= cv_e(cv_localized_value($project, 'cover_alt')) ?>" loading="eager">
            <?php else: ?>
                <div class="project-cover-fallback"></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container detail-grid project-detail-grid">
        <article class="detail-card reveal">
            <div class="section-kicker"><?= cv_e(cv_t('project.overview')) ?></div>
            <div class="rich-text"><?= cv_text_html(cv_localized_value($project, 'full_description')) ?></div>
        </article>

        <aside class="detail-card reveal project-facts-card">
            <div class="fact-block">
                <span><?= cv_e(cv_t('project.role')) ?></span>
                <strong><?= cv_e($projectRole !== '' ? $projectRole : '—') ?></strong>
            </div>
            <div class="fact-block">
                <span><?= cv_e(cv_t('project.category')) ?></span>
                <strong><?= cv_e($projectCategory !== '' ? $projectCategory : '—') ?></strong>
            </div>
            <div class="fact-block">
                <span><?= cv_e(cv_t('project.stack')) ?></span>
                <strong><?= cv_e($techList !== [] ? implode(', ', $techList) : '—') ?></strong>
            </div>
            <?php if (!empty($project['external_url'])): ?>
                <div class="fact-block">
                    <span><?= cv_e(cv_t('project.main_link')) ?></span>
                    <a class="text-link" href="<?= cv_url('go/' . $project['slug']) ?>" target="_blank" rel="noopener noreferrer"><?= cv_e(cv_t('project.open_project')) ?></a>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</section>

<section class="section section-tight">
    <div class="container story-grid">
        <article class="story-card reveal">
            <div class="section-kicker compact"><?= cv_e(cv_t('project.client')) ?></div>
            <div class="rich-text"><?= cv_text_html(cv_localized_value($project, 'client')) ?></div>
        </article>
        <article class="story-card reveal">
            <div class="section-kicker compact"><?= cv_e(cv_t('project.problem')) ?></div>
            <div class="rich-text"><?= cv_text_html(cv_localized_value($project, 'problem')) ?></div>
        </article>
        <article class="story-card reveal">
            <div class="section-kicker compact"><?= cv_e(cv_t('project.process')) ?></div>
            <div class="rich-text"><?= cv_text_html(cv_localized_value($project, 'process')) ?></div>
        </article>
        <article class="story-card reveal">
            <div class="section-kicker compact"><?= cv_e(cv_t('project.solution')) ?></div>
            <div class="rich-text"><?= cv_text_html(cv_localized_value($project, 'solution')) ?></div>
        </article>
        <article class="story-card reveal story-card-wide">
            <div class="section-kicker compact"><?= cv_e(cv_t('project.result')) ?></div>
            <div class="rich-text"><?= cv_text_html(cv_localized_value($project, 'result')) ?></div>
        </article>
    </div>
</section>

<?php if ($video): ?>
<section class="section media-section">
    <div class="container">
        <div class="section-head reveal">
            <div class="section-kicker"><?= cv_e(cv_t('project.video')) ?></div>
            <h2><?= cv_e(cv_t('project.video_title')) ?></h2>
        </div>
        <div class="video-shell reveal">
            <video controls preload="metadata" playsinline poster="<?= cv_e($video['poster_image'] ? cv_upload_url((string) $video['poster_image']) : '') ?>">
                <source src="<?= cv_e((string) $video['secure_url']) ?>" type="video/<?= cv_e((string) ($video['format'] ?? 'mp4')) ?>">
            </video>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($gallery !== []): ?>
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <div class="section-kicker"><?= cv_e(cv_t('project.gallery')) ?></div>
            <h2><?= cv_e(cv_t('project.gallery_title')) ?></h2>
        </div>
        <div class="gallery-grid">
            <?php foreach ($gallery as $image): ?>
                <figure class="gallery-item reveal">
                    <img src="<?= cv_e(cv_upload_url((string) $image['file_path'])) ?>" alt="<?= cv_e(cv_localized_value($image, 'alt')) ?>" loading="lazy">
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($relatedProjects !== []): ?>
<section class="section muted-section">
    <div class="container">
        <div class="section-head reveal">
            <div class="section-kicker"><?= cv_e(cv_t('project.more_work')) ?></div>
            <h2><?= cv_e(cv_t('project.more_work_title')) ?></h2>
        </div>
        <div class="project-grid">
            <?php foreach ($relatedProjects as $project): ?>
                <?php cv_partial('public/partials/project-card', ['project' => $project]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
