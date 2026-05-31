<?php

declare(strict_types=1);
?>
<section class="admin-grid two-thirds">
    <form class="admin-card admin-form" method="post" action="<?= cv_admin_url('security') ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <div class="admin-card-head">
            <div>
                <h2>Account profile</h2>
                <p>Keep a valid owner name and email here so admin access and notifications stay aligned.</p>
            </div>
        </div>
        <div class="admin-form-grid">
            <label><span>Full name</span><input type="text" name="full_name" value="<?= cv_e((string) ($profile['full_name'] ?? '')) ?>"></label>
            <label><span>Email</span><input type="email" name="email" value="<?= cv_e((string) ($profile['email'] ?? '')) ?>"></label>
        </div>
        <p class="field-hint">Last login: <?= !empty($profile['last_login_at']) ? cv_e((string) $profile['last_login_at']) : 'not recorded yet' ?></p>
        <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
    </form>
    <form class="admin-card admin-form" method="post" action="<?= cv_admin_url('security/password') ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <div class="admin-card-head">
            <div>
                <h2>Password update</h2>
                <p>Use a password with at least 10 characters. Session-based access remains limited to one owner account.</p>
            </div>
        </div>
        <div class="admin-form-grid">
            <label><span>Current password</span><input type="password" name="current_password" required></label>
            <label><span>New password</span><input type="password" name="new_password" required></label>
        </div>
        <button class="admin-button admin-button-secondary" type="submit">Update password</button>
    </form>
</section>
