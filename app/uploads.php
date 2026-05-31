<?php

declare(strict_types=1);

function cv_normalize_uploads(array $files): array
{
    if (!isset($files['name']) || !is_array($files['name'])) {
        return [$files];
    }

    $normalized = [];

    foreach ($files['name'] as $index => $name) {
        $normalized[] = [
            'name' => $name,
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$index] ?? 0,
        ];
    }

    return $normalized;
}

function cv_upload_original_extension(array $file): string
{
    return strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
}

function cv_upload_error_message(int $errorCode, string $kind = 'file'): string
{
    return match ($errorCode) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => ucfirst($kind) . ' exceeds the upload limit.',
        UPLOAD_ERR_PARTIAL => ucfirst($kind) . ' upload was interrupted before completion.',
        UPLOAD_ERR_NO_FILE => 'No ' . $kind . ' was selected.',
        UPLOAD_ERR_NO_TMP_DIR => 'Temporary upload directory is missing on the server.',
        UPLOAD_ERR_CANT_WRITE => ucfirst($kind) . ' could not be written to storage.',
        UPLOAD_ERR_EXTENSION => ucfirst($kind) . ' was blocked by a server extension.',
        default => ucfirst($kind) . ' upload failed.',
    };
}

function cv_image_extension_from_mime(string $mime): string
{
    return match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg',
    };
}

function cv_media_type_from_context(string $context): string
{
    return str_contains($context, 'poster') ? 'poster' : 'image';
}

function cv_open_image_resource(string $tmpPath, string $mime): mixed
{
    return match ($mime) {
        'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($tmpPath) : false,
        'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($tmpPath) : false,
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmpPath) : false,
        default => false,
    };
}

function cv_save_image_resource(mixed $image, string $destination, string $mime, int $quality = 82): bool
{
    return match ($mime) {
        'image/jpeg' => function_exists('imagejpeg') ? imagejpeg($image, $destination, $quality) : false,
        'image/png' => function_exists('imagepng') ? imagepng($image, $destination, 6) : false,
        'image/webp' => function_exists('imagewebp') ? imagewebp($image, $destination, $quality) : false,
        default => false,
    };
}

function cv_destroy_image_resource(mixed $image): void
{
    if (PHP_VERSION_ID < 80000 && is_resource($image) && get_resource_type($image) === 'gd') {
        @imagedestroy($image);
    }
}

function cv_resize_canvas(int $sourceWidth, int $sourceHeight, int $maxWidth, int $maxHeight): array
{
    $ratio = min($maxWidth / max($sourceWidth, 1), $maxHeight / max($sourceHeight, 1), 1);

    return [
        max(1, (int) round($sourceWidth * $ratio)),
        max(1, (int) round($sourceHeight * $ratio)),
    ];
}

function cv_process_image_upload(array $file, string $context = 'general', string $altRu = '', string $altEn = ''): array
{
    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => cv_upload_error_message($errorCode, 'image')];
    }

    if (cv_cloudinary_enabled()) {
        $result = cv_upload_image_to_cloudinary($file, [
            'folder' => cv_config('cloudinary.folder', 'portfolio-showcase') . '/' . $context,
        ]);

        if (($result['ok'] ?? false) === true) {
            $url = (string) ($result['url'] ?? '');
            $data = is_array($result['data'] ?? null) ? $result['data'] : [];

            try {
                cv_store_media_library_record([
                    'type'       => cv_media_type_from_context($context),
                    'file_path'  => $url,
                    'thumb_path' => $url,
                    'mime_type'  => (string) ($data['resource_type'] ?? 'image') . '/' . (string) ($data['format'] ?? 'jpg'),
                    'bytes'      => (int) ($data['bytes'] ?? 0),
                    'width'      => (int) ($data['width'] ?? 0),
                    'height'     => (int) ($data['height'] ?? 0),
                    'alt_ru'     => $altRu,
                    'alt_en'     => $altEn,
                    'context'    => $context,
                    'created_at' => cv_now(),
                ]);
            } catch (Throwable $exception) {
                cv_log('upload', 'Cloudinary media library record failed', [
                    'context' => $context,
                    'error' => $exception->getMessage(),
                ]);
            }

            return [
                'ok'         => true,
                'path'       => $url,
                'thumb_path' => $url,
                'mime_type'  => 'image/' . (string) ($data['format'] ?? 'jpg'),
                'bytes'      => (int) ($data['bytes'] ?? 0),
                'width'      => (int) ($data['width'] ?? 0),
                'height'     => (int) ($data['height'] ?? 0),
                'optimized'  => true,
                'cloudinary' => true,
            ];
        }
    }

    return cv_process_database_image_upload($file, $context, $altRu, $altEn);
}

function cv_move_uploaded_file_safe(string $from, string $to): bool
{
    if (is_uploaded_file($from)) {
        return move_uploaded_file($from, $to);
    }

    return @rename($from, $to) || @copy($from, $to);
}

function cv_store_media_library_record(array $payload): int
{
    cv_execute(
        'INSERT INTO media_library (type, file_path, thumb_path, mime_type, bytes, width, height, alt_ru, alt_en, context, created_at)
         VALUES (:type, :file_path, :thumb_path, :mime_type, :bytes, :width, :height, :alt_ru, :alt_en, :context, :created_at)',
        $payload
    );
    return cv_last_insert_id();
}

function cv_process_image_upload_fallback(
    array $file,
    string $mime,
    int $sourceWidth,
    int $sourceHeight,
    string $context,
    string $altRu,
    string $altEn,
    string $imageDir,
    string $thumbDir,
    string $datePath
): array {
    $extension = cv_upload_original_extension($file);
    $fileName = date('His') . '-' . bin2hex(random_bytes(4)) . '.' . ($extension !== '' ? $extension : cv_image_extension_from_mime($mime));
    $relativePath = 'uploads/images/' . $datePath . '/' . $fileName;
    $thumbRelativePath = 'uploads/thumbs/' . $datePath . '/' . $fileName;
    $destination = cv_root($relativePath);
    $thumbDestination = cv_root($thumbRelativePath);

    if (!cv_move_uploaded_file_safe((string) $file['tmp_name'], $destination)) {
        return ['ok' => false, 'error' => 'Image upload could not be moved into local storage.'];
    }

    if (!@copy($destination, $thumbDestination)) {
        $thumbRelativePath = $relativePath;
    }

    cv_store_media_library_record([
        'type' => cv_media_type_from_context($context),
        'file_path' => $relativePath,
        'thumb_path' => $thumbRelativePath,
        'mime_type' => $mime,
        'bytes' => (int) filesize($destination),
        'width' => $sourceWidth,
        'height' => $sourceHeight,
        'alt_ru' => $altRu,
        'alt_en' => $altEn,
        'context' => $context,
        'created_at' => cv_now(),
    ]);

    return [
        'ok' => true,
        'path' => $relativePath,
        'thumb_path' => $thumbRelativePath,
        'mime_type' => $mime,
        'bytes' => (int) filesize($destination),
        'width' => $sourceWidth,
        'height' => $sourceHeight,
        'optimized' => false,
    ];
}

function cv_image_to_data_url(array $file, int $maxW = 480, int $maxH = 640): array
{
    if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => cv_upload_error_message((int) $file['error'], 'image')];
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if (!is_file($tmp)) {
        return ['ok' => false, 'error' => 'Temporary file missing.'];
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'Image exceeds 5 MB. Please compress the file first.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = $finfo ? (string) finfo_file($finfo, $tmp) : 'image/jpeg';

    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        return ['ok' => false, 'error' => 'Use JPG, PNG or WebP.'];
    }

    $size = @getimagesize($tmp);
    if (!$size) {
        return ['ok' => false, 'error' => 'Could not read image.'];
    }

    [$srcW, $srcH] = $size;

    if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png'  => @imagecreatefrompng($tmp),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : false,
            default      => false,
        };

        if ($src !== false) {
            $ratio = min($maxW / max($srcW, 1), $maxH / max($srcH, 1), 1.0);
            $dstW  = max(1, (int) round($srcW * $ratio));
            $dstH  = max(1, (int) round($srcH * $ratio));

            $dst = imagecreatetruecolor($dstW, $dstH);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
            imagedestroy($src);

            ob_start();
            imagejpeg($dst, null, 72);
            $bytes = ob_get_clean();
            imagedestroy($dst);

            if ($bytes !== false && $bytes !== '') {
                return ['ok' => true, 'url' => 'data:image/jpeg;base64,' . base64_encode($bytes)];
            }
        }
    }

    // GD not available — encode raw file bytes
    $raw = file_get_contents($tmp);
    if ($raw === false || $raw === '') {
        return ['ok' => false, 'error' => 'Could not read image data.'];
    }

    return ['ok' => true, 'url' => 'data:' . $mime . ';base64,' . base64_encode($raw)];
}

function cv_process_database_image_upload(array $file, string $context = 'general', string $altRu = '', string $altEn = ''): array
{
    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => cv_upload_error_message($errorCode, 'image')];
    }

    // Store directly in the database. Cloudinary stays reserved for videos.
    // Store image binary in media_data (serverless-safe: no filesystem needed).
    // file_path stores a compact serving URL /img?id=N — never embeds base64 in HTML.
    $dims = match (true) {
        str_contains($context, 'cover')   => [720, 540],
        str_contains($context, 'gallery') => [600, 450],
        str_contains($context, 'poster')  => [640, 360],
        str_contains($context, 'about')   => [400, 530],
        default                           => [640, 480],
    };
    $thumbDims = [240, 180];

    $full = cv_image_to_data_url($file, $dims[0], $dims[1]);
    if (!$full['ok']) {
        return $full;
    }

    $thumb = cv_image_to_data_url($file, $thumbDims[0], $thumbDims[1]);

    // Strip "data:image/jpeg;base64," prefix — store only the raw base64 payload.
    $stripPrefix = static function (string $dataUrl): string {
        $comma = strpos($dataUrl, ',');
        return $comma !== false ? substr($dataUrl, $comma + 1) : $dataUrl;
    };

    $fullB64  = $stripPrefix($full['url']);
    $thumbB64 = $thumb['ok'] ? $stripPrefix($thumb['url']) : $fullB64;

    try {
        $mediaId = cv_store_media_library_record([
            'type'       => cv_media_type_from_context($context),
            'file_path'  => 'pending',
            'thumb_path' => 'pending',
            'mime_type'  => 'image/jpeg',
            'bytes'      => (int) strlen($fullB64),
            'width'      => $dims[0],
            'height'     => $dims[1],
            'alt_ru'     => $altRu,
            'alt_en'     => $altEn,
            'context'    => $context,
            'created_at' => cv_now(),
        ]);

        if ($mediaId > 0) {
            cv_execute(
                'INSERT INTO media_data (id, image_data, thumb_data) VALUES (:id, :image_data, :thumb_data)',
                ['id' => $mediaId, 'image_data' => $fullB64, 'thumb_data' => $thumbB64]
            );
            $servingUrl      = '/img?id=' . $mediaId;
            $thumbServingUrl = '/img?id=' . $mediaId . '&t=1';
            cv_execute(
                'UPDATE media_library SET file_path = :fp, thumb_path = :tp WHERE id = :id',
                ['fp' => $servingUrl, 'tp' => $thumbServingUrl, 'id' => $mediaId]
            );
        } else {
            // media_data table may not exist yet - fall back to full data URL.
            $servingUrl      = $full['url'];
            $thumbServingUrl = $thumb['ok'] ? $thumb['url'] : $full['url'];
        }
    } catch (Throwable $exception) {
        cv_log('upload', 'Database image storage failed', [
            'context' => $context,
            'error' => $exception->getMessage(),
        ]);

        return [
            'ok' => false,
            'error' => 'Image could not be stored in the database. Run the media_data migration and make sure image columns use TEXT.',
        ];
    }

    return [
        'ok'         => true,
        'path'       => $servingUrl,
        'thumb_path' => $thumbServingUrl,
        'mime_type'  => 'image/jpeg',
        'bytes'      => strlen($fullB64),
        'width'      => $dims[0],
        'height'     => $dims[1],
        'optimized'  => true,
    ];
}
