<?php

declare(strict_types=1);

function cv_config(?string $key = null, mixed $default = null): mixed
{
    $config = $GLOBALS['cv_config'] ?? [];

    if ($key === null || $key === '') {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function cv_set_config(string $key, mixed $value): void
{
    $segments = explode('.', $key);
    $config = &$GLOBALS['cv_config'];

    foreach ($segments as $segment) {
        if (!isset($config[$segment]) || !is_array($config[$segment])) {
            $config[$segment] = [];
        }

        $config = &$config[$segment];
    }

    $config = $value;
}

function cv_root(?string $path = null): string
{
    $root = $GLOBALS['cv_root'] ?? dirname(__DIR__);

    if ($path === null || $path === '') {
        return $root;
    }

    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function cv_ensure_directory(string $path): bool
{
    if (!is_dir($path)) {
        if (!@mkdir($path, 0755, true) && !is_dir($path)) {
            return false;
        }
    }

    return is_writable($path);
}

function cv_request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function cv_is_post(): bool
{
    return cv_request_method() === 'POST';
}

function cv_is_ajax_request(): bool
{
    $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
    $acceptHeader = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

    return $requestedWith === 'xmlhttprequest' || str_contains($acceptHeader, 'application/json');
}

function cv_json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function cv_array_get(array $source, string $key, mixed $default = null): mixed
{
    if ($key === '') {
        return $source;
    }

    $value = $source;

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function cv_input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function cv_post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function cv_get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function cv_old(string $key, mixed $default = null): mixed
{
    $old = $_SESSION['_old'] ?? [];

    return cv_array_get($old, $key, $default);
}

function cv_flash_old_input(array $input): void
{
    $_SESSION['_old'] = $input;
}

function cv_clear_old_input(): void
{
    unset($_SESSION['_old']);
}

function cv_flash(string $type, string $message): void
{
    $_SESSION['_flashes'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function cv_consume_flashes(): array
{
    $flashes = $_SESSION['_flashes'] ?? [];
    unset($_SESSION['_flashes']);

    return $flashes;
}

function cv_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function cv_starts_with(string $haystack, string $needle): bool
{
    return str_starts_with($haystack, $needle);
}

function cv_is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? null) === '443'
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function cv_base_path(): string
{
    $basePath = trim((string) cv_config('app.base_path', ''), '/');

    return $basePath === '' ? '' : '/' . $basePath;
}

function cv_locale_prefix(?string $locale = null): string
{
    $locale = $locale ?? cv_current_locale();
    $default = cv_default_locale();

    if (!cv_config('app.multilingual', true) || $locale === $default) {
        return '';
    }

    return '/' . $locale;
}

function cv_url(string $path = '', ?string $locale = null, bool $absolute = false): string
{
    $basePath = cv_base_path();
    $path = trim($path, '/');
    $urlPath = $basePath . cv_locale_prefix($locale);

    if ($path !== '') {
        $urlPath .= '/' . $path;
    }

    $urlPath = $urlPath === '' ? '/' : $urlPath;

    if (!$absolute) {
        return $urlPath;
    }

    return rtrim((string) cv_config('app.url'), '/') . $urlPath;
}

function cv_asset(string $path): string
{
    $relative = ltrim($path, '/');
    $file = cv_root($relative);
    $version = is_file($file) ? (string) filemtime($file) : '1';

    return cv_base_path() . '/' . $relative . '?v=' . $version;
}

function cv_upload_url(?string $path): string
{
    if (!$path) {
        return '';
    }
    // base64 data URLs or external CDN URLs — return as-is
    if (str_starts_with($path, 'data:') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return cv_base_path() . '/' . ltrim($path, '/');
}

function cv_redirect(string $path, int $status = 302): never
{
    header('Location: ' . $path, true, $status);
    exit;
}

function cv_send_admin_cache_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function cv_abort(int $status = 404, string $message = 'Not found'): never
{
    http_response_code($status);
    echo $message;
    exit;
}

function cv_render(string $template, array $data = [], ?string $layout = null): void
{
    $layout = $layout ?? 'public/layout';
    $contentTemplate = cv_root('templates/' . $template . '.php');
    $layoutFile = cv_root('templates/' . $layout . '.php');

    if (!is_file($contentTemplate)) {
        cv_abort(500, 'Template not found: ' . $template);
    }

    extract($data, EXTR_SKIP);

    if (!is_file($layoutFile)) {
        require $contentTemplate;
        return;
    }

    require $layoutFile;
}

function cv_partial(string $template, array $data = []): void
{
    $file = cv_root('templates/' . $template . '.php');

    if (!is_file($file)) {
        return;
    }

    extract($data, EXTR_SKIP);
    require $file;
}

function cv_now(): string
{
    return date('Y-m-d H:i:s');
}

function cv_log(string $channel, string $message, array $context = []): void
{
    $line = '[' . cv_now() . '] ' . strtoupper($channel) . ': ' . $message;

    if ($context !== []) {
        $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // Try writable project storage first; fall back to /tmp for read-only filesystems (Vercel).
    $dirs = [cv_root('storage/logs'), sys_get_temp_dir() . '/cv_logs'];

    foreach ($dirs as $dir) {
        if (cv_ensure_directory($dir)) {
            @file_put_contents($dir . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', $line . PHP_EOL, FILE_APPEND);
            return;
        }
    }
}

function cv_slugify(string $value): string
{
    $value = trim($value);
    $value = mb_strtolower($value, 'UTF-8');
    $value = strtr($value, [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ]);
    $map = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];
    $value = strtr($value, $map);
    $value = preg_replace('/[^a-z0-9]+/u', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'item-' . bin2hex(random_bytes(3));
}

function cv_boolean(mixed $value): bool
{
    return in_array($value, [1, '1', true, 'true', 'on', 'yes'], true);
}

function cv_current_url(): string
{
    $scheme = cv_is_https() ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? parse_url((string) cv_config('app.url'), PHP_URL_HOST) ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    return $scheme . '://' . $host . $uri;
}

function cv_path_without_locale(): string
{
    $segments = cv_request_segments();

    if ($segments !== [] && array_key_exists($segments[0], cv_available_locales())) {
        array_shift($segments);
    }

    return implode('/', $segments);
}

function cv_locale_url(string $locale): string
{
    return cv_url(cv_path_without_locale(), $locale);
}

function cv_text_html(?string $text): string
{
    $escaped = cv_e($text ?? '');

    return nl2br($escaped);
}

function cv_send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    $csp = [
        "default-src 'self'",
        "base-uri 'self'",
        "frame-ancestors 'self'",
        "object-src 'none'",
        "form-action 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com",
        "font-src 'self' https://fonts.gstatic.com https://www.gstatic.com data:",
        "img-src 'self' data: https:",
        "media-src 'self' https://res.cloudinary.com https://*.cloudinary.com",
        "connect-src 'self' https://api.cloudinary.com https://api.telegram.org https://cdn.jsdelivr.net",
    ];

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    header('Content-Security-Policy: ' . implode('; ', $csp));

    if (cv_is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
