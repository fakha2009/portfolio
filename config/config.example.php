<?php

declare(strict_types=1);

return [
    'app' => [
        // Final public site URL without a trailing slash.
        'name' => 'Fakhriddin Portfolio',
        'url' => 'https://fakhriddin-portfolio.vercel.app',

        // Leave empty when the app is deployed at the domain root.
        'base_path' => '',

        'timezone' => 'Asia/Dushanbe',
        'debug' => false,
        'multilingual' => true,
        'default_locale' => 'ru',
        'locales' => [
            'ru' => 'Russian',
            'en' => 'English',
        ],

        // Hidden admin URL slug.
        'admin_slug' => 'secure-portal-x9a7',

        'session_name' => 'cvf_owner_session',
        'theme_default' => 'dark',
        'analytics_cookie' => 'cvf_vid',
    ],

    'db' => [
        // Neon PostgreSQL connection parameters.
        'host' => 'ep-royal-boat-ap98zyh3-pooler.c-7.us-east-1.aws.neon.tech',
        'port' => 5432,
        'database' => 'neondb',
        'username' => 'neondb_owner',
        'password' => 'replace-with-real-db-password',
        'sslmode' => 'require',
    ],

    'cache' => [
        // Safe performance cache. Uses Redis when REDIS_URL + ext-redis are available,
        // otherwise falls back to /tmp file cache on warm serverless instances.
        'enabled' => true,
        'ttl' => 60,
        'prefix' => 'cvf:',
        'redis_url' => getenv('REDIS_URL') ?: '',
    ],

    'security' => [
        // Replace with a long random string before production launch.
        'csrf_key' => 'replace-with-a-long-random-string',
        'session_lifetime' => 7200,
        'login_max_attempts' => 5,
        'login_window_seconds' => 900,
        'contact_max_attempts' => 4,
        'contact_window_seconds' => 1800,
    ],

    'smtp' => [
        // Optional. Leave disabled until real SMTP credentials are ready.
        'enabled' => false,
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'your-smtp-user@example.com',
        'password' => 'your-smtp-password',
        'encryption' => 'tls',
        'from_email' => 'noreply@example.com',
        'from_name' => 'Fakhriddin Portfolio',
        'to_email' => 'fakhridinkon2009@gmail.com',
        'timeout' => 15,
    ],

    'telegram' => [
        // Optional. When disabled or incomplete, the site still works normally.
        'enabled' => false,
        'bot_token' => '',
        'chat_id' => '',
    ],

    'cloudinary' => [
        // Optional. Enable only when video uploads must work immediately.
        'enabled' => false,
        'cloud_name' => 'your-cloud-name',
        'api_key' => 'your-api-key',
        'api_secret' => 'your-api-secret',
        'folder' => 'portfolio-showcase',
    ],

    'upload' => [
        'max_image_size' => 5 * 1024 * 1024,
        'max_video_size' => 9 * 1024 * 1024,
        'image_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'video_extensions' => ['mp4', 'mov', 'webm', 'm4v'],
        'image_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'video_mime_types' => ['video/mp4', 'video/quicktime', 'video/webm', 'video/x-m4v'],
        'project_image_width' => 2200,
        'project_image_height' => 1600,
        'thumbnail_width' => 720,
        'thumbnail_height' => 540,
    ],
];
