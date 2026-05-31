<?php

declare(strict_types=1);

$record = $edit_record ?? null;
?>
<section class="admin-grid two-thirds">
    <div class="admin-card">
        <div class="admin-card-head">
            <h2>Skills manager</h2>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Title</th><th>Group</th><th>Level</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($records as $item): ?>
                        <tr>
                            <td><?= cv_e(cv_localized_value($item, 'title')) ?></td>
                            <td><?= cv_e(cv_localized_value($item, 'group')) ?></td>
                            <td><?= cv_e((string) ($item['skill_level'] ?? '')) ?></td>
                            <td><span class="status-pill status-<?= cv_e((string) $item['status']) ?>"><?= cv_e((string) $item['status']) ?></span></td>
                            <td class="admin-table-actions">
                                <a href="<?= cv_admin_url('skills/edit/' . $item['id']) ?>">Edit</a>
                                <form method="post" action="<?= cv_admin_url('skills/delete/' . $item['id']) ?>">
                                    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <form class="admin-card admin-form" method="post" action="<?= cv_admin_url('skills/' . ($record ? 'edit/' . $record['id'] : 'create')) ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <div class="admin-card-head"><h2><?= $record ? 'Edit skill' : 'New skill' ?></h2></div>
        <div class="admin-form-grid">
            <label><span>Group RU</span><input type="text" name="group_ru" value="<?= cv_e((string) ($record['group_ru'] ?? '')) ?>"></label>
            <label><span>Group EN</span><input type="text" name="group_en" value="<?= cv_e((string) ($record['group_en'] ?? '')) ?>"></label>
            <label><span>Title RU</span><input type="text" name="title_ru" value="<?= cv_e((string) ($record['title_ru'] ?? '')) ?>"></label>
            <label><span>Title EN</span><input type="text" name="title_en" value="<?= cv_e((string) ($record['title_en'] ?? '')) ?>"></label>
            <label class="full"><span>Description RU</span><textarea name="description_ru" rows="4"><?= cv_e((string) ($record['description_ru'] ?? '')) ?></textarea></label>
            <label class="full"><span>Description EN</span><textarea name="description_en" rows="4"><?= cv_e((string) ($record['description_en'] ?? '')) ?></textarea></label>
            <label><span>Level</span><input type="text" name="skill_level" value="<?= cv_e((string) ($record['skill_level'] ?? '')) ?>"></label>
            <label><span>Icon code</span><input type="text" name="icon" value="<?= cv_e((string) ($record['icon'] ?? '')) ?>"></label>
            <label><span>Sort order</span><input type="number" name="sort_order" value="<?= cv_e((string) ($record['sort_order'] ?? 0)) ?>"></label>
            <label><span>Status</span><select name="status"><option value="published" <?= ($record['status'] ?? '') === 'published' ? 'selected' : '' ?>>published</option><option value="draft" <?= ($record['status'] ?? '') === 'draft' ? 'selected' : '' ?>>draft</option></select></label>
        </div>
        <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
    </form>
</section>
