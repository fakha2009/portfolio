<?php

declare(strict_types=1);

function cv_available_locales(): array
{
    return (array) cv_config('app.locales', ['ru' => 'Русский']);
}

function cv_default_locale(): string
{
    return (string) cv_config('app.default_locale', 'ru');
}

function cv_current_locale(): string
{
    return $GLOBALS['cv_locale'] ?? cv_default_locale();
}

function cv_set_locale(string $locale): void
{
    $available = cv_available_locales();
    $GLOBALS['cv_locale'] = array_key_exists($locale, $available) ? $locale : cv_default_locale();
}

function cv_detect_locale(array $segments): array
{
    if (!cv_config('app.multilingual', true) || $segments === []) {
        return [cv_default_locale(), $segments];
    }

    $candidate = $segments[0];

    if (array_key_exists($candidate, cv_available_locales())) {
        array_shift($segments);

        return [$candidate, $segments];
    }

    return [cv_default_locale(), $segments];
}

function cv_translations(?string $locale = null): array
{
    static $cache = [];

    $locale = $locale ?? cv_current_locale();

    if (!isset($cache[$locale])) {
        $file = cv_root('app/lang/' . $locale . '.php');
        $cache[$locale] = is_file($file) ? require $file : [];
    }

    return $cache[$locale];
}

function cv_t(string $key, array $replace = [], ?string $locale = null): string
{
    $locale = $locale ?? cv_current_locale();
    $translations = cv_translations($locale);
    $value = cv_array_get($translations, $key, $key);

    foreach ($replace as $replaceKey => $replaceValue) {
        $value = str_replace(':' . $replaceKey, (string) $replaceValue, (string) $value);
    }

    return (string) $value;
}
