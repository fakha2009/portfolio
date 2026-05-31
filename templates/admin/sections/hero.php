<?php

declare(strict_types=1);
?>
<form class="admin-card admin-form" method="post" action="<?= cv_admin_url('hero') . '?locale=' . urlencode((string) $locale) ?>" data-ajax-save>
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <div class="admin-card-head">
        <h2>Hero editor</h2>
        <div class="locale-tabs">
            <a class="<?= $locale === 'ru' ? 'is-active' : '' ?>" href="<?= cv_admin_url('hero') ?>?locale=ru">RU</a>
            <a class="<?= $locale === 'en' ? 'is-active' : '' ?>" href="<?= cv_admin_url('hero') ?>?locale=en">EN</a>
        </div>
    </div>
    <div class="admin-form-grid">
        <label>
            <span>Eyebrow</span>
            <input type="text" name="eyebrow" value="<?= cv_e((string) ($block['eyebrow'] ?? '')) ?>">
        </label>
        <label class="full">
            <span>Headline</span>
            <input type="text" name="headline" value="<?= cv_e((string) ($block['headline'] ?? '')) ?>">
        </label>
        <label class="full">
            <span>Subheadline</span>
            <textarea name="subheadline" rows="4"><?= cv_e((string) ($block['subheadline'] ?? '')) ?></textarea>
        </label>
        <label>
            <span>Primary CTA</span>
            <input type="text" name="primary_cta" value="<?= cv_e((string) ($block['primary_cta'] ?? '')) ?>">
        </label>
        <label>
            <span>Secondary CTA</span>
            <input type="text" name="secondary_cta" value="<?= cv_e((string) ($block['secondary_cta'] ?? '')) ?>">
        </label>
        <label>
            <span>Metric 1 label</span>
            <input type="text" name="metric_one_label" value="<?= cv_e((string) ($block['metric_one_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Metric 1 value</span>
            <input type="text" name="metric_one_value" value="<?= cv_e((string) ($block['metric_one_value'] ?? '')) ?>">
        </label>
        <label>
            <span>Metric 2 label</span>
            <input type="text" name="metric_two_label" value="<?= cv_e((string) ($block['metric_two_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Metric 2 value</span>
            <input type="text" name="metric_two_value" value="<?= cv_e((string) ($block['metric_two_value'] ?? '')) ?>">
        </label>
        <label>
            <span>Metric 3 label</span>
            <input type="text" name="metric_three_label" value="<?= cv_e((string) ($block['metric_three_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Metric 3 value</span>
            <input type="text" name="metric_three_value" value="<?= cv_e((string) ($block['metric_three_value'] ?? '')) ?>">
        </label>
        <label>
            <span>Panel label</span>
            <input type="text" name="panel_label" value="<?= cv_e((string) ($block['panel_label'] ?? '')) ?>">
        </label>
        <label class="full">
            <span>Panel title</span>
            <input type="text" name="panel_title" value="<?= cv_e((string) ($block['panel_title'] ?? '')) ?>">
        </label>
        <label>
            <span>Hero point 1</span>
            <input type="text" name="point_one" value="<?= cv_e((string) ($block['point_one'] ?? '')) ?>">
        </label>
        <label>
            <span>Hero point 2</span>
            <input type="text" name="point_two" value="<?= cv_e((string) ($block['point_two'] ?? '')) ?>">
        </label>
        <label class="full">
            <span>Hero point 3</span>
            <input type="text" name="point_three" value="<?= cv_e((string) ($block['point_three'] ?? '')) ?>">
        </label>
        <label>
            <span>Surface 1 label</span>
            <input type="text" name="surface_one_label" value="<?= cv_e((string) ($block['surface_one_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Surface 1 value</span>
            <input type="text" name="surface_one_value" value="<?= cv_e((string) ($block['surface_one_value'] ?? '')) ?>">
        </label>
        <label>
            <span>Surface 2 label</span>
            <input type="text" name="surface_two_label" value="<?= cv_e((string) ($block['surface_two_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Surface 2 value</span>
            <input type="text" name="surface_two_value" value="<?= cv_e((string) ($block['surface_two_value'] ?? '')) ?>">
        </label>
        <label>
            <span>Surface 3 label</span>
            <input type="text" name="surface_three_label" value="<?= cv_e((string) ($block['surface_three_label'] ?? '')) ?>">
        </label>
        <label>
            <span>Surface 3 value</span>
            <input type="text" name="surface_three_value" value="<?= cv_e((string) ($block['surface_three_value'] ?? '')) ?>">
        </label>
    </div>
    <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
</form>
