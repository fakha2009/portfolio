<?php

declare(strict_types=1);

function cv_cloudinary_enabled(): bool
{
    return cv_boolean(cv_config('cloudinary.enabled', false))
        && (string) cv_config('cloudinary.cloud_name') !== ''
        && (string) cv_config('cloudinary.api_key') !== ''
        && (string) cv_config('cloudinary.api_secret') !== '';
}

function cv_cloudinary_signature(array $params): string
{
    ksort($params);
    $parts = [];

    foreach ($params as $key => $value) {
        if ($value === null || $value === '' || in_array($key, ['file', 'api_key', 'signature'], true)) {
            continue;
        }

        $parts[] = $key . '=' . $value;
    }

    return sha1(implode('&', $parts) . (string) cv_config('cloudinary.api_secret'));
}

function cv_upload_image_to_cloudinary(array $file, array $options = []): array
{
    if (!cv_cloudinary_enabled()) {
        return ['ok' => false, 'error' => 'Cloudinary is not configured.'];
    }

    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => cv_upload_error_message($errorCode, 'image')];
    }

    if (!is_file((string) ($file['tmp_name'] ?? ''))) {
        return ['ok' => false, 'error' => 'Temporary image file is missing.'];
    }

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'cURL is not available on this server.'];
    }

    $finfo  = finfo_open(FILEINFO_MIME_TYPE);
    $mime   = $finfo ? (string) finfo_file($finfo, (string) $file['tmp_name']) : 'image/jpeg';
    $ext    = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

    $params = [
        'folder'    => $options['folder'] ?? cv_config('cloudinary.folder', 'portfolio-showcase'),
        'timestamp' => time(),
        'overwrite' => 'false',
    ];

    if (!empty($options['public_id'])) {
        $params['public_id'] = (string) $options['public_id'];
    }

    $endpoint = sprintf(
        'https://api.cloudinary.com/v1_1/%s/image/upload',
        rawurlencode((string) cv_config('cloudinary.cloud_name'))
    );

    $postFields = array_merge($params, [
        'api_key'   => (string) cv_config('cloudinary.api_key'),
        'signature' => cv_cloudinary_signature($params),
        'file'      => new CURLFile((string) $file['tmp_name'], $mime, (string) ($file['name'] ?? 'image.' . $ext)),
    ]);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST            => true,
        CURLOPT_POSTFIELDS      => $postFields,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_CONNECTTIMEOUT  => 15,
        CURLOPT_TIMEOUT         => 60,
        CURLOPT_SSL_VERIFYPEER  => true,
        CURLOPT_HTTPHEADER      => ['Accept: application/json'],
    ]);

    $responseRaw = curl_exec($ch);
    $error       = curl_error($ch);
    $statusCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($responseRaw === false || $error !== '') {
        return ['ok' => false, 'error' => 'Cloudinary request failed: ' . ($error ?: 'Unknown error.')];
    }

    $response = json_decode((string) $responseRaw, true);
    if (!is_array($response)) {
        return ['ok' => false, 'error' => 'Cloudinary returned an invalid response.'];
    }

    if ($statusCode >= 400 || isset($response['error'])) {
        return ['ok' => false, 'error' => (string) ($response['error']['message'] ?? 'Cloudinary upload error.')];
    }

    return ['ok' => true, 'data' => $response, 'url' => (string) ($response['secure_url'] ?? '')];
}

function cv_upload_video_to_cloudinary(array $file, array $options = []): array
{
    if (!cv_cloudinary_enabled()) {
        return ['ok' => false, 'error' => 'Cloudinary is not configured. Fill cloud name, API key, and API secret first.'];
    }

    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => cv_upload_error_message($errorCode, 'video')];
    }

    $maxVideoSize = (int) cv_config('upload.max_video_size', 9437184);
    if (($file['size'] ?? 0) > $maxVideoSize) {
        return ['ok' => false, 'error' => 'Video exceeds the configured ' . round($maxVideoSize / 1048576, 1) . ' MB limit.'];
    }

    if (!is_file((string) ($file['tmp_name'] ?? ''))) {
        return ['ok' => false, 'error' => 'Temporary video file is missing on the server.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? (string) finfo_file($finfo, (string) $file['tmp_name']) : '';

    $extension = cv_upload_original_extension($file);
    if (!in_array($extension, (array) cv_config('upload.video_extensions', []), true)) {
        return ['ok' => false, 'error' => 'Unsupported video extension.'];
    }

    if (!in_array($mime, (array) cv_config('upload.video_mime_types', []), true)) {
        return ['ok' => false, 'error' => 'Unsupported video format.'];
    }

    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'cURL is not available on this server.'];
    }

    $params = [
        'folder' => $options['folder'] ?? cv_config('cloudinary.folder', 'portfolio-showcase'),
        'timestamp' => time(),
        'resource_type' => 'video',
        'overwrite' => 'false',
    ];

    if (!empty($options['public_id'])) {
        $params['public_id'] = (string) $options['public_id'];
    }

    $endpoint = sprintf(
        'https://api.cloudinary.com/v1_1/%s/video/upload',
        rawurlencode((string) cv_config('cloudinary.cloud_name'))
    );

    $postFields = array_merge($params, [
        'api_key' => (string) cv_config('cloudinary.api_key'),
        'signature' => cv_cloudinary_signature($params),
        'file' => new CURLFile((string) $file['tmp_name'], $mime, (string) ($file['name'] ?? 'upload.' . $extension)),
    ]);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);

    $responseRaw = curl_exec($ch);
    $error = curl_error($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($responseRaw === false || $error !== '') {
        return ['ok' => false, 'error' => 'Cloudinary request failed: ' . ($error !== '' ? $error : 'Unknown cURL error.')];
    }

    $response = json_decode((string) $responseRaw, true);
    if (!is_array($response)) {
        return ['ok' => false, 'error' => 'Cloudinary returned an invalid response payload.'];
    }

    if ($statusCode >= 400 || isset($response['error'])) {
        return ['ok' => false, 'error' => (string) ($response['error']['message'] ?? 'Cloudinary upload error.')];
    }

    return ['ok' => true, 'data' => $response];
}
