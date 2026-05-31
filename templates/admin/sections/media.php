<?php

declare(strict_types=1);
?>
<section class="admin-stack">
    <form class="admin-card admin-form" method="post" enctype="multipart/form-data" action="<?= cv_admin_url('media') ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <div class="admin-card-head">
            <div>
                <h2>Media library</h2>
                <p>Upload local images for project covers, galleries, testimonial avatars, and video posters.</p>
            </div>
            <span class="status-pill"><?= count($media) ?> items</span>
        </div>
        <label class="upload-dropzone">
            <span>Drag files here or choose multiple images</span>
            <small>JPEG, PNG, and WebP are supported. The uploader will optimize images when GD is available and gracefully fall back to original files otherwise.</small>
            <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
        </label>
        <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.upload')) ?></button>
    </form>

    <?php if ($media !== []): ?>
        <div class="media-grid">
            <?php foreach ($media as $item): ?>
                <article class="media-card">
                    <img src="<?= cv_e(cv_upload_url((string) ($item['thumb_path'] ?: $item['file_path']))) ?>" alt="<?= cv_e(cv_localized_value($item, 'alt')) ?>" loading="lazy">
                    <div class="media-card-body">
                        <strong><?= cv_e(basename((string) $item['file_path'])) ?></strong>
                        <span><?= cv_e((string) $item['mime_type']) ?></span>
                        <span><?= cv_e((string) ($item['type'] ?? 'image')) ?> / <?= cv_e((string) ($item['context'] ?? 'general')) ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <strong>No local media uploaded yet.</strong>
            <p>Upload covers, gallery images, or poster assets here. Files are stored locally and tracked in MySQL.</p>
        </div>
    <?php endif; ?>
</section>
