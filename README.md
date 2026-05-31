# fakhriddin-portfolio

**Premium owner-controlled portfolio platform** built for real shared-hosting constraints, with a hidden admin panel, bilingual public pages, and a complete content management workflow.

## Why this repo is awesome

- Built for PHP 8.3 + MySQL and shared hosting like InfinityFree.
- Zero Composer, zero Node.js, zero shell access required.
- Hidden owner-only admin portal behind a configurable slug.
- Fast public website with SEO, sitemap, robots, and theme switching.
- Optional Cloudinary and Telegram integrations for professional showcase and notifications.

## What you get

### Public site features

- multilingual homepage, projects, blog, testimonials, FAQ, and contact form
- live project gallery with category filters and detail pages
- blog listing and individual posts with SEO-friendly routing
- theme selector stored in cookies for light/dark presentation
- generated `sitemap.xml` and `robots.txt` support
- analytics tracking built into the app without third-party analytics bundling
- image and video media delivery with fallback local storage and Cloudinary support

### Hidden admin panel

- secure single-owner login using `admin_slug` and email/username auth
- admin dashboard with content sections: hero, about, skills, services, testimonials, FAQ, projects, blog, categories, media, messages, backup, SEO, security, theme, and settings
- owner profile and password management
- hidden admin entrypoint with no identifiable URL in the public UI
- rate-limited authentication and CSRF protection for all POST actions
- full media library management for images, thumbnails, and video uploads
- built-in backup utilities and database-safe export options

### Devops-friendly design

- ready for FTP upload to shared hosting
- `config/config.example.php` as a safe template, never commit real secrets
- `storage/schema.sql` for clean database setup
- `storage/seed_demo.sql` to bootstrap demo content very quickly
- no external package manager required on the server side

## Stack

- PHP 8.3
- MySQL via PDO
- PHPMailer bundled directly in `vendor/PHPMailer`
- vanilla JavaScript and handcrafted CSS
- Cloudinary signed uploads for media if enabled
- optional Telegram Bot API notifications

## Installation notes

1. Copy `config/config.example.php` to `config/config.php`.
2. Fill in your real database credentials, admin slug, and app settings.
3. Import `storage/schema.sql` into your MySQL database.
4. Optionally import `storage/seed_demo.sql` for starter content and owner account.
5. Upload the repository contents directly to your hosting root.

## Configuration checklist

Required values in `config/config.php`:

- `app.url`
- `app.base_path`
- `app.admin_slug`
- `security.csrf_key`
- `db.host`
- `db.port`
- `db.database`
- `db.username`
- `db.password`

Optional integrations:

- SMTP for outgoing email notifications
- Cloudinary for signed media uploads and video demo hosting
- Telegram for instant admin notifications

## Deployment guidance

This project is engineered for environments with strict hosting limits:

- no SSH / shell required
- no Composer install on host
- no Node.js runtime needed
- deploy by FTP or File Manager
- path-based hidden admin panel keeps the admin portal off the public site map

## SQL import order

1. `storage/schema.sql`
2. `storage/seed_demo.sql` (optional, for demo content and default admin account)

### Notes

- `schema.sql` creates all application tables safely.
- `seed_demo.sql` is optional and useful for quick setup.
- If you skip seeded content, create your owner admin manually after the first install.

## Owner-first security

This repository is designed to be owned by a single operator. The admin interface is intentionally hidden and the whole system is optimized for a secure, easy-to-maintain portfolio deployment.

> This is not a generic template. It is a polished, production-ready portfolio engine built to let a single owner control every public page, media asset, and site setting from one hidden PHP admin dashboard.


If you imported `storage/seed_demo.sql`, the default account is:

- username: `owner`
- email: `fakhridinkon2009@gmail.com`
- password: `ChangeMe!2026`

After first login:

1. open the hidden admin
2. go to `Security`
3. change the password immediately
4. update the profile email/name if needed

## Hidden Admin URL

The admin path is controlled by:

- `app.admin_slug` in `config/config.php`

Example:

- `https://your-domain.example/secure-portal-x9a7/login`

There is no public link to this panel in the site UI.

## Writable Folders

These locations must be writable by PHP on hosting:

- `uploads/`
- `uploads/images/`
- `uploads/thumbs/`
- `uploads/videos/`
- `storage/logs/`
- `storage/backups/`
- `storage/exports/`
- `storage/tmp/`

The app tries to create these folders if they are missing, but it is better to upload them as part of the project.

Added protection for deployment:

- `uploads/.htaccess` blocks PHP execution inside uploads
- `.htaccess` files inside private folders deny direct access as defense in depth
- `index.html` placeholders are included in writable folders
- root `.htaccess` blocks direct access to `app/`, `config/`, `storage/`, `templates/`, and `vendor/`

## SMTP Setup

The contact form always saves messages into MySQL first.

SMTP is only a notification layer.

To enable SMTP:

1. set `smtp.enabled` to `true`
2. fill `smtp.host`
3. fill `smtp.port`
4. fill `smtp.username`
5. fill `smtp.password`
6. fill `smtp.encryption`
7. fill `smtp.from_email`
8. fill `smtp.from_name`
9. fill `smtp.to_email`

If SMTP is disabled:

- the contact form still works
- messages still save to MySQL
- messages still appear in the admin inbox

If SMTP is enabled but fails:

- the message still saves
- the admin inbox still contains the submission
- a graceful public success flow is preserved

## Cloudinary Setup

Project videos are not stored locally.

They upload from PHP to Cloudinary with a signed server-side request.

To enable Cloudinary:

1. set `cloudinary.enabled` to `true`
2. fill `cloudinary.cloud_name`
3. fill `cloudinary.api_key`
4. fill `cloudinary.api_secret`
5. optionally adjust `cloudinary.folder`

If Cloudinary is disabled:

- the site still works
- project pages still load
- image uploads still work locally
- admin shows a clear error for video uploads instead of failing silently

## Telegram Setup

Telegram notifications are optional.

To enable later:

1. create a bot with BotFather
2. set `telegram.enabled` to `true`
3. fill `telegram.bot_token`
4. fill `telegram.chat_id`

If Telegram is disabled or incomplete:

- the site still works
- contact form still saves to MySQL
- SMTP remains independent
- Telegram failures do not break submissions

## Public / Admin Launch Test

After upload, SQL import, and config setup, verify:

1. home page loads in RU and EN
2. `/projects` loads
3. at least one project detail page loads
4. theme toggle works
5. language switching works
6. `robots.txt` opens
7. `sitemap.xml` opens
8. favicon and header logo load
9. hidden admin login page opens under your slug
10. admin login works
11. dashboard opens
12. settings save correctly
13. projects page in admin opens
14. local image upload works
15. contact form submission appears in inbox
16. Cloudinary video upload works if enabled
17. SMTP notification works if enabled
18. Telegram notification works if enabled

## Troubleshooting

### 1. Site shows the setup screen

Check:

- `config/config.php` exists
- DB credentials are correct
- `storage/schema.sql` was imported

### 2. CSS or images do not load

Check:

- `app.url` is correct
- `app.base_path` is correct
- files were uploaded into the real web root
- `assets/` and `uploads/` exist on hosting

### 3. Admin login URL does not open

Check:

- `app.admin_slug` in `config/config.php`
- root `.htaccess` was uploaded
- the project is deployed from the root of `htdocs`, not inside a nested folder

### 4. Contact form saves but no email arrives

Check:

- `smtp.enabled`
- SMTP host/port/user/password
- provider-specific requirements like app passwords

The message should still be visible in the admin inbox.

### 5. Video upload fails

Check:

- `cloudinary.enabled`
- cloud name, API key, API secret
- cURL availability
- file size under 9 MB on InfinityFree

### 6. Telegram does not send

Check:

- `telegram.enabled`
- bot token
- chat ID
- whether the bot has access to the destination chat

### 7. Image uploads fail

Check:

- `uploads/` folders are writable
- PHP upload size on hosting
- MIME type and extension of the file
- GD availability for optimization

If GD is not available, the uploader falls back to storing the original image when possible.

## Production Hardening

- replace the demo admin password immediately
- use a long random `security.csrf_key`
- set the final hidden `app.admin_slug`
- keep `app.debug` set to `false`
- keep Cloudinary, SMTP, and Telegram credentials private
- use HTTPS only
- export JSON backups regularly from the admin panel
- remove or replace demo content before launch if needed

## Notes

- the app is ready to run from `config/config.php` without editing business logic
- optional integrations are safe to leave disabled
- local images remain on hosting
- project videos remain on Cloudinary
