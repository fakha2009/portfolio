<?php

declare(strict_types=1);

function cv_telegram_enabled(): bool
{
    return cv_boolean(cv_config('telegram.enabled', false));
}

function cv_telegram_ready(): bool
{
    return cv_telegram_enabled()
        && trim((string) cv_config('telegram.bot_token', '')) !== ''
        && trim((string) cv_config('telegram.chat_id', '')) !== '';
}

function cv_telegram_escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function cv_telegram_send_message(string $text, string $parseMode = 'HTML'): array
{
    if (!cv_telegram_enabled()) {
        return ['ok' => false, 'disabled' => true, 'error' => 'Telegram notifications are disabled.'];
    }

    if (!cv_telegram_ready()) {
        cv_log('telegram', 'Telegram is enabled but configuration is incomplete.');

        return ['ok' => false, 'error' => 'Telegram is enabled, but bot token or chat ID is missing.'];
    }

    $endpoint = sprintf(
        'https://api.telegram.org/bot%s/sendMessage',
        rawurlencode((string) cv_config('telegram.bot_token'))
    );

    $payload = [
        'chat_id' => (string) cv_config('telegram.chat_id'),
        'text' => $text,
        'parse_mode' => $parseMode,
        'disable_web_page_preview' => 'true',
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $responseRaw = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseRaw === false || $error !== '') {
            cv_log('telegram', 'Telegram request failed', ['error' => $error !== '' ? $error : 'Unknown cURL error']);

            return ['ok' => false, 'error' => $error !== '' ? $error : 'Telegram request failed.'];
        }
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($payload),
                'timeout' => 15,
            ],
        ]);

        $responseRaw = @file_get_contents($endpoint, false, $context);
        $statusCode = 0;

        foreach (($http_response_header ?? []) as $headerLine) {
            if (preg_match('~HTTP/\S+\s+(\d{3})~', $headerLine, $matches) === 1) {
                $statusCode = (int) $matches[1];
                break;
            }
        }

        if ($responseRaw === false) {
            cv_log('telegram', 'Telegram request failed', ['error' => 'HTTP request failed']);

            return ['ok' => false, 'error' => 'Telegram request failed.'];
        }
    }

    $payloadDecoded = json_decode((string) $responseRaw, true);
    if (!is_array($payloadDecoded)) {
        cv_log('telegram', 'Telegram returned invalid JSON', ['response' => $responseRaw]);

        return ['ok' => false, 'error' => 'Telegram returned an invalid response payload.'];
    }

    if (($payloadDecoded['ok'] ?? false) !== true || $statusCode >= 400) {
        $errorMessage = (string) ($payloadDecoded['description'] ?? 'Telegram send failed.');
        cv_log('telegram', 'Telegram returned an error', ['status' => $statusCode, 'response' => $payloadDecoded]);

        return ['ok' => false, 'error' => $errorMessage, 'payload' => $payloadDecoded];
    }

    return ['ok' => true, 'payload' => $payloadDecoded];
}

function cv_send_telegram_notification(string $text): array
{
    return cv_telegram_send_message($text);
}

function cv_send_contact_telegram_notification(array $message, int $messageId = 0): array
{
    $siteName = trim((string) cv_config('app.name', 'Portfolio'));
    $preview = trim((string) ($message['message'] ?? ''));
    $preview = preg_replace('/\s+/u', ' ', $preview) ?? $preview;

    if (mb_strlen($preview, 'UTF-8') > 220) {
        $preview = rtrim(mb_substr($preview, 0, 217, 'UTF-8')) . '...';
    }

    $lines = [
        '<b>New contact request</b>',
        '<b>Site:</b> ' . cv_telegram_escape_html($siteName),
        '<b>Name:</b> ' . cv_telegram_escape_html(trim((string) ($message['name'] ?? 'Unknown'))),
        '<b>Email:</b> ' . cv_telegram_escape_html(trim((string) ($message['email'] ?? ''))),
    ];

    $phone = trim((string) ($message['phone'] ?? ''));
    if ($phone !== '') {
        $lines[] = '<b>Phone:</b> ' . cv_telegram_escape_html($phone);
    }

    $company = trim((string) ($message['company'] ?? ''));
    if ($company !== '') {
        $lines[] = '<b>Company:</b> ' . cv_telegram_escape_html($company);
    }

    $subject = trim((string) ($message['subject'] ?? ''));
    if ($subject !== '') {
        $lines[] = '<b>Subject:</b> ' . cv_telegram_escape_html($subject);
    }

    $budget = trim((string) ($message['budget'] ?? ''));
    if ($budget !== '') {
        $lines[] = '<b>Budget:</b> ' . cv_telegram_escape_html($budget);
    }

    $lines[] = '<b>Preview:</b> ' . cv_telegram_escape_html($preview !== '' ? $preview : 'No message text provided.');
    $lines[] = '<b>Time:</b> ' . cv_telegram_escape_html(date('Y-m-d H:i'));

    if ($messageId > 0) {
        $lines[] = '<b>Admin:</b> Check the inbox for message #' . $messageId . '.';
    } else {
        $lines[] = '<b>Admin:</b> Check the inbox for the full message.';
    }

    return cv_telegram_send_message(implode("\n", $lines));
}
