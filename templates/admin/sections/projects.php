<?php

declare(strict_types=1);

$projectRecord = $edit_record ?? cv_admin_project_defaults();
$projectId     = (int) ($projectRecord['id'] ?? 0);
$coverUrl      = (string) ($projectRecord['cover_image'] ?? '');
$gallery       = $edit_gallery ?? [];
$video         = $edit_video ?? null;

function proj_img_url(string $path): string {
    if ($path === '') return '';
    if (str_starts_with($path, 'data:') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    return cv_upload_url($path);
}
?>

<!-- Projects list -->
<div class="admin-card" style="margin-bottom:24px">
    <div class="admin-card-head">
        <div>
            <h2>Projects</h2>
            <p>All images are stored in the Neon database — no external storage needed.</p>
        </div>
        <a class="admin-button admin-button-secondary" href="<?= cv_admin_url('projects/create') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M12 5v14M5 12h14"/></svg>
            New project
        </a>
    </div>
    <?php if ($records !== []): ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px">Cover</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th style="width:130px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $item): ?>
                        <?php $thumb = proj_img_url((string) ($item['cover_image'] ?? '')); ?>
                        <tr class="<?= $projectId === (int) $item['id'] ? 'project-row-active' : '' ?>">
                            <td style="padding:8px 12px">
                                <?php if ($thumb): ?>
                                    <img src="<?= cv_e($thumb) ?>"
                                         style="width:48px;height:36px;object-fit:cover;border-radius:6px;display:block" alt="">
                                <?php else: ?>
                                    <div style="width:48px;height:36px;background:var(--line-2);border-radius:6px;display:flex;align-items:center;justify-content:center">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:16px;height:16px;color:var(--muted)"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= cv_e(cv_localized_value($item, 'title')) ?></strong>
                                <?php if (!empty($item['technologies'])): ?>
                                    <span style="display:block;font-size:12px;color:var(--muted);margin-top:2px"><?= cv_e((string) $item['technologies']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="status-pill status-<?= cv_e((string) $item['status']) ?>"><?= cv_e((string) $item['status']) ?></span></td>
                            <td><?= (int) ($item['featured'] ?? 0) === 1 ? '<span style="color:var(--warn)">★ Yes</span>' : '—' ?></td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a class="admin-button admin-button-secondary admin-button-small" href="<?= cv_admin_url('projects/edit/' . $item['id']) ?>">Edit</a>
                                    <form method="post" action="<?= cv_admin_url('projects/delete/' . $item['id']) ?>" data-confirm="Delete this project and all its media?">
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
            <strong>No projects yet.</strong>
            <p>Create the first project to populate the portfolio grid.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Edit / Create form -->
<form method="post" enctype="multipart/form-data" id="project-form" class="admin-form"
      action="<?= cv_admin_url('projects/' . ($projectId > 0 ? 'edit/' . $projectId : 'create')) ?>">
    <input type="hidden" name="_token"               value="<?= cv_e(cv_csrf_token('admin')) ?>">
    <input type="hidden" name="existing_cover_image" value="<?= cv_e($coverUrl) ?>">
    <input type="hidden" name="existing_og_image"    value="<?= cv_e((string) ($projectRecord['og_image'] ?? '')) ?>">
    <input type="hidden" name="existing_video_poster" value="<?= cv_e((string) ($video['poster_image'] ?? '')) ?>">

    <!-- Header -->
    <div class="admin-card" style="margin-bottom:18px">
        <div class="admin-card-head" style="border-bottom:0;padding-bottom:20px">
            <div>
                <h2 style="font-size:20px"><?= $projectId > 0 ? 'Editing: ' . cv_e(cv_localized_value($projectRecord, 'title') ?: 'Project') : 'New project' ?></h2>
                <?php if ($projectId > 0): ?>
                    <p>ID: <?= $projectId ?> · slug: <code style="font-family:var(--font-mono);font-size:12px"><?= cv_e((string) ($projectRecord['slug'] ?? '')) ?></code></p>
                <?php endif; ?>
            </div>
            <div style="display:flex;gap:10px;align-items:center">
                <?php if ($projectId > 0): ?>
                    <a class="admin-button admin-button-secondary admin-button-small"
                       href="<?= cv_url('projects/' . ($projectRecord['slug'] ?? '')) ?>"
                       target="_blank" rel="noopener">View on site</a>
                <?php endif; ?>
                <button class="admin-button admin-button-primary" type="submit" id="proj-save-btn">
                    <span id="proj-save-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save project
                    </span>
                    <span id="proj-save-spinner" style="display:none;align-items:center;gap:8px">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;animation:proj-spin .8s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        Saving…
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="proj-edit-grid">

        <!-- LEFT COLUMN: Main info -->
        <div class="proj-col-left">

            <!-- Basic info -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">01</span>
                    <h3>Basic info</h3>
                </div>
                <div class="admin-form-grid">
                    <label><span>Title RU</span><input type="text" name="title_ru" value="<?= cv_e((string) ($projectRecord['title_ru'] ?? '')) ?>" placeholder="Название проекта"></label>
                    <label><span>Title EN</span><input type="text" name="title_en" value="<?= cv_e((string) ($projectRecord['title_en'] ?? '')) ?>" placeholder="Project name"></label>
                    <label><span>Slug (URL)</span><input type="text" name="slug" value="<?= cv_e((string) ($projectRecord['slug'] ?? '')) ?>" placeholder="project-slug"></label>
                    <label><span>Category</span>
                        <select name="category_id">
                            <option value="">— No category —</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (int) ($projectRecord['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= cv_e(cv_localized_value($category, 'name')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label><span>Technologies</span><input type="text" name="technologies" value="<?= cv_e((string) ($projectRecord['technologies'] ?? '')) ?>" placeholder="Go, PostgreSQL, Docker"></label>
                    <label><span>External URL</span><input type="url" name="external_url" value="<?= cv_e((string) ($projectRecord['external_url'] ?? '')) ?>" placeholder="https://"></label>
                    <label><span>Sort order</span><input type="number" name="sort_order" value="<?= cv_e((string) ($projectRecord['sort_order'] ?? 0)) ?>"></label>
                    <label><span>Role RU</span><input type="text" name="role_ru" value="<?= cv_e((string) ($projectRecord['role_ru'] ?? '')) ?>"></label>
                    <label class="full"><span>Role EN</span><input type="text" name="role_en" value="<?= cv_e((string) ($projectRecord['role_en'] ?? '')) ?>"></label>
                </div>
                <div style="display:flex;gap:14px;margin-top:4px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;flex:1">
                        <span style="font-family:var(--font-mono);font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)">Status</span>
                        <select name="status" style="flex:1">
                            <option value="published" <?= ($projectRecord['status'] ?? '') === 'published' ? 'selected' : '' ?>>✓ Published</option>
                            <option value="draft"     <?= ($projectRecord['status'] ?? '') === 'draft'     ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;flex:1">
                        <span style="font-family:var(--font-mono);font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)">Featured</span>
                        <select name="featured" style="flex:1">
                            <option value="0" <?= (int) ($projectRecord['featured'] ?? 0) === 0 ? 'selected' : '' ?>>No</option>
                            <option value="1" <?= (int) ($projectRecord['featured'] ?? 0) === 1 ? 'selected' : '' ?>>★ Yes</option>
                        </select>
                    </label>
                </div>
            </div>

            <!-- Descriptions -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">02</span>
                    <h3>Descriptions</h3>
                </div>
                <div class="admin-form-grid">
                    <label class="full"><span>Short description RU</span><textarea name="short_description_ru" rows="3" placeholder="Краткое описание для карточки проекта"><?= cv_e((string) ($projectRecord['short_description_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Short description EN</span><textarea name="short_description_en" rows="3" placeholder="Short description for the project card"><?= cv_e((string) ($projectRecord['short_description_en'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Full description RU</span><textarea name="full_description_ru" rows="5" placeholder="Полное описание для страницы кейса"><?= cv_e((string) ($projectRecord['full_description_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Full description EN</span><textarea name="full_description_en" rows="5" placeholder="Full description for the case study page"><?= cv_e((string) ($projectRecord['full_description_en'] ?? '')) ?></textarea></label>
                </div>
            </div>

            <!-- Case study texts -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">03</span>
                    <h3>Case study</h3>
                </div>
                <div class="admin-form-grid">
                    <label class="full"><span>Client / Context RU</span><textarea name="client_ru" rows="2"><?= cv_e((string) ($projectRecord['client_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Client / Context EN</span><textarea name="client_en" rows="2"><?= cv_e((string) ($projectRecord['client_en'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Problem RU</span><textarea name="problem_ru" rows="3"><?= cv_e((string) ($projectRecord['problem_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Problem EN</span><textarea name="problem_en" rows="3"><?= cv_e((string) ($projectRecord['problem_en'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Process RU</span><textarea name="process_ru" rows="3"><?= cv_e((string) ($projectRecord['process_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Process EN</span><textarea name="process_en" rows="3"><?= cv_e((string) ($projectRecord['process_en'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Solution RU</span><textarea name="solution_ru" rows="3"><?= cv_e((string) ($projectRecord['solution_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Solution EN</span><textarea name="solution_en" rows="3"><?= cv_e((string) ($projectRecord['solution_en'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Result RU</span><textarea name="result_ru" rows="3"><?= cv_e((string) ($projectRecord['result_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>Result EN</span><textarea name="result_en" rows="3"><?= cv_e((string) ($projectRecord['result_en'] ?? '')) ?></textarea></label>
                </div>
            </div>

            <!-- SEO -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">04</span>
                    <h3>SEO</h3>
                </div>
                <div class="admin-form-grid">
                    <label><span>SEO title RU</span><input type="text" name="seo_title_ru" value="<?= cv_e((string) ($projectRecord['seo_title_ru'] ?? '')) ?>"></label>
                    <label><span>SEO title EN</span><input type="text" name="seo_title_en" value="<?= cv_e((string) ($projectRecord['seo_title_en'] ?? '')) ?>"></label>
                    <label class="full"><span>SEO description RU</span><textarea name="seo_description_ru" rows="2"><?= cv_e((string) ($projectRecord['seo_description_ru'] ?? '')) ?></textarea></label>
                    <label class="full"><span>SEO description EN</span><textarea name="seo_description_en" rows="2"><?= cv_e((string) ($projectRecord['seo_description_en'] ?? '')) ?></textarea></label>
                </div>
            </div>

        </div><!-- /proj-col-left -->

        <!-- RIGHT COLUMN: Media -->
        <div class="proj-col-right">

            <!-- Cover image -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">🖼</span>
                    <h3>Cover image</h3>
                </div>

                <!-- Preview -->
                <div class="cover-preview-box" id="cover-preview-box">
                    <?php $dCover = proj_img_url($coverUrl); ?>
                    <?php if ($dCover !== ''): ?>
                        <img id="cover-preview-img" src="<?= cv_e($dCover) ?>"
                             style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block" alt="">
                    <?php else: ?>
                        <div id="cover-preview-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:var(--muted);height:100%">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span style="font-size:13px">No cover yet</span>
                        </div>
                        <img id="cover-preview-img" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:inherit">
                    <?php endif; ?>
                </div>

                <label class="proj-upload-btn" for="cover_image_input">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <?= $dCover !== '' ? 'Replace cover' : 'Upload cover' ?>
                </label>
                <input type="file" id="cover_image_input" name="cover_image"
                       accept="image/jpeg,image/png,image/webp"
                       style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px">
                <p class="proj-hint">JPG / PNG / WebP · max 5 MB · saved in DB</p>

                <div class="admin-form-grid" style="margin-top:12px">
                    <label><span>Alt RU</span><input type="text" name="cover_alt_ru" value="<?= cv_e((string) ($projectRecord['cover_alt_ru'] ?? '')) ?>"></label>
                    <label><span>Alt EN</span><input type="text" name="cover_alt_en" value="<?= cv_e((string) ($projectRecord['cover_alt_en'] ?? '')) ?>"></label>
                </div>
            </div>

            <!-- Gallery -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">🗂</span>
                    <h3>Gallery <span style="font-size:13px;font-weight:400;color:var(--muted)">(<?= count($gallery) ?> images)</span></h3>
                </div>

                <?php if ($gallery !== []): ?>
                    <div class="gallery-grid">
                        <?php foreach ($gallery as $img): ?>
                            <?php $gUrl = proj_img_url((string) ($img['thumb_path'] ?: $img['file_path'])); ?>
                            <div class="gallery-thumb">
                                <?php if ($gUrl !== ''): ?>
                                    <img src="<?= cv_e($gUrl) ?>" alt="" loading="lazy">
                                <?php endif; ?>
                                <form method="post"
                                      action="<?= cv_admin_url('projects/gallery-delete/' . $img['id']) ?>"
                                      data-confirm="Remove this image?"
                                      class="gallery-thumb-del">
                                    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                                    <button type="submit" title="Remove">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- New images preview -->
                <div id="gallery-new-preview" class="gallery-grid" style="margin-top:<?= $gallery !== [] ? '10px' : '0' ?>"></div>

                <label class="proj-upload-btn" for="gallery_images_input" style="margin-top:12px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Add images
                </label>
                <input type="file" id="gallery_images_input" name="gallery_images[]"
                       accept="image/jpeg,image/png,image/webp" multiple
                       style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px">
                <p class="proj-hint">Multiple files allowed</p>

                <div class="admin-form-grid" style="margin-top:12px">
                    <label><span>Alt RU (for new)</span><input type="text" name="gallery_alt_ru"></label>
                    <label><span>Alt EN (for new)</span><input type="text" name="gallery_alt_en"></label>
                </div>
            </div>

            <!-- Video -->
            <div class="admin-card proj-section">
                <div class="proj-section-head">
                    <span class="proj-section-num">▶</span>
                    <h3>Video</h3>
                </div>
                <?php if ($video && !empty($video['secure_url'])): ?>
                    <div style="background:var(--paper-2);border:1px solid var(--line-2);border-radius:10px;padding:12px;margin-bottom:14px">
                        <a href="<?= cv_e((string) $video['secure_url']) ?>" target="_blank" rel="noopener"
                           style="font-size:13px;font-weight:600;color:var(--accent-ink)">▶ Current video (Cloudinary)</a>
                        <?php $pUrl = proj_img_url((string) ($video['poster_image'] ?? '')); ?>
                        <?php if ($pUrl): ?>
                            <img src="<?= cv_e($pUrl) ?>"
                                 style="width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:8px;margin-top:8px;display:block" alt="">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="admin-form-grid">
                    <label class="full"><span>Showcase video (.mp4 / .webm)</span><input type="file" name="showcase_video" accept=".mp4,.mov,.webm,.m4v"></label>
                    <label class="full"><span>Video poster image</span><input type="file" name="video_poster" accept=".jpg,.jpeg,.png,.webp"></label>
                    <label><span>Poster alt RU</span><input type="text" name="video_poster_alt_ru" value="<?= cv_e((string) ($video['poster_alt_ru'] ?? '')) ?>"></label>
                    <label><span>Poster alt EN</span><input type="text" name="video_poster_alt_en" value="<?= cv_e((string) ($video['poster_alt_en'] ?? '')) ?>"></label>
                </div>
                <p class="proj-hint" style="margin-top:8px">Video requires Cloudinary. Images are stored in Neon DB.</p>
            </div>

        </div><!-- /proj-col-right -->
    </div><!-- /proj-edit-grid -->

    <!-- Sticky save bar -->
    <div class="proj-save-bar">
        <?php if ($projectId > 0): ?>
            <a href="<?= cv_url('projects/' . ($projectRecord['slug'] ?? '')) ?>" target="_blank" rel="noopener"
               class="admin-button admin-button-secondary admin-button-small">↗ View on site</a>
            <a href="<?= cv_admin_url('projects/create') ?>"
               class="admin-button admin-button-secondary admin-button-small">+ New project</a>
        <?php endif; ?>
        <div style="margin-left:auto">
            <button class="admin-button admin-button-primary" type="submit" id="proj-save-btn2">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save project
            </button>
        </div>
    </div>
</form>

<style>
/* Project edit layout */
.proj-edit-grid{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:18px;align-items:start}
.proj-col-left,.proj-col-right{display:grid;gap:18px}
.proj-section{padding:0!important}
.proj-section-head{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid var(--line-2)}
.proj-section-head h3{font-size:15px;font-weight:600;margin:0}
.proj-section-num{font-family:var(--font-mono);font-size:11px;color:var(--accent-ink);background:var(--accent-soft);padding:3px 7px;border-radius:6px;font-weight:600}
.proj-section .admin-form-grid{padding:16px 20px 20px}
.proj-section > div:not(.proj-section-head){padding:16px 20px}
/* Cover preview */
.cover-preview-box{margin:16px 20px 0;height:200px;border-radius:12px;border:2px dashed var(--line);overflow:hidden;background:var(--paper-2)}
/* Gallery */
.gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:12px 20px 0}
.gallery-thumb{position:relative;border-radius:8px;overflow:hidden;aspect-ratio:4/3;background:var(--line-2)}
.gallery-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.gallery-thumb-del{position:absolute;top:4px;right:4px}
.gallery-thumb-del button{width:22px;height:22px;border-radius:50%;background:rgba(0,0,0,.65);border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.15s}
.gallery-thumb-del button:hover{background:var(--accent)}
/* Upload button */
.proj-upload-btn{display:inline-flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--line);border-radius:10px;padding:9px 14px;font-size:13px;font-weight:600;color:var(--ink);cursor:pointer;transition:.18s;margin:12px 20px 0}
.proj-upload-btn:hover{border-color:var(--ink)}
.proj-hint{font-size:11.5px;color:var(--muted);margin:6px 20px 0}
/* Save bar */
.proj-save-bar{display:flex;align-items:center;gap:10px;padding:14px 20px;background:rgba(244,241,235,.9);backdrop-filter:blur(12px);border-top:1px solid var(--line);position:sticky;bottom:0;z-index:10;margin-top:18px;border-radius:0 0 var(--radius-lg) var(--radius-lg)}
/* Active row */
.project-row-active td{background:var(--accent-soft)!important}
/* Responsive */
@media(max-width:900px){.proj-edit-grid{grid-template-columns:1fr}}
@keyframes proj-spin{to{transform:rotate(360deg)}}
</style>
<script>
(function(){
    // Cover preview
    document.getElementById('cover_image_input')?.addEventListener('change',function(){
        var f=this.files[0]; if(!f)return;
        var r=new FileReader();
        r.onload=function(e){
            var img=document.getElementById('cover-preview-img');
            var emp=document.getElementById('cover-preview-empty');
            img.src=e.target.result; img.style.display='block';
            if(emp)emp.style.display='none';
        };
        r.readAsDataURL(f);
    });

    // Gallery preview
    document.getElementById('gallery_images_input')?.addEventListener('change',function(){
        var gp=document.getElementById('gallery-new-preview');
        gp.innerHTML='';
        Array.from(this.files).forEach(function(f){
            var r=new FileReader();
            r.onload=function(e){
                var d=document.createElement('div');
                d.className='gallery-thumb';
                var img=document.createElement('img');
                img.src=e.target.result;
                d.appendChild(img);
                gp.appendChild(d);
            };
            r.readAsDataURL(f);
        });
    });

    // Spinner on both save buttons
    var form=document.getElementById('project-form');
    if(form){
        form.addEventListener('submit',function(){
            ['proj-save-btn','proj-save-btn2'].forEach(function(id){
                var btn=document.getElementById(id);
                if(btn)btn.disabled=true;
            });
            var lbl=document.getElementById('proj-save-label');
            var sp=document.getElementById('proj-save-spinner');
            if(lbl)lbl.style.display='none';
            if(sp)sp.style.display='inline-flex';
        });
    }
})();
</script>
