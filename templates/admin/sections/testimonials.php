<?php

declare(strict_types=1);

$record = $edit_record ?? null;
?>
<section class="admin-grid two-thirds">
    <div class="admin-card">
        <div class="admin-card-head"><h2>Testimonials manager</h2></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Company</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($records as $item): ?>
                        <tr>
                            <td><?= cv_e((string) $item['name']) ?></td>
                            <td><?= cv_e((string) ($item['company'] ?? '')) ?></td>
                            <td><span class="status-pill status-<?= cv_e((string) $item['status']) ?>"><?= cv_e((string) $item['status']) ?></span></td>
                            <td class="admin-table-actions">
                                <a href="<?= cv_admin_url('testimonials/edit/' . $item['id']) ?>">Edit</a>
                                <form method="post" action="<?= cv_admin_url('testimonials/delete/' . $item['id']) ?>"><input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>"><button type="submit">Delete</button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <form class="admin-card admin-form" method="post" enctype="multipart/form-data" action="<?= cv_admin_url('testimonials/' . ($record ? 'edit/' . $record['id'] : 'create')) ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <input type="hidden" name="existing_avatar" value="<?= cv_e((string) ($record['avatar'] ?? '')) ?>">
        <div class="admin-card-head"><h2><?= $record ? 'Edit testimonial' : 'New testimonial' ?></h2></div>
        <div class="admin-form-grid">
            <label><span>Name</span><input type="text" name="name" value="<?= cv_e((string) ($record['name'] ?? '')) ?>"></label>
            <label><span>Company</span><input type="text" name="company" value="<?= cv_e((string) ($record['company'] ?? '')) ?>"></label>
            <label><span>Role RU</span><input type="text" name="role_ru" value="<?= cv_e((string) ($record['role_ru'] ?? '')) ?>"></label>
            <label><span>Role EN</span><input type="text" name="role_en" value="<?= cv_e((string) ($record['role_en'] ?? '')) ?>"></label>
            <label class="full"><span>Quote RU</span><textarea name="quote_ru" rows="4"><?= cv_e((string) ($record['quote_ru'] ?? '')) ?></textarea></label>
            <label class="full"><span>Quote EN</span><textarea name="quote_en" rows="4"><?= cv_e((string) ($record['quote_en'] ?? '')) ?></textarea></label>
            <label><span>Avatar image</span><input type="file" name="avatar_image" accept=".jpg,.jpeg,.png,.webp"></label>
            <label><span>Rating</span><input type="number" name="rating" min="1" max="5" value="<?= cv_e((string) ($record['rating'] ?? 5)) ?>"></label>
            <label><span>Sort order</span><input type="number" name="sort_order" value="<?= cv_e((string) ($record['sort_order'] ?? 0)) ?>"></label>
            <label><span>Status</span><select name="status"><option value="published" <?= ($record['status'] ?? '') === 'published' ? 'selected' : '' ?>>published</option><option value="draft" <?= ($record['status'] ?? '') === 'draft' ? 'selected' : '' ?>>draft</option></select></label>
        </div>
        <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
    </form>
</section>
