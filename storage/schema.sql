-- PostgreSQL schema for Neon.
-- Run this first, then seed_demo.sql for starter content.

DROP TABLE IF EXISTS rate_limits        CASCADE;
DROP TABLE IF EXISTS analytics_daily    CASCADE;
DROP TABLE IF EXISTS analytics_events   CASCADE;
DROP TABLE IF EXISTS contact_messages   CASCADE;
DROP TABLE IF EXISTS project_videos     CASCADE;
DROP TABLE IF EXISTS project_images     CASCADE;
DROP TABLE IF EXISTS projects           CASCADE;
DROP TABLE IF EXISTS project_categories CASCADE;
DROP TABLE IF EXISTS testimonials       CASCADE;
DROP TABLE IF EXISTS services           CASCADE;
DROP TABLE IF EXISTS skills             CASCADE;
DROP TABLE IF EXISTS faqs               CASCADE;
DROP TABLE IF EXISTS media_library      CASCADE;
DROP TABLE IF EXISTS content_blocks     CASCADE;
DROP TABLE IF EXISTS site_settings      CASCADE;
DROP TABLE IF EXISTS blog_posts         CASCADE;
DROP TABLE IF EXISTS admin_users        CASCADE;
DROP TABLE IF EXISTS php_sessions       CASCADE;

CREATE TABLE php_sessions (
    session_id   VARCHAR(128)  NOT NULL PRIMARY KEY,
    session_data TEXT          NOT NULL DEFAULT '',
    expires_at   TIMESTAMP     NOT NULL
);
CREATE INDEX idx_php_sessions_expires ON php_sessions (expires_at);

CREATE TABLE admin_users (
    id              SERIAL        PRIMARY KEY,
    username        VARCHAR(80)   NOT NULL UNIQUE,
    email           VARCHAR(190)  NOT NULL UNIQUE,
    full_name       VARCHAR(150)  NOT NULL,
    password_hash   VARCHAR(255)  NOT NULL,
    last_login_at   TIMESTAMP     NULL,
    last_login_ip   VARCHAR(64)   NULL,
    created_at      TIMESTAMP     NOT NULL,
    updated_at      TIMESTAMP     NOT NULL
);

CREATE TABLE site_settings (
    id           SERIAL       PRIMARY KEY,
    group_key    VARCHAR(80)  NOT NULL,
    setting_key  VARCHAR(80)  NOT NULL,
    locale_code  VARCHAR(10)  NOT NULL DEFAULT '*',
    value_long   TEXT         NOT NULL,
    value_type   VARCHAR(20)  NOT NULL DEFAULT 'text',
    updated_at   TIMESTAMP    NOT NULL,
    UNIQUE (group_key, setting_key, locale_code)
);
CREATE INDEX idx_site_settings_group ON site_settings (group_key, locale_code);

CREATE TABLE content_blocks (
    id           SERIAL       PRIMARY KEY,
    block_key    VARCHAR(80)  NOT NULL,
    locale_code  VARCHAR(10)  NOT NULL,
    payload_json TEXT         NOT NULL,
    status       VARCHAR(20)  NOT NULL DEFAULT 'published',
    updated_at   TIMESTAMP    NOT NULL,
    UNIQUE (block_key, locale_code)
);
CREATE INDEX idx_content_blocks_status ON content_blocks (block_key, status);

CREATE TABLE media_library (
    id          SERIAL        PRIMARY KEY,
    type        VARCHAR(20)   NOT NULL DEFAULT 'image',
    file_path   TEXT          NOT NULL,
    thumb_path  TEXT          NULL,
    mime_type   VARCHAR(120)  NOT NULL,
    bytes       BIGINT        NOT NULL DEFAULT 0,
    width       INTEGER       NULL,
    height      INTEGER       NULL,
    alt_ru      VARCHAR(255)  NULL,
    alt_en      VARCHAR(255)  NULL,
    context     VARCHAR(120)  NULL,
    created_at  TIMESTAMP     NOT NULL
);
CREATE INDEX idx_media_context ON media_library (context);
CREATE INDEX idx_media_type    ON media_library (type);

CREATE TABLE media_data (
    id          INTEGER  NOT NULL PRIMARY KEY REFERENCES media_library (id) ON DELETE CASCADE,
    image_data  TEXT     NOT NULL,
    thumb_data  TEXT     NULL
);

CREATE TABLE skills (
    id              SERIAL        PRIMARY KEY,
    group_ru        VARCHAR(120)  NOT NULL,
    group_en        VARCHAR(120)  NOT NULL,
    title_ru        VARCHAR(160)  NOT NULL,
    title_en        VARCHAR(160)  NOT NULL,
    description_ru  TEXT          NOT NULL,
    description_en  TEXT          NOT NULL,
    skill_level     VARCHAR(80)   NULL,
    icon            VARCHAR(80)   NULL,
    sort_order      INTEGER       NOT NULL DEFAULT 0,
    status          VARCHAR(20)   NOT NULL DEFAULT 'published',
    created_at      TIMESTAMP     NOT NULL,
    updated_at      TIMESTAMP     NOT NULL
);
CREATE INDEX idx_skills_status ON skills (status, sort_order);

CREATE TABLE services (
    id              SERIAL        PRIMARY KEY,
    title_ru        VARCHAR(180)  NOT NULL,
    title_en        VARCHAR(180)  NOT NULL,
    description_ru  TEXT          NOT NULL,
    description_en  TEXT          NOT NULL,
    cta_ru          VARCHAR(140)  NULL,
    cta_en          VARCHAR(140)  NULL,
    accent          VARCHAR(40)   NULL,
    sort_order      INTEGER       NOT NULL DEFAULT 0,
    status          VARCHAR(20)   NOT NULL DEFAULT 'published',
    created_at      TIMESTAMP     NOT NULL,
    updated_at      TIMESTAMP     NOT NULL
);
CREATE INDEX idx_services_status ON services (status, sort_order);

CREATE TABLE faqs (
    id           SERIAL        PRIMARY KEY,
    question_ru  VARCHAR(255)  NOT NULL,
    question_en  VARCHAR(255)  NOT NULL,
    answer_ru    TEXT          NOT NULL,
    answer_en    TEXT          NOT NULL,
    sort_order   INTEGER       NOT NULL DEFAULT 0,
    status       VARCHAR(20)   NOT NULL DEFAULT 'published',
    created_at   TIMESTAMP     NOT NULL,
    updated_at   TIMESTAMP     NOT NULL
);
CREATE INDEX idx_faqs_status ON faqs (status, sort_order);

CREATE TABLE testimonials (
    id          SERIAL        PRIMARY KEY,
    name        VARCHAR(140)  NOT NULL,
    role_ru     VARCHAR(180)  NOT NULL,
    role_en     VARCHAR(180)  NOT NULL,
    company     VARCHAR(180)  NULL,
    quote_ru    TEXT          NOT NULL,
    quote_en    TEXT          NOT NULL,
    avatar      TEXT          NULL,
    rating      SMALLINT      NOT NULL DEFAULT 5,
    sort_order  INTEGER       NOT NULL DEFAULT 0,
    status      VARCHAR(20)   NOT NULL DEFAULT 'published',
    created_at  TIMESTAMP     NOT NULL,
    updated_at  TIMESTAMP     NOT NULL
);
CREATE INDEX idx_testimonials_status ON testimonials (status, sort_order);

CREATE TABLE project_categories (
    id              SERIAL        PRIMARY KEY,
    name_ru         VARCHAR(150)  NOT NULL,
    name_en         VARCHAR(150)  NOT NULL,
    slug            VARCHAR(150)  NOT NULL UNIQUE,
    description_ru  TEXT          NULL,
    description_en  TEXT          NULL,
    sort_order      INTEGER       NOT NULL DEFAULT 0,
    status          VARCHAR(20)   NOT NULL DEFAULT 'published',
    created_at      TIMESTAMP     NOT NULL,
    updated_at      TIMESTAMP     NOT NULL
);
CREATE INDEX idx_project_categories_status ON project_categories (status, sort_order);

CREATE TABLE projects (
    id                    SERIAL        PRIMARY KEY,
    category_id           INTEGER       NULL REFERENCES project_categories (id) ON DELETE SET NULL,
    title_ru              VARCHAR(200)  NOT NULL,
    title_en              VARCHAR(200)  NOT NULL,
    slug                  VARCHAR(180)  NOT NULL UNIQUE,
    short_description_ru  TEXT          NOT NULL,
    short_description_en  TEXT          NOT NULL,
    full_description_ru   TEXT          NOT NULL,
    full_description_en   TEXT          NOT NULL,
    role_ru               VARCHAR(190)  NULL,
    role_en               VARCHAR(190)  NULL,
    technologies          VARCHAR(255)  NULL,
    external_url          VARCHAR(255)  NULL,
    featured              SMALLINT      NOT NULL DEFAULT 0,
    status                VARCHAR(20)   NOT NULL DEFAULT 'draft',
    sort_order            INTEGER       NOT NULL DEFAULT 0,
    cover_image           TEXT          NULL,
    cover_alt_ru          VARCHAR(255)  NULL,
    cover_alt_en          VARCHAR(255)  NULL,
    og_image              TEXT          NULL,
    client_ru             TEXT          NULL,
    client_en             TEXT          NULL,
    problem_ru            TEXT          NULL,
    problem_en            TEXT          NULL,
    process_ru            TEXT          NULL,
    process_en            TEXT          NULL,
    solution_ru           TEXT          NULL,
    solution_en           TEXT          NULL,
    result_ru             TEXT          NULL,
    result_en             TEXT          NULL,
    seo_title_ru          VARCHAR(255)  NULL,
    seo_title_en          VARCHAR(255)  NULL,
    seo_description_ru    TEXT          NULL,
    seo_description_en    TEXT          NULL,
    created_at            TIMESTAMP     NOT NULL,
    updated_at            TIMESTAMP     NOT NULL
);
CREATE INDEX idx_projects_status   ON projects (status, featured, sort_order);
CREATE INDEX idx_projects_category ON projects (category_id);

CREATE TABLE blog_posts (
    id          SERIAL        PRIMARY KEY,
    title_ru    VARCHAR(255)  NOT NULL,
    title_en    VARCHAR(255)  NOT NULL,
    slug        VARCHAR(255)  NOT NULL UNIQUE,
    excerpt_ru  TEXT          NOT NULL,
    excerpt_en  TEXT          NOT NULL,
    body_ru     TEXT          NOT NULL,
    body_en     TEXT          NOT NULL,
    cover_image TEXT          NULL,
    tags        VARCHAR(255)  NULL,
    published_at TIMESTAMP    NULL,
    featured    SMALLINT      NOT NULL DEFAULT 0,
    status      VARCHAR(20)   NOT NULL DEFAULT 'draft',
    sort_order  INTEGER       NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL,
    updated_at  TIMESTAMP     NOT NULL
);
CREATE INDEX idx_blog_posts_status ON blog_posts (status, featured, sort_order, published_at);

CREATE TABLE project_images (
    id          SERIAL        PRIMARY KEY,
    project_id  INTEGER       NOT NULL REFERENCES projects (id) ON DELETE CASCADE,
    file_path   TEXT          NOT NULL,
    thumb_path  TEXT          NULL,
    alt_ru      VARCHAR(255)  NULL,
    alt_en      VARCHAR(255)  NULL,
    is_cover    SMALLINT      NOT NULL DEFAULT 0,
    sort_order  INTEGER       NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL
);
CREATE INDEX idx_project_images_sort ON project_images (project_id, sort_order);

CREATE TABLE project_videos (
    id                    SERIAL        PRIMARY KEY,
    project_id            INTEGER       NOT NULL REFERENCES projects (id) ON DELETE CASCADE,
    cloudinary_public_id  VARCHAR(255)  NOT NULL,
    secure_url            VARCHAR(500)  NOT NULL,
    format                VARCHAR(40)   NULL,
    duration              NUMERIC(10,2) NULL,
    bytes                 BIGINT        NULL,
    width                 INTEGER       NULL,
    height                INTEGER       NULL,
    poster_image          TEXT          NULL,
    poster_alt_ru         VARCHAR(255)  NULL,
    poster_alt_en         VARCHAR(255)  NULL,
    created_at            TIMESTAMP     NOT NULL,
    updated_at            TIMESTAMP     NOT NULL
);
CREATE INDEX idx_project_videos_project ON project_videos (project_id);

CREATE TABLE contact_messages (
    id              SERIAL        PRIMARY KEY,
    locale_code     VARCHAR(10)   NOT NULL DEFAULT 'ru',
    name            VARCHAR(140)  NOT NULL,
    email           VARCHAR(190)  NOT NULL,
    phone           VARCHAR(80)   NULL,
    company         VARCHAR(160)  NULL,
    budget          VARCHAR(120)  NULL,
    message         TEXT          NOT NULL,
    page_url        VARCHAR(255)  NULL,
    ip_hash         VARCHAR(64)   NULL,
    user_agent      VARCHAR(500)  NULL,
    referrer        VARCHAR(255)  NULL,
    is_read         SMALLINT      NOT NULL DEFAULT 0,
    status          VARCHAR(20)   NOT NULL DEFAULT 'new',
    smtp_sent       SMALLINT      NOT NULL DEFAULT 0,
    telegram_sent   SMALLINT      NOT NULL DEFAULT 0,
    created_at      TIMESTAMP     NOT NULL,
    updated_at      TIMESTAMP     NOT NULL
);
CREATE INDEX idx_contact_status ON contact_messages (status, is_read, created_at);
CREATE INDEX idx_contact_locale ON contact_messages (locale_code);

CREATE TABLE analytics_events (
    id               BIGSERIAL     PRIMARY KEY,
    event_date       DATE          NOT NULL,
    visitor_token    VARCHAR(80)   NOT NULL,
    event_type       VARCHAR(50)   NOT NULL,
    page_path        VARCHAR(255)  NULL,
    project_id       INTEGER       NULL REFERENCES projects (id) ON DELETE SET NULL,
    referrer_host    VARCHAR(190)  NULL,
    device_type      VARCHAR(20)   NULL,
    theme_preference VARCHAR(20)   NULL,
    metadata_json    TEXT          NULL,
    created_at       TIMESTAMP     NOT NULL
);
CREATE INDEX idx_analytics_event_date ON analytics_events (event_date);
CREATE INDEX idx_analytics_event_type ON analytics_events (event_type);
CREATE INDEX idx_analytics_visitor    ON analytics_events (visitor_token, event_type, event_date);
CREATE INDEX idx_analytics_project    ON analytics_events (project_id, event_type);

CREATE TABLE analytics_daily (
    id                  SERIAL    PRIMARY KEY,
    event_date          DATE      NOT NULL UNIQUE,
    page_views          INTEGER   NOT NULL DEFAULT 0,
    unique_visitors     INTEGER   NOT NULL DEFAULT 0,
    project_views       INTEGER   NOT NULL DEFAULT 0,
    external_clicks     INTEGER   NOT NULL DEFAULT 0,
    contact_submissions INTEGER   NOT NULL DEFAULT 0,
    mobile_visits       INTEGER   NOT NULL DEFAULT 0,
    tablet_visits       INTEGER   NOT NULL DEFAULT 0,
    desktop_visits      INTEGER   NOT NULL DEFAULT 0,
    dark_theme_hits     INTEGER   NOT NULL DEFAULT 0,
    light_theme_hits    INTEGER   NOT NULL DEFAULT 0,
    updated_at          TIMESTAMP NOT NULL
);

CREATE TABLE rate_limits (
    id                SERIAL        PRIMARY KEY,
    scope_key         VARCHAR(80)   NOT NULL,
    identifier        VARCHAR(255)  NOT NULL,
    hits              INTEGER       NOT NULL DEFAULT 0,
    window_started_at TIMESTAMP     NOT NULL,
    blocked_until     TIMESTAMP     NULL,
    updated_at        TIMESTAMP     NOT NULL,
    UNIQUE (scope_key, identifier)
);
CREATE INDEX idx_rate_limits_blocked ON rate_limits (scope_key, blocked_until);
