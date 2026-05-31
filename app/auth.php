<?php

declare(strict_types=1);

function cv_admin_slug(): string
{
    return trim((string) cv_config('app.admin_slug', 'secure-portal-x9a7'), '/');
}

function cv_admin_url(string $path = ''): string
{
    $slug = cv_admin_slug();
    $path = trim($path, '/');
    $base = cv_base_path() . '/' . $slug;

    if ($path !== '') {
        $base .= '/' . $path;
    }

    return $base;
}

function cv_admin_user(): ?array
{
    static $user = false;

    if ($user !== false) {
        return $user;
    }

    $userId = (int) ($_SESSION['admin_user_id'] ?? 0);

    if ($userId <= 0 || !cv_database_ready()) {
        $user = null;
        return $user;
    }

    $user = cv_fetch_one('SELECT * FROM admin_users WHERE id = :id LIMIT 1', ['id' => $userId]);

    return $user ?: null;
}

function cv_is_admin_authenticated(): bool
{
    return cv_admin_user() !== null;
}

function cv_require_admin_auth(): void
{
    if (cv_is_admin_authenticated()) {
        return;
    }

    cv_redirect(cv_admin_url('login'));
}

function cv_admin_login(string $identity, string $password): array
{
    if (!cv_database_ready()) {
        return ['ok' => false, 'message' => cv_t('messages.setup_required')];
    }

    $identifier = hash('sha256', strtolower(trim($identity)) . '|' . cv_client_ip());
    $limitState = cv_rate_limit_check(
        'admin_login',
        $identifier,
        (int) cv_config('security.login_max_attempts', 5),
        (int) cv_config('security.login_window_seconds', 900)
    );

    if ($limitState['limited']) {
        return ['ok' => false, 'message' => cv_t('messages.login_blocked')];
    }

    $user = cv_fetch_one(
        'SELECT * FROM admin_users WHERE username = :username OR email = :email LIMIT 1',
        [
            'username' => trim($identity),
            'email' => trim($identity),
        ]
    );

    if (!$user || !password_verify($password, (string) $user['password_hash'])) {
        cv_rate_limit_hit(
            'admin_login',
            $identifier,
            (int) cv_config('security.login_max_attempts', 5),
            (int) cv_config('security.login_window_seconds', 900)
        );

        return ['ok' => false, 'message' => cv_t('messages.login_failed')];
    }

    cv_rate_limit_clear('admin_login', $identifier);
    session_regenerate_id(true);
    $_SESSION['admin_user_id'] = (int) $user['id'];

    cv_execute(
        'UPDATE admin_users SET last_login_at = :last_login_at, last_login_ip = :last_login_ip, updated_at = :updated_at WHERE id = :id',
        [
            'last_login_at' => cv_now(),
            'last_login_ip' => cv_client_ip(),
            'updated_at' => cv_now(),
            'id' => $user['id'],
        ]
    );

    return ['ok' => true, 'message' => cv_t('messages.login_success')];
}

function cv_admin_logout(): void
{
    unset($_SESSION['admin_user_id']);
    session_regenerate_id(true);
}

function cv_admin_update_profile(array $data): array
{
    $admin = cv_admin_user();

    if (!$admin) {
        return ['ok' => false, 'message' => cv_t('messages.login_failed')];
    }

    $fullName = trim((string) ($data['full_name'] ?? $admin['full_name']));
    $email = trim((string) ($data['email'] ?? $admin['email']));

    if (mb_strlen($fullName, 'UTF-8') < 2) {
        return ['ok' => false, 'message' => 'Please enter a valid full name.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Please enter a valid email address.'];
    }

    $updated = cv_execute(
        'UPDATE admin_users SET full_name = :full_name, email = :email, updated_at = :updated_at WHERE id = :id',
        [
            'full_name' => $fullName,
            'email' => $email,
            'updated_at' => cv_now(),
            'id' => $admin['id'],
        ]
    );

    return ['ok' => $updated, 'message' => $updated ? cv_t('messages.saved') : 'Profile update failed.'];
}

function cv_admin_update_password(string $currentPassword, string $newPassword): array
{
    $admin = cv_admin_user();

    if (!$admin) {
        return ['ok' => false, 'message' => cv_t('messages.login_failed')];
    }

    if (!password_verify($currentPassword, (string) $admin['password_hash'])) {
        return ['ok' => false, 'message' => cv_t('messages.login_failed')];
    }

    if (strlen($newPassword) < 10) {
        return ['ok' => false, 'message' => 'Password must be at least 10 characters long.'];
    }

    if ($currentPassword === $newPassword) {
        return ['ok' => false, 'message' => 'Choose a new password that differs from the current one.'];
    }

    cv_execute(
        'UPDATE admin_users SET password_hash = :password_hash, updated_at = :updated_at WHERE id = :id',
        [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'updated_at' => cv_now(),
            'id' => $admin['id'],
        ]
    );

    return ['ok' => true, 'message' => cv_t('messages.saved')];
}
