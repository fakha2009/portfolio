<?php

declare(strict_types=1);

$projectTitle = cv_localized_value($project, 'title');
$projectDescription = cv_localized_value($project, 'short_description');
$projectRole = cv_localized_value($project, 'role');
$projectCategory = cv_localized_value([
    'name_ru' => $project['category_name_ru'] ?? '',
    'name_en' => $project['category_name_en'] ?? '',
], 'name');
$techList = array_slice(array_filter(array_map('trim', explode(',', (string) ($project['technologies'] ?? '')))), 0, 3);
?>
<article class="proj reveal" data-cats="<?= cv_e((string) ($project['category_slug'] ?? 'all')) ?>">
    <div class="proj__top">
        <span class="proj__cat"><?= cv_e($projectCategory !== '' ? $projectCategory : 'Project') ?></span>
        <?php if (!empty($project['external_url'])): ?>
            <span class="proj__live"><?= cv_e(cv_t('labels.live')) ?></span>
        <?php endif; ?>
    </div>
    <h3><a href="<?= cv_url('projects/' . $project['slug']) ?>"><?= cv_e($projectTitle) ?></a></h3>
    <?php if ($projectRole !== ''): ?>
        <div class="proj__role"><?= cv_e($projectRole) ?></div>
    <?php endif; ?>
    <p><?= cv_e($projectDescription) ?></p>
    <?php if ($techList !== []): ?>
        <div class="proj__tags">
            <?php foreach ($techList as $tech): ?>
                <span><?= cv_e($tech) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>
