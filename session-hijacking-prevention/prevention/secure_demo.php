<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../prevention/csrf_token.php';

require_login();
secure_session_start();

// CSRF-protected example action
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;
    if (verify_csrf_token($token)) {
        $result = 'CSRF token valid. Action completed securely.';
    } else {
        $result = 'CSRF validation failed. Action blocked.';
    }
}

$cookieParams = session_get_cookie_params();
$flags = [
  'HttpOnly' => true,
  'Secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
  'SameSite' => ini_get('session.cookie_samesite') ?: 'Lax',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prevention Demo - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>Session Hijacking Prevention</h1></header>
    <nav>
      <a href="../dashboard/index.php">Dashboard</a> |
      <a href="../simulation/hijack_demo.php">Hijacking Demo</a>
    </nav>

    <section class="card">
      <h2>Active Protections</h2>
      <ul>
        <li><strong>Cookie Flags</strong>: HttpOnly = <?= h($flags['HttpOnly'] ? 'true' : 'false') ?>, Secure = <?= h($flags['Secure'] ? 'true' : 'false') ?>, SameSite = <?= h((string)$flags['SameSite']) ?></li>
        <li><strong>Session Timeouts</strong>: Idle = <?= h((string)IDLE_TIMEOUT) ?>s, Absolute = <?= h((string)ABSOLUTE_TIMEOUT) ?>s</li>
        <li><strong>Fingerprint</strong>: Session is bound to your IP/User-Agent</li>
        <li><strong>CSRF</strong>: Token required on all POST forms</li>
        <li><strong>XSS</strong>: All output encoded with htmlspecialchars()</li>
      </ul>
    </section>

    <section class="card">
      <h2>CSRF-Protected Action</h2>
      <?php if ($result): ?><div class="alert <?= strpos($result, 'failed') !== false ? 'error' : 'success' ?>"><?= h($result) ?></div><?php endif; ?>
      <form method="post">
        <?= csrf_field() ?>
        <button type="submit" class="btn">Perform Secure Action</button>
      </form>
    </section>

    <section class="info">
      <h2>Best Practices</h2>
      <ul>
        <li>Use HTTPS everywhere and set <code>Secure</code> cookie flag.</li>
        <li>Regenerate session ID after login and on privilege changes.</li>
        <li>Implement idle and absolute session timeouts.</li>
        <li>Validate CSRF tokens on state-changing requests.</li>
        <li>Sanitize inputs and escape outputs to prevent XSS.</li>
      </ul>
    </section>
  </div>
</body>
</html>