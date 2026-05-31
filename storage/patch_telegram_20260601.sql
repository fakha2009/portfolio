-- Patch: update Telegram handle from @fakhriddin2607 to @Fakhriddin_dev
-- Run in Neon SQL console

UPDATE site_settings
SET value_long = '@Fakhriddin_dev', updated_at = NOW()
WHERE group_key = 'site' AND setting_key = 'contact_telegram';

UPDATE site_settings
SET value_long = 'https://t.me/Fakhriddin_dev', updated_at = NOW()
WHERE group_key = 'social' AND setting_key = 'telegram';
