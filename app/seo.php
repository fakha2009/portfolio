<?php

declare(strict_types=1);

function cv_meta_defaults(): array
{
    $site = cv_site_settings();
    $seo = cv_seo_settings();

    return [
        'title' => (string) ($seo['meta_title'] ?? $site['site_name'] ?? 'Portfolio'),
        'description' => (string) ($seo['meta_description'] ?? $site['site_tagline'] ?? ''),
        'image' => (string) ($seo['og_image'] ?? 'assets/img/og-cover.svg'),
        'type' => 'website',
        'robots' => cv_boolean($seo['robots_index'] ?? true) ? 'index,follow' : 'noindex,nofollow',
    ];
}

function cv_page_meta(string $page, array $context = []): array
{
    $defaults = cv_meta_defaults();

    if ($page === 'project' && isset($context['project'])) {
        $project = $context['project'];
        return [
            'title' => cv_localized_value($project, 'seo_title') ?: cv_localized_value($project, 'title'),
            'description' => cv_localized_value($project, 'seo_description') ?: cv_localized_value($project, 'short_description'),
            'image' => (string) ($project['og_image'] ?: $project['cover_image'] ?: $defaults['image']),
            'type' => 'article',
            'robots' => 'index,follow',
        ];
    }

    if ($page === 'projects') {
        $portfolio = cv_content_block('portfolio_intro');

        return [
            'title' => (($portfolio['label'] ?? cv_t('nav.projects')) ?: cv_t('nav.projects')) . ' | ' . $defaults['title'],
            'description' => (string) ($portfolio['text'] ?? 'Backend practice projects, API case studies, and practical web engineering work.'),
            'image' => $defaults['image'],
            'type' => 'website',
            'robots' => 'index,follow',
        ];
    }

    if ($page === 'blog') {
        $blogIntro = cv_content_block('blog_intro');

        return [
            'title' => (($blogIntro['label'] ?? cv_t('nav.blog')) ?: cv_t('nav.blog')) . ' | ' . $defaults['title'],
            'description' => (string) ($blogIntro['text'] ?? 'Insights into backend, APIs, and practical web engineering.'),
            'image' => $defaults['image'],
            'type' => 'website',
            'robots' => 'index,follow',
        ];
    }

    if ($page === 'blog_post' && isset($context['post'])) {
        $post = $context['post'];
        return [
            'title' => cv_localized_value($post, 'seo_title') ?: cv_localized_value($post, 'title'),
            'description' => cv_localized_value($post, 'excerpt') ?: cv_localized_value($post, 'title'),
            'image' => (string) ($post['cover_image'] ?: $defaults['image']),
            'type' => 'article',
            'robots' => 'index,follow',
        ];
    }

    return $defaults;
}
