<?php

declare(strict_types=1);

$messageFilter = $message_filter ?? '';
$messageCounts = $message_counts ?? ['all' => count($records), 'new' => 0, 'replied' => 0, 'archived' => 0];
?>
<section class="admin-grid two-thirds">
    <div class="admin-card inbox-list">
        <div class="admin-card-head">
            <div>
                <h2>Contact inbox</h2>
                <p>All leads are stored in MySQL first. Email and Telegram remain optional notification layers.</p>
            </div>
        </div>

        <div class="filter-chip-row">
            <a class="filter-chip <?= $messageFilter === '' ? 'is-active' : '' ?>" href="<?= cv_admin_url('messages') ?>">All <span><?= (int) ($messageCounts['all'] ?? 0) ?></span></a>
            <a class="filter-chip <?= $messageFilter === 'new' ? 'is-active' : '' ?>" href="<?= cv_admin_url('messages') ?>?status=new">New <span><?= (int) ($messageCounts['new'] ?? 0) ?></span></a>
            <a class="filter-chip <?= $messageFilter === 'replied' ? 'is-active' : '' ?>" href="<?= cv_admin_url('messages') ?>?status=replied">Replied <span><?= (int) ($messageCounts['replied'] ?? 0) ?></span></a>
            <a class="filter-chip <?= $messageFilter === 'archived' ? 'is-active' : '' ?>" href="<?= cv_admin_url('messages') ?>?status=archived">Archived <span><?= (int) ($messageCounts['archived'] ?? 0) ?></span></a>
        </div>

        <?php if ($records !== []): ?>
            <div class="mini-list">
                <?php foreach ($records as $message): ?>
                    <a class="mini-list-item <?= ($active_record['id'] ?? 0) === (int) $message['id'] ? 'is-active' : '' ?>" href="<?= cv_admin_url('messages/view/' . $message['id']) . ($messageFilter !== '' ? '?status=' . rawurlencode($messageFilter) : '') ?>">
                        <div class="mini-list-item-body">
                            <strong><?= cv_e((string) $message['name']) ?></strong>
                            <span><?= cv_e((string) $message['email']) ?></span>
                            <span><?= cv_e((string) ($message['company'] ?: 'Independent contact')) ?></span>
                        </div>
                        <div class="mini-list-item-meta">
                            <span><?= cv_e((string) date('Y-m-d H:i', strtotime((string) $message['created_at']))) ?></span>
                            <span class="status-pill status-<?= cv_e((string) $message['status']) ?>"><?= cv_e((string) $message['status']) ?></span>
                            <?php if ((int) ($message['is_read'] ?? 0) === 0): ?>
                                <span class="status-pill status-new">Unread</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <strong>No messages in this view yet.</strong>
                <p>New contact submissions will appear here automatically and stay saved even if SMTP or Telegram is unavailable.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="admin-card">
        <?php if ($active_record): ?>
            <div class="admin-card-head">
                <div>
                    <h2><?= cv_e((string) $active_record['name']) ?></h2>
                    <p><?= cv_e((string) $active_record['email']) ?></p>
                </div>
                <span class="status-pill status-<?= cv_e((string) ($active_record['status'] ?? 'new')) ?>"><?= cv_e((string) ($active_record['status'] ?? 'new')) ?></span>
            </div>
            <div class="detail-stack">
                <div class="detail-grid">
                    <div><strong>Email</strong><span><a href="mailto:<?= cv_e((string) ($active_record['email'] ?? '')) ?>"><?= cv_e((string) ($active_record['email'] ?? '')) ?></a></span></div>
                    <div><strong>Phone</strong><span><?= cv_e((string) (($active_record['phone'] ?? '') !== '' ? $active_record['phone'] : 'Not provided')) ?></span></div>
                    <div><strong>Company</strong><span><?= cv_e((string) (($active_record['company'] ?? '') !== '' ? $active_record['company'] : 'Not provided')) ?></span></div>
                    <div><strong>Budget</strong><span><?= cv_e((string) (($active_record['budget'] ?? '') !== '' ? $active_record['budget'] : 'Not specified')) ?></span></div>
                    <div><strong>Received</strong><span><?= cv_e((string) date('Y-m-d H:i', strtotime((string) ($active_record['created_at'] ?? cv_now())))) ?></span></div>
                    <div><strong>Delivery</strong><span>SMTP: <?= (int) ($active_record['smtp_sent'] ?? 0) === 1 ? 'sent' : 'pending' ?> / Telegram: <?= (int) ($active_record['telegram_sent'] ?? 0) === 1 ? 'sent' : 'optional' ?></span></div>
                    <?php if (!empty($active_record['page_url'])): ?>
                        <div class="full"><strong>Source page</strong><span><?= cv_e((string) $active_record['page_url']) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($active_record['referrer'])): ?>
                        <div class="full"><strong>Referrer</strong><span><?= cv_e((string) $active_record['referrer']) ?></span></div>
                    <?php endif; ?>
                </div>
                <div class="message-body-card">
                    <strong>Message</strong>
                    <div><?= cv_text_html((string) ($active_record['message'] ?? '')) ?></div>
                </div>
            </div>
            <div class="inline-actions">
                <form method="post" action="<?= cv_admin_url('messages/view/' . $active_record['id']) . ($messageFilter !== '' ? '?status=' . rawurlencode($messageFilter) : '') ?>">
                    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                    <input type="hidden" name="status" value="replied">
                    <input type="hidden" name="is_read" value="1">
                    <button class="admin-button admin-button-secondary admin-button-small" type="submit">Mark replied</button>
                </form>
                <form method="post" action="<?= cv_admin_url('messages/view/' . $active_record['id']) . ($messageFilter !== '' ? '?status=' . rawurlencode($messageFilter) : '') ?>">
                    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                    <input type="hidden" name="status" value="archived">
                    <input type="hidden" name="is_read" value="1">
                    <button class="admin-button admin-button-secondary admin-button-small" type="submit">Archive</button>
                </form>
                <form method="post" action="<?= cv_admin_url('messages/view/' . $active_record['id']) . ($messageFilter !== '' ? '?status=' . rawurlencode($messageFilter) : '') ?>">
                    <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                    <input type="hidden" name="status" value="<?= cv_e((string) ($active_record['status'] ?? 'new')) ?>">
                    <input type="hidden" name="is_read" value="0">
                    <button class="admin-button admin-button-secondary admin-button-small" type="submit">Mark unread</button>
                </form>
            </div>
            <form class="admin-form compact-form" method="post" action="<?= cv_admin_url('messages/view/' . $active_record['id']) . ($messageFilter !== '' ? '?status=' . rawurlencode($messageFilter) : '') ?>">
                <input type="hidden" name="_token" value="<?= cv_e(cv_csrf_token('admin')) ?>">
                <div class="admin-form-grid">
                    <label><span>Status</span><select name="status"><option value="new" <?= ($active_record['status'] ?? '') === 'new' ? 'selected' : '' ?>>new</option><option value="replied" <?= ($active_record['status'] ?? '') === 'replied' ? 'selected' : '' ?>>replied</option><option value="archived" <?= ($active_record['status'] ?? '') === 'archived' ? 'selected' : '' ?>>archived</option></select></label>
                    <label><span>Read</span><select name="is_read"><option value="1" <?= (int) ($active_record['is_read'] ?? 0) === 1 ? 'selected' : '' ?>>Yes</option><option value="0" <?= (int) ($active_record['is_read'] ?? 0) === 0 ? 'selected' : '' ?>>No</option></select></label>
                </div>
                <button class="admin-button admin-button-primary" type="submit"><?= cv_e(cv_t('actions.save')) ?></button>
            </form>
        <?php else: ?>
            <div class="empty-state">
                <strong>No message selected.</strong>
                <p>Pick a contact request from the inbox to review details and update its status.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
