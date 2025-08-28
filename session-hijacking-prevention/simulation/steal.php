<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';

// Educational only: pretend attacker endpoint that "receives" a SID via query string
$captured = $_GET['sid'] ?? '';
$captured = substr($captured, 0, 6) . (strlen($captured) > 10 ? '…' : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attacker Capture Demo - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>"Attacker" Capture Demo</h1></header>
    <section class="card warning">
      <p>This page demonstrates how an attacker could capture a session identifier if it appears in a URL or is exfiltrated. The captured value here is a <strong>fake demo string</strong>:</p>
      <p><strong>Captured:</strong> <code><?= h($captured) ?></code></p>
      <p><em>Important:</em> This demo never stores or uses real session IDs. In a real attack, the attacker might try to reuse the SID against a vulnerable app.</p>
    </section>
    <nav><a href="../simulation/hijack_demo.php">Back to Hijacking Demo</a></nav>
  </div>
</body>
</html>