-- Migration: change image path columns from VARCHAR(255) to TEXT
-- and add media_data table for binary image storage (serverless-safe).
-- Required for base64 data URL storage on Vercel (serverless — no filesystem).
-- Run this once against your Neon database.

ALTER TABLE projects
    ALTER COLUMN cover_image TYPE TEXT,
    ALTER COLUMN og_image    TYPE TEXT;

ALTER TABLE project_images
    ALTER COLUMN file_path   TYPE TEXT,
    ALTER COLUMN thumb_path  TYPE TEXT;

ALTER TABLE media_library
    ALTER COLUMN file_path   TYPE TEXT,
    ALTER COLUMN thumb_path  TYPE TEXT;

ALTER TABLE testimonials
    ALTER COLUMN avatar      TYPE TEXT;

ALTER TABLE blog_posts
    ALTER COLUMN cover_image TYPE TEXT;

ALTER TABLE project_videos
    ALTER COLUMN poster_image TYPE TEXT;

CREATE TABLE IF NOT EXISTS media_data (
    id          INTEGER  NOT NULL PRIMARY KEY REFERENCES media_library (id) ON DELETE CASCADE,
    image_data  TEXT     NOT NULL,
    thumb_data  TEXT     NULL
);
