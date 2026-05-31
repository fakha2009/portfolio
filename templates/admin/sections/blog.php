<?php

declare(strict_types=1);

$record = $edit_record ?? cv_admin_blog_defaults();
$postId = (int) ($record['id'] ?? 0);
$isEditing = $postId > 0;
$coverUrl = (string) ($record['cover_image'] ?? '');
$publishedValue = '';

if (!empty($record['published_at'])) {
    $publishedTime = strtotime((string) $record['published_at']);
    $publishedValue = $publishedTime ? date('Y-m-d\TH:i', $publishedTime) : '';
}

$blogImageUrl = static function (string $path): string {
    if ($path === '') {
        return '';
    }

    if (
        str_starts_with($path, 'data:')
        || str_starts_with($path, 'http://')
        || str_starts_with($path, 'https://')
        || str_starts_with($path, '/')
    ) {
        return $path;
    }

    return cv_upload_url($path);
};

$previewUrl = $blogImageUrl($coverUrl);
?>
<div class="blog-admin-layout">
    <div class="admin-card blog-admin-list">
        <div class="admin-card-head">
            <div>
                <h2>Blog posts</h2>
                <p>Covers are stored in the database and served through the image route.</p>
            </div>
            <a class="admin-button admin-button-secondary" href="<?= cv_admin_url('blog/create') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New post
            </a>
        </div>

        <?php if ($records !== []): ?>
            <div class="admin-table-wrap">
                <table class="admin-table blog-admin-table">
                    <thead>
                        <tr>
                            <th style="width:76px">Cover</th>
                            <th>Post</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th style="width:150px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $item): ?>
                            <?php
                            $itemId = (int) ($item['id'] ?? 0);
                            $thumb = $blogImageUrl((string) ($item['cover_image'] ?? ''));
                            $title = cv_localized_value($item, 'title') ?: (string) ($item['slug'] ?? 'Untitled post');
                            $excerpt = cv_localized_value($item, 'excerpt');
                            ?>
                            <tr class="<?= $postId === $itemId ? 'blog-row-active' : '' ?>">
                                <td>
                                    <?php if ($thumb !== ''): ?>
                                        <img class="blog-admin-thumb" src="<?= cv_e($thumb) ?>" alt="">
                                    <?php else: ?>
                                        <div class="blog-admin-thumb blog-admin-thumb-empty" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="blog-admin-title"><?= cv_e($title) ?></strong>
                                    <span class="blog-admin-meta"><?= cv_e((string) ($item['slug'] ?? '')) ?></span>
                                    <?php if ($excerpt !== ''): ?>
                                        <span class="blog-admin-excerpt"><?= cv_e($excerpt) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-pill status-<?= cv_e((string) ($item['status'] ?? 'draft')) ?>"><?= cv_e((string) ($item['status'] ?? 'draft')) ?></span></td>
                                <td><?= (int) ($item['featured'] ?? 0) === 1 ? '<span class="blog-featured">Yes</span>' : '<span class="blog-muted">No</span>' ?></td>
                                <td>
                                    <div class="blog-admin-actions">
                                        <a class="admin-button admin-button-secondary admin-button-small" href="<?= cv_admin_url('blog/edit/' . $itemId) ?>">Edit</a>
                                        <form method="post" action="<?= cv_admin_url('blog/delete/' . $itemId) ?>" data-confirm="Delete this blog post?">
                                            <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                                            <button class="admin-button admin-button-secondary admin-button-small" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <strong>No blog posts yet.</strong>
                <p>Create the first post and add a cover image from the editor.</p>
            </div>
        <?php endif; ?>
    </div>

    <form class="admin-form blog-editor" id="blog-form" method="post" enctype="multipart/form-data"
          action="<?= cv_admin_url('blog/' . ($isEditing ? 'edit/' . $postId : 'create')) ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <input type="hidden" name="existing_cover_image" value="<?= cv_e($coverUrl) ?>">

        <div class="admin-card blog-editor-head">
            <div>
                <h2><?= $isEditing ? 'Editing: ' . cv_e(cv_localized_value($record, 'title') ?: 'Blog post') : 'New blog post' ?></h2>
                <?php if ($isEditing): ?>
                    <p>ID: <?= $postId ?> - slug: <code><?= cv_e((string) ($record['slug'] ?? '')) ?></code></p>
                <?php else: ?>
                    <p>Write bilingual content and attach a clean cover image.</p>
                <?php endif; ?>
            </div>
            <div class="blog-editor-head-actions">
                <?php if ($isEditing && !empty($record['slug'])): ?>
                    <a class="admin-button admin-button-secondary admin-button-small" href="<?= cv_url('blog/' . $record['slug']) ?>" target="_blank" rel="noopener">View on site</a>
                <?php endif; ?>
                <button class="admin-button admin-button-primary" type="submit" id="blog-save-btn">
                    <span id="blog-save-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
                        Save post
                    </span>
                    <span id="blog-save-spinner" hidden>Saving...</span>
                </button>
            </div>
        </div>

        <div class="blog-editor-grid">
            <div class="blog-editor-main">
                <div class="admin-card blog-editor-section">
                    <div class="blog-section-head">
                        <span>01</span>
                        <h3>Basic info</h3>
                    </div>
                    <div class="admin-form-grid">
                        <label><span>Title RU</span><input type="text" name="title_ru" value="<?= cv_e((string) ($record['title_ru'] ?? '')) ?>"></label>
                        <label><span>Title EN</span><input type="text" name="title_en" value="<?= cv_e((string) ($record['title_en'] ?? '')) ?>"></label>
                        <label class="full"><span>Slug</span><input type="text" name="slug" value="<?= cv_e((string) ($record['slug'] ?? '')) ?>" placeholder="post-slug"></label>
                        <label class="full"><span>Excerpt RU</span><textarea name="excerpt_ru" rows="3"><?= cv_e((string) ($record['excerpt_ru'] ?? '')) ?></textarea></label>
                        <label class="full"><span>Excerpt EN</span><textarea name="excerpt_en" rows="3"><?= cv_e((string) ($record['excerpt_en'] ?? '')) ?></textarea></label>
                    </div>
                </div>

                <div class="admin-card blog-editor-section">
                    <div class="blog-section-head">
                        <span>02</span>
                        <h3>Article body</h3>
                    </div>
                    <div class="admin-form-grid">
                        <label class="full"><span>Body RU</span><textarea name="body_ru" rows="8"><?= cv_e((string) ($record['body_ru'] ?? '')) ?></textarea></label>
                        <label class="full"><span>Body EN</span><textarea name="body_en" rows="8"><?= cv_e((string) ($record['body_en'] ?? '')) ?></textarea></label>
                    </div>
                </div>
            </div>

            <div class="blog-editor-side">
                <div class="admin-card blog-editor-section">
                    <div class="blog-section-head">
                        <span>03</span>
                        <h3>Cover image</h3>
                    </div>
                    <div class="blog-cover-preview" id="blog-cover-box">
                        <?php if ($previewUrl !== ''): ?>
                            <img id="blog-cover-img" src="<?= cv_e($previewUrl) ?>" alt="">
                            <div id="blog-cover-empty" class="blog-cover-empty" hidden>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                <span>No cover yet</span>
                            </div>
                        <?php else: ?>
                            <img id="blog-cover-img" src="" alt="" hidden>
                            <div id="blog-cover-empty" class="blog-cover-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                <span>No cover yet</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <label class="blog-upload-btn" for="blog_cover_input">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></svg>
                        <?= $previewUrl !== '' ? 'Replace cover' : 'Upload cover' ?>
                    </label>
                    <input type="file" id="blog_cover_input" name="cover_image_file" accept="image/jpeg,image/png,image/webp">
                    <p class="blog-editor-hint">JPG, PNG or WebP. Max 5 MB. Stored in database.</p>
                </div>

                <div class="admin-card blog-editor-section">
                    <div class="blog-section-head">
                        <span>04</span>
                        <h3>Publishing</h3>
                    </div>
                    <div class="admin-form-grid">
                        <label class="full"><span>Tags</span><input type="text" name="tags" value="<?= cv_e((string) ($record['tags'] ?? '')) ?>" placeholder="backend, api, notes"></label>
                        <label class="full"><span>Published at</span><input type="datetime-local" name="published_at" value="<?= cv_e($publishedValue) ?>"></label>
                        <label><span>Status</span><select name="status"><option value="published" <?= ($record['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option><option value="draft" <?= ($record['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option></select></label>
                        <label><span>Featured</span><select name="featured"><option value="0" <?= (int) ($record['featured'] ?? 0) === 0 ? 'selected' : '' ?>>No</option><option value="1" <?= (int) ($record['featured'] ?? 0) === 1 ? 'selected' : '' ?>>Yes</option></select></label>
                        <label class="full"><span>Sort order</span><input type="number" name="sort_order" value="<?= cv_e((string) ($record['sort_order'] ?? 0)) ?>"></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="blog-save-bar">
            <?php if ($isEditing): ?>
                <a href="<?= cv_admin_url('blog/create') ?>" class="admin-button admin-button-secondary admin-button-small">New post</a>
            <?php endif; ?>
            <button class="admin-button admin-button-primary" type="submit" id="blog-save-btn-bottom">Save post</button>
        </div>
    </form>
</div>

<script>
(function(){
    var input = document.getElementById('blog_cover_input');
    var img = document.getElementById('blog-cover-img');
    var empty = document.getElementById('blog-cover-empty');
    var form = document.getElementById('blog-form');

    if (input && img) {
        input.addEventListener('change', function(){
            var file = this.files && this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(event){
                img.src = event.target.result;
                img.hidden = false;
                if (empty) empty.hidden = true;
            };
            reader.readAsDataURL(file);
        });
    }

    if (form) {
        form.addEventListener('submit', function(){
            ['blog-save-btn', 'blog-save-btn-bottom'].forEach(function(id){
                var button = document.getElementById(id);
                if (button) button.disabled = true;
            });

            var label = document.getElementById('blog-save-label');
            var spinner = document.getElementById('blog-save-spinner');
            if (label) label.hidden = true;
            if (spinner) spinner.hidden = false;
        });
    }
})();
</script>
