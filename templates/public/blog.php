<?php

declare(strict_types=1);

$blogPage = $blogPage ?? [];
$site = $blogPage['site'] ?? cv_site_settings();
$social = $blogPage['social'] ?? cv_social_settings();
$intro = $blogPage['blog_intro'] ?? [];
$posts = $blogPage['posts'] ?? [];
$isRu = cv_current_locale() === 'ru';
?>
<section class="section section--tight page-hero blog-hero">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= cv_e($intro['label'] ?? cv_t('nav.blog')) ?></span>
            <h1><?= cv_e($intro['title'] ?? cv_t('nav.blog')) ?></h1>
            <p><?= cv_e($intro['text'] ?? 'Latest notes on backend, APIs, admin panels, and practical web engineering.') ?></p>
        </div>
    </div>
</section>

<section class="section blog-section">
    <div class="container">
        <?php if ($posts !== []): ?>
            <div class="blog-grid blog-grid--listing">
                <?php foreach ($posts as $index => $post): ?>
                    <?php
                    $title = cv_localized_value($post, 'title');
                    $excerpt = cv_localized_value($post, 'excerpt');
                    $cover = cv_upload_url((string) ($post['cover_image'] ?? ''));
                    $tags = array_slice(array_filter(array_map('trim', explode(',', (string) ($post['tags'] ?? '')))), 0, 3);
                    $isFeatured = $index === 0 && (int) ($post['featured'] ?? 0) === 1;
                    $publishedTime = !empty($post['published_at']) ? strtotime((string) $post['published_at']) : false;
                    $publishedAt = $publishedTime ? date('M j, Y', $publishedTime) : '';
                    ?>
                    <article class="post <?= $isFeatured ? 'post--featured' : '' ?> reveal">
                        <a class="post__media <?= $cover === '' ? 'post__media--empty' : '' ?>" href="<?= cv_url('blog/' . $post['slug']) ?>" aria-label="<?= cv_e($title) ?>">
                            <?php if ($cover !== ''): ?>
                                <img src="<?= cv_e($cover) ?>" alt="<?= cv_e($title) ?>" loading="lazy">
                            <?php else: ?>
                                <span><?= cv_e(cv_t('nav.blog')) ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="post__body">
                            <div class="post__meta">
                                <?php if ($publishedAt !== ''): ?>
                                    <span class="post__date"><?= cv_e($publishedAt) ?></span>
                                <?php endif; ?>
                                <?php if ($tags !== []): ?>
                                    <div class="post__tags">
                                        <?php foreach ($tags as $tag): ?>
                                            <span><?= cv_e($tag) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3><a href="<?= cv_url('blog/' . $post['slug']) ?>"><?= cv_e($title) ?></a></h3>
                            <?php if ($excerpt !== ''): ?>
                                <p><?= cv_e($excerpt) ?></p>
                            <?php endif; ?>
                            <a class="post__link" href="<?= cv_url('blog/' . $post['slug']) ?>">
                                <?= cv_e($isRu ? 'Читать запись' : cv_t('actions.read_post')) ?><span class="ar">-></span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="blog-empty reveal">
                <strong><?= cv_e(cv_t('nav.blog')) ?></strong>
                <p><?= cv_e('No published posts available yet.') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>
