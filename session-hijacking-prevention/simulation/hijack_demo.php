<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../prevention/csrf_token.php';

require_login();
secure_session_start();

// Demonstration-only: We will show a masked session ID and construct a fake URL param.
$realSid = session_id();
$maskedSid = substr($realSid, 0, 6) . str_repeat('•', max(0, strlen($realSid) - 10)) . substr($realSid, -4);
$fakeLeakSid = bin2hex(random_bytes(8)); // Do NOT leak the real SID
$attackerUrl = '/Session-Hijacking/session-hijacking-prevention/simulation/steal.php?sid=' . urlencode($fakeLeakSid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hijacking Simulation - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>Session Hijacking Simulation</h1></header>
    <nav>
      <a href="../dashboard/index.php">Dashboard</a> |
      <a href="hijack_demo.php">Hijacking Demo</a> |
      <a href="../prevention/secure_demo.php">Prevention Demo</a>
    </nav>

    <section class="card">
      <h2>What is Session Hijacking?</h2>
      <p>Session hijacking occurs when an attacker obtains a victim's session identifier (SID) and uses it to impersonate the user. Common sources include insecure transport (no HTTPS), <em>URL-based</em> session IDs, XSS stealing cookies, or malware.</p>
      <p><strong>Your current session ID (masked):</strong> <code><?= h($maskedSid) ?></code></p>
    </section>

    <section class="card warning">
      <h2>Demonstration: URL-based SID leakage</h2>
      <p>Some insecure apps used to put session IDs in the URL (e.g., <code>?PHPSESSID=...</code>). If such a URL is shared or logged, an attacker can capture it.</p>
      <p>This demo uses a <strong>fake</strong> SID to illustrate the risk. Click the link below to simulate an attacker capturing a session identifier:</p>
      <p><a class="btn danger" href="<?= h($attackerUrl) ?>" target="_blank" rel="noopener noreferrer">Simulate Attacker Capturing SID</a></p>
      <p><em>Note:</em> This project disables URL-based sessions (<code>session.use_only_cookies = 1</code>), sets HttpOnly and SameSite flags, and never exposes the real session ID.</p>
    </section>

    <section class="info">
      <h2>Learn</h2>
      <ul>
        <li>Always use HTTPS to protect cookies in transit.</li>
        <li>Never use URL-based session IDs; use secure cookies only.</li>
        <li>Implement idle and absolute timeouts. Bind sessions to IP/User-Agent.</li>
      </ul>
    </section>
  </div>
</body>
</html>