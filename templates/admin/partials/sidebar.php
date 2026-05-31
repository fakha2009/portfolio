<?php

declare(strict_types=1);

$adminName  = trim((string) ($admin_user['full_name'] ?? ''));
$adminEmail = trim((string) ($admin_user['email'] ?? ''));
$initials   = '';
if ($adminName !== '') {
    $parts = explode(' ', $adminName);
    $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));
}
if ($initials === '') $initials = 'FM';

$navGroups = [
    cv_t('admin.nav_analytics') => [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'analytics' => '<path d="M3 3v18h18"/><path d="M7 14l3-4 3 3 5-7"/>',
    ],
    cv_t('admin.nav_content') => [
        'projects'     => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
        'blog'         => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'media'        => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
        'hero'         => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'about'        => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'skills'       => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        'services'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/>',
        'testimonials' => '<path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>',
        'faq'          => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'categories'   => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
    ],
    cv_t('admin.nav_comms') => [
        'messages' => '<path d="M4 5h16v12H8l-4 3z"/>',
    ],
    cv_t('admin.nav_config') => [
        'seo'    => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'social' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>',
        'theme'  => '<circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2V21a2 2 0 1 1-4 0v-.2A1.7 1.7 0 0 0 6 19.4l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0-1.2-2.9H2a2 2 0 1 1 0-4h.2A1.7 1.7 0 0 0 4.6 6l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 9 4.6h.1A1.7 1.7 0 0 0 10 2.9V2a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 2.9 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.1a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.5 1.9z"/>',
    ],
    cv_t('admin.nav_system') => [
        'backup'   => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
        'security' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    ],
];

$newMessageCount = (int) ($metrics['new_messages'] ?? 0);
?>
<aside class="side" id="side">
    <div class="side__brand">
        <span class="side__mark">FM</span>
        <span>
            <b>Hidden Admin</b>
            <span>Control Panel</span>
        </span>
    </div>

    <nav style="flex:1;overflow-y:auto;overflow-x:hidden" data-admin-nav>
        <?php foreach ($navGroups as $groupLabel => $items): ?>
            <span class="side__label"><?= cv_e($groupLabel) ?></span>
            <?php foreach ($items as $key => $iconPaths): ?>
                <?php if (!isset($sections[$key])) continue; ?>
                <a class="nav-item <?= $section === $key ? 'active' : '' ?>"
                   href="<?= cv_admin_url($key) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <?= $iconPaths ?>
                    </svg>
                    <span><?= cv_e($sections[$key]['label']) ?></span>
                    <?php if ($key === 'messages' && $newMessageCount > 0): ?>
                        <span class="badge"><?= $newMessageCount ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>

    <div class="side__foot">
        <div class="side__user">
            <span class="side__avatar"><?= cv_e($initials) ?></span>
            <span>
                <b><?= cv_e($adminName !== '' ? $adminName : 'Owner') ?></b>
                <span><?= cv_e($adminEmail !== '' ? $adminEmail : 'admin') ?></span>
            </span>
        </div>
        <div class="side__actions">
            <a href="<?= cv_url('') ?>" target="_blank" rel="noopener noreferrer" title="View site">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3h7v7"/><path d="M21 3l-9 9"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
                <?= cv_e(cv_t('actions.view_site')) ?>
            </a>
            <a href="<?= cv_admin_url('logout') ?>" title="Log out">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                <?= cv_e(cv_t('actions.logout')) ?>
            </a>
        </div>
    </div>
</aside>
