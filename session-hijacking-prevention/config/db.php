<?php
declare(strict_types=1);

// Database connection using PDO (utf8mb4)
// Configure via environment variables when deploying (DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT)
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'session_security';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_PORT = getenv('DB_PORT') ?: '3306';

/**
 * Get a shared PDO instance.
 */
function get_pdo(): PDO {
    static $pdo;
    if ($pdo instanceof PDO) { return $pdo; }
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT;
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};port={$DB_PORT};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    return $pdo;
}