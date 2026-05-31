<?php

declare(strict_types=1);
?>
<form class="admin-card admin-form" method="post" action="<?= cv_admin_url('seo') . '?locale=' . urlencode((string) $locale) ?>" data-ajax-save>
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <div class="admin-card-head">
        <h2>SEO settings</h2>
        <div class="locale-tabs">
            <a class="<?= $locale === 'ru' ? 'is-active' : '' ?>" href="<?= cv_admin_url('seo') ?>?locale=ru">RU</a>
            <a class="<?= $locale === 'en' ? 'is-active' : '' ?>" href="<?= cv_admin_url('seo') ?>?locale=en">EN</a>
        </div>
    </div>
    <div class="admin-form-grid">
        <label><span>Meta title</span><input type="text" name="meta_title" value="<?= cv_e((string) ($settings['meta_title'] ?? '')) ?>"></label>
        <label><span>OG image path</span><input type="text" name="og_image" value="<?= cv_e((string) ($settings['og_image'] ?? '')) ?>"></label>
        <label class="full"><span>Meta description</span><textarea name="meta_description" rows="4"><?= cv_e((string) ($settings['meta_description'] ?? '')) ?></textarea></label>
        <label><span>Twitter card</span><input type="text" name="twitter_card" value="<?= cv_e((string) ($settings['twitter_card'] ?? 'summary_large_image')) ?>"></label>
        <label><span>Allow indexing</span><select name="robots_index"><option value="1" <?= ($settings['robots_index'] ?? '') === '1' ? 'selected' : '' ?>>Yes</option><option value="0" <?= ($settings['robots_index'] ?? '') === '0' ? 'selected' : '' ?>>No</option></select></label>
    </div>
    <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
</form>
