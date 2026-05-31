<?php

declare(strict_types=1);

$post = $post ?? [];
$site = $site ?? cv_site_settings();
$social = $social ?? cv_social_settings();
$isRu = cv_current_locale() === 'ru';
?>
<section class="section section--tight page-hero blog-hero">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= cv_e($isRu ? 'Блог' : 'Blog') ?></span>
            <h1><?= cv_e(cv_localized_value($post, 'title')) ?></h1>
            <p class="blog-meta"><?= cv_e((string) ($post['published_at'] ? date('F j, Y', strtotime($post['published_at'])) : '')) ?></p>
        </div>
    </div>
</section>

<section class="section blog-post-section">
    <div class="container">
        <article class="blog-post-card reveal">
            <?php if (!empty($post['cover_image'])): ?>
                <div class="blog-post-media">
                    <img src="<?= cv_e(cv_upload_url((string) $post['cover_image'])) ?>" alt="<?= cv_e(cv_localized_value($post, 'title')) ?>" loading="lazy">
                </div>
            <?php endif; ?>
            <div class="blog-post-body">
                <div class="blog-post-content">
                    <?= nl2br(cv_e(cv_localized_value($post, 'body'))) ?>
                </div>
                <?php if (!empty($post['tags'])): ?>
                    <div class="blog-post-tags">
                        <?php foreach (array_filter(array_map('trim', explode(',', (string) $post['tags']))) as $tag): ?>
                            <span class="tag-pill"><?= cv_e($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a class="btn btn--ghost" href="<?= cv_url('blog') ?>"><?= cv_e($isRu ? 'Все записи' : 'All posts') ?></a>
            </div>
        </article>
    </div>
</section>
