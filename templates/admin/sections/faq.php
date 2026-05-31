<?php

declare(strict_types=1);

$record = $edit_record ?? null;
?>
<section class="admin-grid two-thirds">
    <div class="admin-card">
        <div class="admin-card-head"><h2>FAQ manager</h2></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Question</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($records as $item): ?>
                        <tr>
                            <td><?= cv_e(cv_localized_value($item, 'question')) ?></td>
                            <td><span class="status-pill status-<?= cv_e((string) $item['status']) ?>"><?= cv_e((string) $item['status']) ?></span></td>
                            <td class="admin-table-actions">
                                <a href="<?= cv_admin_url('faq/edit/' . $item['id']) ?>">Edit</a>
                                <form method="post" action="<?= cv_admin_url('faq/delete/' . $item['id']) ?>"><input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>"><button type="submit">Delete</button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <form class="admin-card admin-form" method="post" action="<?= cv_admin_url('faq/' . ($record ? 'edit/' . $record['id'] : 'create')) ?>">
        <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
        <div class="admin-card-head"><h2><?= $record ? 'Edit FAQ item' : 'New FAQ item' ?></h2></div>
        <div class="admin-form-grid">
            <label><span>Question RU</span><input type="text" name="question_ru" value="<?= cv_e((string) ($record['question_ru'] ?? '')) ?>"></label>
            <label><span>Question EN</span><input type="text" name="question_en" value="<?= cv_e((string) ($record['question_en'] ?? '')) ?>"></label>
            <label class="full"><span>Answer RU</span><textarea name="answer_ru" rows="4"><?= cv_e((string) ($record['answer_ru'] ?? '')) ?></textarea></label>
            <label class="full"><span>Answer EN</span><textarea name="answer_en" rows="4"><?= cv_e((string) ($record['answer_en'] ?? '')) ?></textarea></label>
            <label><span>Sort order</span><input type="number" name="sort_order" value="<?= cv_e((string) ($record['sort_order'] ?? 0)) ?>"></label>
            <label><span>Status</span><select name="status"><option value="published" <?= ($record['status'] ?? '') === 'published' ? 'selected' : '' ?>>published</option><option value="draft" <?= ($record['status'] ?? '') === 'draft' ? 'selected' : '' ?>>draft</option></select></label>
        </div>
        <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
    </form>
</section>
