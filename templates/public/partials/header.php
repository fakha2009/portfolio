<?php

declare(strict_types=1);

$isRu = cv_current_locale() === 'ru';
$brandName = $site['site_name'] ?? ($isRu ? 'Махмадхонзода Фахриддин' : 'Makhmadkhonzoda Fakhriddin');
$brandSubtitle = 'Go / PHP / Python · backend';
$navLabel = $isRu ? 'Основная навигация' : 'Main navigation';
$skipLabel = $isRu ? 'Перейти к содержанию' : 'Skip to content';
$burgerLabel = $isRu ? 'Меню' : 'Menu';
$ctaLabel = $site['primary_cta_label'] ?? cv_t('actions.contact_me');
$isHome = (bool) ($is_home ?? false);
$homeHref = static fn (string $anchor): string => $isHome ? ('#' . $anchor) : (cv_url('') . '#' . $anchor);
?>
<a href="<?= cv_e($homeHref('about')) ?>" class="skip-link"><?= cv_e($skipLabel) ?></a>
<header class="header">
    <div class="container">
        <div class="header__bar">
            <a class="brand" href="<?= cv_url('') ?>" aria-label="<?= cv_e($brandName) ?>">
                <span class="brand__mark">FM</span>
                <span>
                    <span class="brand__name"><?= cv_e($brandName) ?></span>
                    <span class="brand__role"><?= cv_e($brandSubtitle) ?></span>
                </span>
            </a>
            <nav class="nav" aria-label="<?= cv_e($navLabel) ?>">
                <a href="<?= cv_e($homeHref('about')) ?>"><?= cv_e(cv_t('nav.about')) ?></a>
                <a href="<?= cv_e($homeHref('skills')) ?>"><?= cv_e($isRu ? 'Стек' : 'Stack') ?></a>
                <a href="<?= cv_e($homeHref('services')) ?>"><?= cv_e(cv_t('nav.services')) ?></a>
                <a href="<?= cv_e($homeHref('pricing')) ?>"><?= cv_e($isRu ? 'Тарифы' : 'Pricing') ?></a>
                <a href="<?= cv_e($homeHref('work')) ?>"><?= cv_e(cv_t('nav.projects')) ?></a>
                <a href="<?= cv_e($homeHref('contact')) ?>"><?= cv_e(cv_t('nav.contact')) ?></a>
            </nav>
            <div class="header__actions">
                <div class="lang" role="group" aria-label="<?= cv_e($isRu ? 'Язык' : 'Language') ?>">
                    <a class="<?= cv_current_locale() === 'ru' ? 'is-active' : '' ?>" href="<?= cv_e(cv_locale_url('ru')) ?>" data-lang-btn="ru">RU</a>
                    <a class="<?= cv_current_locale() === 'en' ? 'is-active' : '' ?>" href="<?= cv_e(cv_locale_url('en')) ?>" data-lang-btn="en">EN</a>
                </div>
                <a href="<?= cv_e($homeHref('contact')) ?>" class="btn btn--primary btn--sm"><?= cv_e($ctaLabel) ?></a>
                <button class="burger" type="button" aria-label="<?= cv_e($burgerLabel) ?>" aria-expanded="false"><span></span><span></span><span></span></button>
            </div>
        </div>
    </div>
</header>
<nav class="mobile-menu" aria-label="<?= cv_e($navLabel) ?>">
    <a href="<?= cv_e($homeHref('about')) ?>"><?= cv_e(cv_t('nav.about')) ?></a>
    <a href="<?= cv_e($homeHref('skills')) ?>"><?= cv_e($isRu ? 'Стек' : 'Stack') ?></a>
    <a href="<?= cv_e($homeHref('services')) ?>"><?= cv_e(cv_t('nav.services')) ?></a>
    <a href="<?= cv_e($homeHref('pricing')) ?>"><?= cv_e($isRu ? 'Тарифы' : 'Pricing') ?></a>
    <a href="<?= cv_e($homeHref('work')) ?>"><?= cv_e(cv_t('nav.projects')) ?></a>
    <a href="<?= cv_e($homeHref('contact')) ?>"><?= cv_e(cv_t('nav.contact')) ?></a>
</nav>
