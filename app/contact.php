<?php

declare(strict_types=1);

function cv_contact_form_errors(array $input): array
{
    $errors = [];

    if (mb_strlen(trim((string) ($input['name'] ?? ''))) < 2) {
        $errors['name'] = cv_t('messages.contact_name_required');
    }

    if (!filter_var((string) ($input['email'] ?? ''), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = cv_t('messages.contact_email_invalid');
    }

    if (mb_strlen(trim((string) ($input['message'] ?? ''))) < 10) {
        $errors['message'] = cv_t('messages.contact_message_short');
    }

    return $errors;
}

function cv_handle_contact_submit(): void
{
    if (!cv_is_post()) {
        cv_abort(405, 'Method not allowed');
    }

    cv_require_csrf('contact');

    $redirectUrl = (string) cv_post('_redirect', cv_url(''));
    $input = [
        'name' => trim((string) cv_post('name', '')),
        'email' => trim((string) cv_post('email', '')),
        'phone' => trim((string) cv_post('phone', '')),
        'company' => trim((string) cv_post('company', '')),
        'budget' => trim((string) cv_post('budget', '')),
        'message' => trim((string) cv_post('message', '')),
    ];

    cv_flash_old_input($input);

    if (!cv_honeypot_passed('website')) {
        cv_flash('success', cv_t('messages.message_sent'));
        cv_redirect($redirectUrl . '#contact');
    }

    $limit = cv_rate_limit_check(
        'contact_form',
        cv_ip_hash(),
        (int) cv_config('security.contact_max_attempts', 4),
        (int) cv_config('security.contact_window_seconds', 1800)
    );

    $isAjax = cv_is_ajax_request();
    if ($limit['limited']) {
        if ($isAjax) {
            cv_json_response([
                'ok' => false,
                'error' => cv_t('messages.rate_limited'),
            ], 429);
        }
        cv_flash('error', cv_t('messages.rate_limited'));
        cv_redirect($redirectUrl . '#contact');
    }

    $errors = cv_contact_form_errors($input);

    if ($errors !== []) {
        if ($isAjax) {
            cv_json_response([
                'ok' => false,
                'errors' => $errors,
            ], 422);
        }
        foreach ($errors as $message) {
            cv_flash('error', $message);
        }

        cv_redirect($redirectUrl . '#contact');
    }

    cv_rate_limit_hit(
        'contact_form',
        cv_ip_hash(),
        (int) cv_config('security.contact_max_attempts', 4),
        (int) cv_config('security.contact_window_seconds', 1800)
    );

    $messageData = [
        'locale_code' => cv_current_locale(),
        'name' => $input['name'],
        'email' => $input['email'],
        'phone' => $input['phone'],
        'company' => $input['company'],
        'budget' => $input['budget'],
        'message' => $input['message'],
        'page_url' => $redirectUrl,
        'ip_hash' => cv_ip_hash(),
        'user_agent' => cv_user_agent(),
        'referrer' => substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 255),
        'status' => 'new',
        'smtp_sent' => 0,
        'telegram_sent' => 0,
        'created_at' => cv_now(),
        'updated_at' => cv_now(),
    ];

    cv_execute(
        'INSERT INTO contact_messages
         (locale_code, name, email, phone, company, budget, message, page_url, ip_hash, user_agent, referrer, status, smtp_sent, telegram_sent, created_at, updated_at)
         VALUES
         (:locale_code, :name, :email, :phone, :company, :budget, :message, :page_url, :ip_hash, :user_agent, :referrer, :status, :smtp_sent, :telegram_sent, :created_at, :updated_at)',
        $messageData
    );

    $messageId = cv_last_insert_id();

    $smtp = cv_send_contact_email($input);
    if (($smtp['ok'] ?? false) === true) {
        cv_execute('UPDATE contact_messages SET smtp_sent = 1, updated_at = :updated_at WHERE id = :id', [
            'updated_at' => cv_now(),
            'id' => $messageId,
        ]);
    }

    $telegram = cv_send_contact_telegram_notification($input, $messageId);

    if (($telegram['ok'] ?? false) === true) {
        cv_execute('UPDATE contact_messages SET telegram_sent = 1, updated_at = :updated_at WHERE id = :id', [
            'updated_at' => cv_now(),
            'id' => $messageId,
        ]);
    } elseif (!($telegram['disabled'] ?? false)) {
        cv_log('telegram', 'Telegram notification was not delivered for a contact submission', [
            'message_id' => $messageId,
            'error' => (string) ($telegram['error'] ?? 'Unknown Telegram error'),
        ]);
    }

    cv_track_contact_submit($redirectUrl);
    cv_clear_old_input();
    $successMessage = cv_t('messages.message_sent');
    if (!($smtp['ok'] ?? false) && !($smtp['disabled'] ?? false)) {
        $successMessage = cv_t('messages.message_saved_mail_failed');
    }
    if ($isAjax) {
        cv_json_response([
            'ok' => true,
            'message' => $successMessage,
            'id' => $messageId,
        ]);
    }
    cv_flash('success', $successMessage);
    cv_redirect($redirectUrl . '#contact');
}
