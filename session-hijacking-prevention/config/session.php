<?php
declare(strict_types=1);

// HTML escape helper for safe output
define('APP_NAME', 'Session Hijacking Prevention');
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/**
 * Start a secure PHP session with hardened cookie flags and settings.
 */
function secure_session_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) { return; }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $cookieParams = [
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax', // Consider 'Strict' for maximum protection
    ];
    session_name('SHPSID');
    session_set_cookie_params($cookieParams);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1'); // Disallow SID in URLs
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', $cookieParams['samesite']);
    if ($secure) { ini_set('session.cookie_secure', '1'); }
    session_start();
}

const IDLE_TIMEOUT = 900;       // 15 minutes
const ABSOLUTE_TIMEOUT = 28800; // 8 hours

/** Create a fingerprint binding the session to client properties. */
function fingerprint(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    // Use raw IP string to support both IPv4 and IPv6
    return hash('sha256', $ua . '|' . $ip);
}

/** Initialize context on first request after login. */
function establish_session_context(): void {
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['fingerprint'] = fingerprint();
    }
}

/** Enforce idle timeout, absolute timeout, and fingerprint checks. */
function enforce_session_security(): void {
    $now = time();
    if (!isset($_SESSION['fingerprint']) || $_SESSION['fingerprint'] !== fingerprint()) {
        logout();
        header('Location: /Session-Hijacking/session-hijacking-prevention/auth/login.php?m=session_reset');
        exit;
    }
    if (isset($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity']) > IDLE_TIMEOUT) {
        logout();
        header('Location: /Session-Hijacking/session-hijacking-prevention/auth/login.php?m=timeout');
        exit;
    }
    if (isset($_SESSION['created_at']) && ($now - (int)$_SESSION['created_at']) > ABSOLUTE_TIMEOUT) {
        logout();
        header('Location: /Session-Hijacking/session-hijacking-prevention/auth/login.php?m=expired');
        exit;
    }
    $_SESSION['last_activity'] = $now;
}

/** Set session state for authenticated users and regenerate ID. */
function login_user(int $userId, string $username): void {
    secure_session_start();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['created_at'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['fingerprint'] = fingerprint();
}

/** Is the current user authenticated? */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/** Protect pages that require authentication. */
function require_login(): void {
    secure_session_start();
    establish_session_context();
    if (!is_logged_in()) {
        header('Location: /Session-Hijacking/session-hijacking-prevention/auth/login.php');
        exit;
    }
    enforce_session_security();
}

/** Destroy all session data and cookie. */
function logout(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        secure_session_start();
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    session_destroy();
}