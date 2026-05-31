<?php

declare(strict_types=1);

function cv_cache_enabled(): bool
{
    return cv_boolean(cv_config('cache.enabled', true));
}

function cv_cache_prefix(): string
{
    $prefix = (string) cv_config('cache.prefix', 'cvf:');

    return $prefix !== '' ? $prefix : 'cvf:';
}

function cv_cache_key(string $key): string
{
    return cv_cache_prefix() . $key;
}

function cv_cache_redis(): ?Redis
{
    static $redis = false;

    if ($redis !== false) {
        return $redis instanceof Redis ? $redis : null;
    }

    $redis = null;
    $url = trim((string) cv_config('cache.redis_url', getenv('REDIS_URL') ?: ''));

    if ($url === '' || !class_exists('Redis')) {
        return null;
    }

    $parts = parse_url($url);
    if (!is_array($parts) || empty($parts['host'])) {
        return null;
    }

    try {
        $client = new Redis();
        $host = (($parts['scheme'] ?? '') === 'rediss' ? 'tls://' : '') . (string) $parts['host'];
        $client->connect(
            $host,
            (int) ($parts['port'] ?? 6379),
            1.5
        );

        if (!empty($parts['pass'])) {
            $client->auth((string) $parts['pass']);
        }

        if (($parts['scheme'] ?? '') === 'rediss' && method_exists($client, 'setOption')) {
            $client->setOption(Redis::OPT_READ_TIMEOUT, 1.5);
        }

        $redis = $client;
    } catch (Throwable $exception) {
        cv_log('cache', 'Redis connection failed', ['error' => $exception->getMessage()]);
        $redis = null;
    }

    return $redis instanceof Redis ? $redis : null;
}

function cv_cache_dir(): string
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cvf_cache';
    cv_ensure_directory($dir);

    return $dir;
}

function cv_cache_file(string $key): string
{
    return cv_cache_dir() . DIRECTORY_SEPARATOR . 'cvf_' . hash('sha256', cv_cache_key($key)) . '.cache';
}

function cv_cache_get(string $key, mixed $default = null): mixed
{
    static $memory = [];

    if (!cv_cache_enabled()) {
        return $default;
    }

    $fullKey = cv_cache_key($key);
    if (array_key_exists($fullKey, $memory)) {
        $item = $memory[$fullKey];
        if (($item['expires'] ?? 0) >= time()) {
            return $item['value'];
        }
        unset($memory[$fullKey]);
    }

    $redis = cv_cache_redis();
    if ($redis) {
        try {
            $raw = $redis->get($fullKey);
            if (is_string($raw) && $raw !== '') {
                $item = @unserialize($raw, ['allowed_classes' => false]);
                if (is_array($item) && ($item['expires'] ?? 0) >= time()) {
                    $memory[$fullKey] = $item;
                    return $item['value'];
                }
            }
        } catch (Throwable $exception) {
            cv_log('cache', 'Redis get failed', ['error' => $exception->getMessage()]);
        }
    }

    $file = cv_cache_file($key);
    if (is_file($file)) {
        $raw = @file_get_contents($file);
        $item = is_string($raw) ? @unserialize($raw, ['allowed_classes' => false]) : null;

        if (is_array($item) && ($item['expires'] ?? 0) >= time()) {
            $memory[$fullKey] = $item;
            return $item['value'];
        }

        @unlink($file);
    }

    return $default;
}

function cv_cache_set(string $key, mixed $value, ?int $ttl = null): bool
{
    static $memory = [];

    if (!cv_cache_enabled()) {
        return false;
    }

    $ttl = max(1, $ttl ?? (int) cv_config('cache.ttl', 60));
    $fullKey = cv_cache_key($key);
    $item = [
        'expires' => time() + $ttl,
        'value' => $value,
    ];
    $payload = serialize($item);
    $memory[$fullKey] = $item;

    $stored = false;
    $redis = cv_cache_redis();
    if ($redis) {
        try {
            $stored = (bool) $redis->setex($fullKey, $ttl, $payload);
        } catch (Throwable $exception) {
            cv_log('cache', 'Redis set failed', ['error' => $exception->getMessage()]);
        }
    }

    $file = cv_cache_file($key);
    $stored = (@file_put_contents($file, $payload, LOCK_EX) !== false) || $stored;

    return $stored;
}

function cv_cache_remember(string $key, int $ttl, callable $callback): mixed
{
    $cached = cv_cache_get($key, null);
    if ($cached !== null) {
        return $cached;
    }

    $value = $callback();
    cv_cache_set($key, $value, $ttl);

    return $value;
}

function cv_cache_flush(): void
{
    static $flushedAt = 0.0;

    $now = microtime(true);
    if ($flushedAt > 0 && ($now - $flushedAt) < 0.25) {
        return;
    }
    $flushedAt = $now;

    $prefix = cv_cache_prefix();
    $redis = cv_cache_redis();

    if ($redis) {
        try {
            $keys = $redis->keys($prefix . '*');
            if (is_array($keys) && $keys !== []) {
                $redis->del($keys);
            }
        } catch (Throwable $exception) {
            cv_log('cache', 'Redis flush failed', ['error' => $exception->getMessage()]);
        }
    }

    foreach (glob(cv_cache_dir() . DIRECTORY_SEPARATOR . 'cvf_*.cache') ?: [] as $file) {
        @unlink($file);
    }
}
