<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';

/** Get or create a CSRF token (rotates every 30 minutes or after use). */
function csrf_token(): string {
    secure_session_start();
    if (empty($_SESSION['csrf_token']) || time() - ($_SESSION['csrf_token_time'] ?? 0) > 1800) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/** HTML input field for CSRF token. */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . h($token) . '" />';
}

/** Validate a submitted CSRF token; rotate on success. */
function verify_csrf_token(?string $token): bool {
    secure_session_start();
    if (!$token || empty($_SESSION['csrf_token'])) { return false; }
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    if ($valid) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $valid;
}