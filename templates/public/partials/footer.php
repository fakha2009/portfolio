<?php

declare(strict_types=1);

$isRu = cv_current_locale() === 'ru';
$footerStack = ['Go', 'PHP', 'Python', 'PostgreSQL', 'MySQL', 'Redis'];
$contactTitle = $isRu ? 'Контакты' : 'Contacts';
$navigationTitle = $isRu ? 'Навигация' : 'Navigation';
$year = date('Y');
$copyrightName = $site['site_name'] ?? 'Fakhriddin Portfolio';
$copyrightText = "© {$year} {$copyrightName}";
$madeWithText = $isRu ? 'Спроектировано вокруг инженерной ясности' : 'Designed around engineering clarity';
?>
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                <a class="brand" href="<?= cv_url('') ?>">
                    <span class="brand__mark">FM</span>
                    <span class="brand__name"><?= cv_e($site['site_name'] ?? 'Fakhriddin Portfolio') ?></span>
                </a>
                <p><?= cv_e($site['footer_notice'] ?? '') ?></p>
                <div class="footer__stack">
                <?php foreach ($footerStack as $item): ?>
                    <span><?= cv_e($item) ?></span>
                <?php endforeach; ?>
                </div>
            </div>
            <div class="footer__col">
                <h4><?= cv_e($navigationTitle) ?></h4>
                <a href="<?= cv_url('') ?>#about"><?= cv_e(cv_t('nav.about')) ?></a>
                <a href="<?= cv_url('') ?>#skills"><?= cv_e($isRu ? 'Стек' : 'Stack') ?></a>
                <a href="<?= cv_url('') ?>#services"><?= cv_e(cv_t('nav.services')) ?></a>
                <a href="<?= cv_url('') ?>#work"><?= cv_e(cv_t('nav.projects')) ?></a>
                <a href="<?= cv_url('') ?>#contact"><?= cv_e(cv_t('nav.contact')) ?></a>
            </div>
            <div class="footer__col">
                <h4><?= cv_e($contactTitle) ?></h4>
                <a href="<?= cv_e($social['email'] ?? 'mailto:fakhridinkon2009@gmail.com') ?>"><?= cv_e($site['contact_email'] ?? 'fakhridinkon2009@gmail.com') ?></a>
                <a href="<?= cv_e($social['phone'] ?? 'tel:+992881845151') ?>"><?= cv_e($site['contact_phone'] ?? '+992 88 184 5151') ?></a>
                <a href="<?= cv_e($social['telegram'] ?? 'https://t.me/Fakhriddin_dev') ?>" target="_blank" rel="noopener">Telegram</a>
                <?php if (!empty($social['linkedin'])): ?>
                    <a href="<?= cv_e($social['linkedin']) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                <?php endif; ?>
                <?php if (!empty($social['github'])): ?>
                    <a href="<?= cv_e($social['github']) ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer__bottom">
            <span><?= cv_e($copyrightText) ?></span>
            <span><?= cv_e($madeWithText) ?></span>
        </div>
    </div>
</footer>
