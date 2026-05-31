<?php

declare(strict_types=1);

// Minimal admin API: messages
$root = dirname(__DIR__, 2);
chdir($root);
require $root . '/app/bootstrap.php';

if (!cv_is_admin_authenticated()) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Unauthorized'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// optional status filter or id
$status = (string) cv_get('status', '');
$id = (int) cv_get('id', 0);

if ($id > 0) {
    $message = cv_find_by_id('contact_messages', $id);
    if (!$message) {
        cv_json_response(['ok' => false, 'error' => 'Not found'], 404);
    }
    cv_json_response(['ok' => true, 'message' => $message]);
}

$messages = cv_fetch_contact_messages($status);
cv_json_response(['ok' => true, 'messages' => $messages]);
