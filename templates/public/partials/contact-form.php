<?php

declare(strict_types=1);
?>
<form id="contact-form" class="contact-form" method="post" action="<?= cv_url('contact/submit') ?>">
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('contact')) ?>">
    <input type="hidden" name="_redirect" value="<?= cv_e(cv_current_url()) ?>">
    <div id="plan-badge" class="plan-badge" hidden
         data-label="<?= cv_e(cv_current_locale() === 'ru' ? 'Тариф' : 'Plan') ?>">
        <span class="plan-badge__text"></span>
        <button class="plan-badge__close" type="button" aria-label="<?= cv_e(cv_current_locale() === 'ru' ? 'Сбросить' : 'Clear') ?>">×</button>
    </div>
    <div class="honeypot">
        <label for="website">Website</label>
        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
    </div>
    <div class="form-grid">
        <label class="field">
            <span><?= cv_e(cv_t('labels.name')) ?></span>
            <input type="text" name="name" value="<?= cv_e((string) cv_old('name')) ?>" required>
        </label>
        <label class="field">
            <span><?= cv_e(cv_t('labels.email')) ?></span>
            <input type="email" name="email" value="<?= cv_e((string) cv_old('email')) ?>" required>
        </label>
        <label class="field">
            <span><?= cv_e(cv_t('labels.phone')) ?></span>
            <input type="text" name="phone" value="<?= cv_e((string) cv_old('phone')) ?>">
        </label>
        <label class="field">
            <span><?= cv_e(cv_t('labels.company')) ?></span>
            <input type="text" name="company" value="<?= cv_e((string) cv_old('company')) ?>">
        </label>
        <label class="field full">
            <span><?= cv_e(cv_t('labels.budget')) ?></span>
            <select name="budget">
                <?php $oldBudget = (string) cv_old('budget'); ?>
                <?php foreach (['' => cv_current_locale() === 'ru' ? 'Не определён' : 'Not defined', '$500 - $1 500' => '$500 - $1 500', '$1 500 - $5 000' => '$1 500 - $5 000', '$5 000+' => '$5 000+'] as $value => $label): ?>
                    <option value="<?= cv_e($value !== '' ? $value : $label) ?>" <?= $oldBudget === ($value !== '' ? $value : $label) ? 'selected' : '' ?>><?= cv_e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field full">
            <span><?= cv_e(cv_t('labels.message')) ?></span>
            <textarea name="message" rows="6" required><?= cv_e((string) cv_old('message')) ?></textarea>
        </label>
    </div>
    <div class="form-submit">
        <button class="btn btn--primary" type="submit"><?= cv_e(cv_t('actions.send')) ?></button>
        <span class="form-note"><?= cv_e(cv_current_locale() === 'ru' ? 'Отвечаю обычно в течение дня' : 'I usually reply within a day') ?></span>
    </div>
    <div id="form-status" class="form-status" role="status" aria-live="polite"></div>
</form>
