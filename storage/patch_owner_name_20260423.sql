-- Patch existing content after deployment (PostgreSQL / Neon version).
-- Run this against the production database if old owner-name values
-- are already stored in site_settings, content_blocks, or admin_users.

UPDATE admin_users
SET full_name  = 'Makhmadkhonzoda Fakhriddin',
    updated_at = NOW()
WHERE full_name IN ('Fakhriddin Makhmadkhonzoda', 'Fakhriddin Makhmadkhozoda', 'Фахриддин Махмадхонзода');

UPDATE site_settings
SET value_long = CASE
        WHEN locale_code = 'ru' AND group_key = 'site' AND setting_key = 'site_name'
            THEN 'Махмадхонзода Фахриддин'
        WHEN locale_code = 'en' AND group_key = 'site' AND setting_key = 'site_name'
            THEN 'Makhmadkhonzoda Fakhriddin'
        WHEN locale_code = 'ru' AND group_key = 'seo' AND setting_key = 'meta_title'
            THEN REPLACE(REPLACE(value_long, 'Фахриддин Махмадхонзода', 'Махмадхонзода Фахриддин'), 'Fakhriddin Makhmadkhonzoda', 'Makhmadkhonzoda Fakhriddin')
        WHEN locale_code = 'en' AND group_key = 'seo' AND setting_key = 'meta_title'
            THEN REPLACE(REPLACE(REPLACE(value_long, 'Fakhriddin Makhmadkhozoda', 'Makhmadkhonzoda Fakhriddin'), 'Fakhriddin Makhmadkhonzoda', 'Makhmadkhonzoda Fakhriddin'), 'Фахриддин Махмадхонзода', 'Махмадхонзода Фахриддин')
        ELSE value_long
    END,
    updated_at = NOW()
WHERE (group_key = 'site' AND setting_key = 'site_name')
   OR (group_key = 'seo'  AND setting_key = 'meta_title');

UPDATE content_blocks
SET payload_json = REPLACE(
        REPLACE(
            REPLACE(payload_json, 'Fakhriddin Makhmadkhozoda', 'Makhmadkhonzoda Fakhriddin'),
            'Fakhriddin Makhmadkhonzoda',
            'Makhmadkhonzoda Fakhriddin'
        ),
        'Фахриддин Махмадхонзода',
        'Махмадхонзода Фахриддин'
    ),
    updated_at = NOW()
WHERE block_key = 'hero';
