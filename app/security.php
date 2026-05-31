<?php

declare(strict_types=1);

function cv_boot_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name((string) cv_config('app.session_name', 'cvf_session'));
    $cookiePath = cv_base_path();
    if ($cookiePath === '') {
        $cookiePath = '/';
    }

    session_set_cookie_params([
        'lifetime' => (int) cv_config('security.session_lifetime', 7200),
        'path' => $cookiePath,
        'domain' => '',
        'secure' => cv_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function cv_random_string(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}

function cv_csrf_token(string $scope = 'default'): string
{
    if (!isset($_SESSION['_csrf'][$scope])) {
        $_SESSION['_csrf'][$scope] = hash_hmac('sha256', cv_random_string(16), (string) cv_config('security.csrf_key'));
    }

    return $_SESSION['_csrf'][$scope];
}

function cv_validate_csrf(?string $token, string $scope = 'default'): bool
{
    $sessionToken = $_SESSION['_csrf'][$scope] ?? null;

    return is_string($token) && is_string($sessionToken) && hash_equals($sessionToken, $token);
}

function cv_require_csrf(string $scope = 'default'): void
{
    if (!cv_validate_csrf((string) cv_post('_token'), $scope)) {
        cv_flash('error', cv_t('messages.invalid_csrf'));
        if (cv_is_ajax_request()) {
            cv_json_response(['ok' => false, 'message' => cv_t('messages.invalid_csrf')], 403);
        }
        cv_redirect(cv_current_url());
    }
}

function cv_client_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

    foreach ($keys as $key) {
        $value = trim((string) ($_SERVER[$key] ?? ''));

        if ($value === '') {
            continue;
        }

        $candidate = trim(explode(',', $value)[0]);

        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }

    return '0.0.0.0';
}

function cv_ip_hash(): string
{
    return hash('sha256', cv_client_ip() . '|' . (string) cv_config('security.csrf_key'));
}

function cv_user_agent(): string
{
    return substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'), 0, 500);
}

function cv_honeypot_passed(string $field = 'website'): bool
{
    return trim((string) cv_post($field, '')) === '';
}

function cv_device_type(?string $userAgent = null): string
{
    $userAgent = strtolower($userAgent ?? cv_user_agent());

    if (str_contains($userAgent, 'ipad') || str_contains($userAgent, 'tablet')) {
        return 'tablet';
    }

    if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
        return 'mobile';
    }

    return 'desktop';
}

function cv_rate_limit_record(string $scope, string $identifier): ?array
{
    return cv_fetch_one(
        'SELECT * FROM rate_limits WHERE scope_key = :scope_key AND identifier = :identifier LIMIT 1',
        [
            'scope_key' => $scope,
            'identifier' => $identifier,
        ]
    );
}

function cv_rate_limit_check(string $scope, string $identifier, int $limit, int $windowSeconds): array
{
    $record = cv_rate_limit_record($scope, $identifier);

    if (!$record) {
        return [
            'limited' => false,
            'hits' => 0,
            'remaining' => $limit,
            'retry_after' => 0,
        ];
    }

    $windowStarted = strtotime((string) $record['window_started_at']);
    $blockedUntil = $record['blocked_until'] ? strtotime((string) $record['blocked_until']) : 0;
    $now = time();

    if ($windowStarted + $windowSeconds <= $now) {
        cv_execute(
            'UPDATE rate_limits SET hits = 0, window_started_at = :started_at, blocked_until = NULL, updated_at = :updated_at WHERE id = :id',
            [
                'started_at' => date('Y-m-d H:i:s', $now),
                'updated_at' => cv_now(),
                'id' => $record['id'],
            ]
        );

        return [
            'limited' => false,
            'hits' => 0,
            'remaining' => $limit,
            'retry_after' => 0,
        ];
    }

    $limited = $blockedUntil > $now;
    $hits = (int) $record['hits'];

    return [
        'limited' => $limited,
        'hits' => $hits,
        'remaining' => max($limit - $hits, 0),
        'retry_after' => $limited ? ($blockedUntil - $now) : 0,
    ];
}

function cv_rate_limit_hit(string $scope, string $identifier, int $limit, int $windowSeconds): array
{
    $record = cv_rate_limit_record($scope, $identifier);
    $now = cv_now();
    $nowTimestamp = time();
    $blockedUntil = null;

    if (!$record) {
        cv_execute(
            'INSERT INTO rate_limits (scope_key, identifier, hits, window_started_at, blocked_until, updated_at) VALUES (:scope_key, :identifier, 1, :window_started_at, NULL, :updated_at)',
            [
                'scope_key' => $scope,
                'identifier' => $identifier,
                'window_started_at' => $now,
                'updated_at' => $now,
            ]
        );

        return cv_rate_limit_check($scope, $identifier, $limit, $windowSeconds);
    }

    $windowStarted = strtotime((string) $record['window_started_at']);
    $hits = (int) $record['hits'];

    if ($windowStarted + $windowSeconds <= $nowTimestamp) {
        $hits = 1;
        $windowStartedAt = $now;
    } else {
        $hits++;
        $windowStartedAt = $record['window_started_at'];
    }

    if ($hits >= $limit) {
        $blockedUntil = date('Y-m-d H:i:s', $nowTimestamp + $windowSeconds);
    }

    cv_execute(
        'UPDATE rate_limits SET hits = :hits, window_started_at = :window_started_at, blocked_until = :blocked_until, updated_at = :updated_at WHERE id = :id',
        [
            'hits' => $hits,
            'window_started_at' => $windowStartedAt,
            'blocked_until' => $blockedUntil,
            'updated_at' => $now,
            'id' => $record['id'],
        ]
    );

    return cv_rate_limit_check($scope, $identifier, $limit, $windowSeconds);
}

function cv_rate_limit_clear(string $scope, string $identifier): void
{
    cv_execute(
        'DELETE FROM rate_limits WHERE scope_key = :scope_key AND identifier = :identifier',
        [
            'scope_key' => $scope,
            'identifier' => $identifier,
        ]
    );
}
