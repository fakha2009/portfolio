<?php

declare(strict_types=1);
?>
<form class="admin-card admin-form" method="post" action="<?= cv_admin_url('social') . '?locale=' . urlencode((string) $locale) ?>" data-ajax-save>
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <div class="admin-card-head"><h2>Social and contact links</h2></div>
    <div class="admin-form-grid">
        <label><span>Email link</span><input type="text" name="email" value="<?= cv_e((string) ($settings['email'] ?? '')) ?>"></label>
        <label><span>Phone link</span><input type="text" name="phone" value="<?= cv_e((string) ($settings['phone'] ?? '')) ?>"></label>
        <label><span>Telegram link</span><input type="text" name="telegram" value="<?= cv_e((string) ($settings['telegram'] ?? '')) ?>"></label>
        <label><span>LinkedIn</span><input type="text" name="linkedin" value="<?= cv_e((string) ($settings['linkedin'] ?? '')) ?>"></label>
        <label><span>GitHub</span><input type="text" name="github" value="<?= cv_e((string) ($settings['github'] ?? '')) ?>"></label>
        <label><span>Instagram</span><input type="text" name="instagram" value="<?= cv_e((string) ($settings['instagram'] ?? '')) ?>"></label>
    </div>
    <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
</form>
