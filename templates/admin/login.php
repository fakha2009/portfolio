<?php

declare(strict_types=1);

$flashes = $flashes ?? cv_consume_flashes();
?>
<div class="gate">
    <form class="gate__card" method="post" action="<?= cv_admin_url('login') ?>" autocomplete="off">
        <div class="gate__mark">FM</div>
        <h1><?= cv_e(cv_current_locale() === 'ru' ? 'Панель управления' : 'Control panel') ?></h1>
        <p><?= cv_e(cv_current_locale() === 'ru' ? 'Закрытая зона. Войдите в аккаунт администратора, чтобы продолжить.' : 'Private area. Sign in with the owner account to continue.') ?></p>
            <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin_login')) ?>">
            <label class="gate__field">
                <span>Email or username</span>
                <input type="text" name="identity" autocomplete="username" required>
            </label>
            <label class="gate__field">
                <span>Password</span>
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <?php foreach ($flashes as $flash): ?>
                <div class="gate__err"><?= cv_e($flash['message']) ?></div>
            <?php endforeach; ?>
            <button class="gate__btn" type="submit"><?= cv_e(cv_t('actions.login')) ?></button>
            <div class="gate__hint"><?= cv_e(cv_current_locale() === 'ru' ? 'Авторизация серверная: сессия, CSRF и проверка прав.' : 'Server-side auth: session, CSRF, and access checks.') ?></div>
    </form>
</div>
