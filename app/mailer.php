<?php

declare(strict_types=1);

$phpMailerFiles = [
    cv_root('vendor/PHPMailer/src/Exception.php'),
    cv_root('vendor/PHPMailer/src/PHPMailer.php'),
    cv_root('vendor/PHPMailer/src/SMTP.php'),
];

foreach ($phpMailerFiles as $phpMailerFile) {
    if (is_file($phpMailerFile)) {
        require_once $phpMailerFile;
    }
}

function cv_mailer_available(): bool
{
    return class_exists(\PHPMailer\PHPMailer\PHPMailer::class);
}

function cv_send_contact_email(array $message): array
{
    if (!cv_boolean(cv_config('smtp.enabled', true))) {
        return ['ok' => false, 'disabled' => true, 'error' => 'SMTP is disabled.'];
    }

    if (!cv_mailer_available()) {
        return ['ok' => false, 'error' => 'PHPMailer is missing.'];
    }

    try {
        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = (string) cv_config('smtp.host');
        $mailer->SMTPAuth = true;
        $mailer->Username = (string) cv_config('smtp.username');
        $mailer->Password = (string) cv_config('smtp.password');
        $mailer->Port = (int) cv_config('smtp.port', 587);
        $mailer->Timeout = (int) cv_config('smtp.timeout', 15);
        $mailer->CharSet = 'UTF-8';

        $encryption = (string) cv_config('smtp.encryption', 'tls');
        if ($encryption !== '') {
            $mailer->SMTPSecure = $encryption;
        }

        $mailer->setFrom((string) cv_config('smtp.from_email'), (string) cv_config('smtp.from_name'));
        $mailer->addAddress((string) cv_config('smtp.to_email', 'fakhridinkon2009@gmail.com'));
        $mailer->addReplyTo((string) ($message['email'] ?? cv_config('smtp.from_email')), (string) ($message['name'] ?? 'Portfolio Lead'));
        $mailer->isHTML(true);
        $mailer->Subject = 'New portfolio lead: ' . ($message['name'] ?? 'Unknown');

        $html = '<h2>New contact submission</h2>'
            . '<p><strong>Name:</strong> ' . cv_e((string) ($message['name'] ?? '')) . '</p>'
            . '<p><strong>Email:</strong> ' . cv_e((string) ($message['email'] ?? '')) . '</p>'
            . '<p><strong>Phone:</strong> ' . cv_e((string) ($message['phone'] ?? '')) . '</p>'
            . '<p><strong>Company:</strong> ' . cv_e((string) ($message['company'] ?? '')) . '</p>'
            . '<p><strong>Budget:</strong> ' . cv_e((string) ($message['budget'] ?? '')) . '</p>'
            . '<p><strong>Message:</strong><br>' . nl2br(cv_e((string) ($message['message'] ?? ''))) . '</p>';

        $plain = "New contact submission\n"
            . 'Name: ' . ($message['name'] ?? '') . "\n"
            . 'Email: ' . ($message['email'] ?? '') . "\n"
            . 'Phone: ' . ($message['phone'] ?? '') . "\n"
            . 'Company: ' . ($message['company'] ?? '') . "\n"
            . 'Budget: ' . ($message['budget'] ?? '') . "\n\n"
            . 'Message: ' . ($message['message'] ?? '');

        $mailer->Body = $html;
        $mailer->AltBody = $plain;
        $mailer->send();

        return ['ok' => true];
    } catch (\Throwable $throwable) {
        cv_log('mail', 'SMTP send failed', ['error' => $throwable->getMessage()]);

        return ['ok' => false, 'error' => $throwable->getMessage()];
    }
}
