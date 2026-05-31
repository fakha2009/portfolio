<?php

declare(strict_types=1);

$sectionLabel = $sections[$section]['label'] ?? 'Dashboard';
$newMsgCount  = (int) ($metrics['new_messages'] ?? 0);
?>
<header class="topbar">
    <button class="burger-admin" id="burger" aria-label="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <div class="topbar__title">
        <h1><?= cv_e($sectionLabel) ?></h1>
        <p>Edit content, review leads, media, SEO, and portfolio operations.</p>
    </div>
    <div class="topbar__spacer"></div>
    <form class="search" role="search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4-4"/></svg>
        <input type="search" placeholder="Search this panel" aria-label="Search admin panel" data-admin-search autocomplete="off">
        <button class="admin-search-clear" type="button" data-admin-search-clear hidden aria-label="Clear search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </form>
    <a class="btn btn--ghost btn--sm" href="<?= cv_admin_url('projects/create') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        <?= cv_e(cv_t('actions.new_project')) ?>
    </a>
    <a class="btn btn--primary btn--sm" href="<?= cv_admin_url('messages') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16v12H8l-4 3z"/></svg>
        <?= cv_e(cv_t('admin.messages')) ?>
        <?php if ($newMsgCount > 0): ?>
            <span style="background:rgba(255,255,255,.25);border-radius:999px;padding:1px 6px;font-family:var(--font-mono);font-size:11px"><?= $newMsgCount ?></span>
        <?php endif; ?>
    </a>
</header>
