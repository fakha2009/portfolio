<?php

declare(strict_types=1);

$currentPhoto = (string) ($block['photo'] ?? '');
?>
<form class="admin-card admin-form" method="post"
      action="<?= cv_admin_url('about') . '?locale=' . urlencode((string) $locale) ?>"
      enctype="multipart/form-data"
      id="about-form">
    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <div class="admin-card-head">
        <h2>About editor</h2>
        <div class="locale-tabs">
            <a class="<?= $locale === 'ru' ? 'is-active' : '' ?>" href="<?= cv_admin_url('about') ?>?locale=ru">RU</a>
            <a class="<?= $locale === 'en' ? 'is-active' : '' ?>" href="<?= cv_admin_url('about') ?>?locale=en">EN</a>
        </div>
    </div>

    <div class="admin-form-grid">
        <div class="full">
            <label><span>Profile photo</span></label>
            <div class="photo-upload-row">
                <div class="photo-preview" id="photo-preview-wrap">
                    <?php if ($currentPhoto !== ''): ?>
                        <img id="photo-preview-img"
                             src="<?= cv_e($currentPhoto) ?>"
                             alt="Profile photo"
                             style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                    <?php else: ?>
                        <div class="photo-preview__empty" id="photo-preview-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;height:32px;color:var(--muted)"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span>Нет фото</span>
                        </div>
                        <img id="photo-preview-img" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:inherit">
                    <?php endif; ?>
                </div>
                <div class="photo-upload-controls">
                    <label class="admin-button admin-button-secondary" for="about_photo_input" style="cursor:pointer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Выбрать фото
                    </label>
                    <input type="file" id="about_photo_input" name="about_photo"
                           accept="image/jpeg,image/png,image/webp"
                           style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px">
                    <p style="font-size:12px;color:var(--muted);margin:8px 0 0">
                        JPG, PNG, WebP — до 5 МБ<br>
                        <span style="color:var(--ok)">✓ Сохраняется в базе данных Neon</span>
                    </p>
                </div>
            </div>
        </div>

        <label class="full">
            <span>Title</span>
            <input type="text" name="title" value="<?= cv_e((string) ($block['title'] ?? '')) ?>">
        </label>
        <label class="full">
            <span>Body</span>
            <textarea name="body" rows="6"><?= cv_e((string) ($block['body'] ?? '')) ?></textarea>
        </label>
        <label>
            <span>Highlight 1 (Фокус)</span>
            <input type="text" name="highlight_one" value="<?= cv_e((string) ($block['highlight_one'] ?? '')) ?>">
        </label>
        <label>
            <span>Highlight 2 (Стек)</span>
            <input type="text" name="highlight_two" value="<?= cv_e((string) ($block['highlight_two'] ?? '')) ?>">
        </label>
        <label class="full">
            <span>Highlight 3 (Принцип)</span>
            <input type="text" name="highlight_three" value="<?= cv_e((string) ($block['highlight_three'] ?? '')) ?>">
        </label>
    </div>

    <div style="display:flex;align-items:center;gap:14px;padding:0 22px 22px">
        <button class="admin-button admin-button-primary" type="submit" id="about-save-btn">
            <span id="about-save-label"><?= cv_e(cv_t('actions.save')) ?></span>
            <span id="about-save-spinner" style="display:none;align-items:center;gap:8px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;animation:spin .8s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                Сохраняю…
            </span>
        </button>
    </div>
</form>

<style>
.photo-upload-row{display:flex;gap:20px;align-items:flex-start}
.photo-preview{width:160px;height:200px;flex:none;border-radius:var(--radius-lg);border:2px dashed var(--line);overflow:hidden;background:var(--paper-2);display:flex;align-items:center;justify-content:center;position:relative;transition:.2s}
.photo-preview__empty{display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--muted);font-size:12px;text-align:center;padding:8px}
.photo-upload-controls{flex:1;display:flex;flex-direction:column;align-items:flex-start;justify-content:center}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
<script>
(function(){
    var input    = document.getElementById('about_photo_input');
    var preview  = document.getElementById('photo-preview-img');
    var empty    = document.getElementById('photo-preview-empty');
    var form     = document.getElementById('about-form');
    var saveBtn  = document.getElementById('about-save-btn');
    var lbl      = document.getElementById('about-save-label');
    var spin     = document.getElementById('about-save-spinner');

    if (input && preview) {
        input.addEventListener('change', function() {
            var file = this.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (empty) empty.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    if (form && saveBtn) {
        form.addEventListener('submit', function() {
            saveBtn.disabled = true;
            if (lbl)  lbl.style.display  = 'none';
            if (spin) spin.style.display = 'inline-flex';
        });
    }
})();
</script>
