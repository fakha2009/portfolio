<?php

declare(strict_types=1);
?>
<form class="admin-card admin-form" method="post" action="<?= cv_admin_url('settings') . '?locale=' . urlencode((string) $locale) ?>" data-ajax-save>
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <div class="admin-card-head">
        <h2>Site settings</h2>
        <div class="locale-tabs">
            <a class="<?= $locale === 'ru' ? 'is-active' : '' ?>" href="<?= cv_admin_url('settings') ?>?locale=ru">RU</a>
            <a class="<?= $locale === 'en' ? 'is-active' : '' ?>" href="<?= cv_admin_url('settings') ?>?locale=en">EN</a>
        </div>
    </div>
    <div class="admin-form-grid">
        <label>
            <span>Site name</span>
            <input type="text" name="site_name" value="<?= cv_e((string) ($settings['site_name'] ?? '')) ?>">
        </label>
        <label>
            <span>Tagline</span>
            <input type="text" name="site_tagline" value="<?= cv_e((string) ($settings['site_tagline'] ?? '')) ?>">
        </label>
        <label>
            <span>Primary CTA label</span>
            <input type="text" name="primary_cta_label" value="<?= cv_e((string) ($settings['primary_cta_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Primary CTA URL</span>
            <input type="text" name="primary_cta_url" value="<?= cv_e((string) ($settings['primary_cta_url'] ?? '')) ?>">
        </label>
        <label>
            <span>Secondary CTA label</span>
            <input type="text" name="secondary_cta_label" value="<?= cv_e((string) ($settings['secondary_cta_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Secondary CTA URL</span>
            <input type="text" name="secondary_cta_url" value="<?= cv_e((string) ($settings['secondary_cta_url'] ?? '')) ?>">
        </label>
        <label>
            <span>Contact email</span>
            <input type="email" name="contact_email" value="<?= cv_e((string) ($settings['contact_email'] ?? '')) ?>">
        </label>
        <label>
            <span>Contact phone</span>
            <input type="text" name="contact_phone" value="<?= cv_e((string) ($settings['contact_phone'] ?? '')) ?>">
        </label>
        <label>
            <span>Telegram handle</span>
            <input type="text" name="contact_telegram" value="<?= cv_e((string) ($settings['contact_telegram'] ?? '')) ?>">
        </label>
        <label>
            <span>Location</span>
            <input type="text" name="location" value="<?= cv_e((string) ($settings['location'] ?? '')) ?>">
        </label>
        <label>
            <span>Default theme</span>
            <select name="theme_default">
                <option value="dark" <?= ($settings['theme_default'] ?? '') === 'dark' ? 'selected' : '' ?>>Dark</option>
                <option value="light" <?= ($settings['theme_default'] ?? '') === 'light' ? 'selected' : '' ?>>Light</option>
            </select>
        </label>
        <label class="full">
            <span>Footer notice</span>
            <textarea name="footer_notice" rows="4"><?= cv_e((string) ($settings['footer_notice'] ?? '')) ?></textarea>
        </label>
    </div>
    <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
</form>
