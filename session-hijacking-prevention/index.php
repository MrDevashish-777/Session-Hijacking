<?php
declare(strict_types=1);
require_once __DIR__ . '/config/session.php';
secure_session_start();
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= h(APP_NAME) ?> - Home</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1><?= h(APP_NAME) ?></h1></header>
    <section class="card">
      <p>This project demonstrates both the <strong>risks</strong> of session hijacking and the <strong>solutions</strong> to defend against it using modern PHP best practices.</p>
      <div>
        <?php if ($loggedIn): ?>
          <a class="btn primary" href="dashboard/index.php">Go to Dashboard</a>
          <a class="btn" href="auth/logout.php">Logout</a>
        <?php else: ?>
          <a class="btn primary" href="auth/login.php">Login</a>
          <a class="btn" href="auth/register.php">Register</a>
        <?php endif; ?>
      </div>
    </section>

    <section class="info">
      <h2>Included Demos</h2>
      <ul>
        <li><strong>Hijacking Demo</strong>: Safe simulation of how SIDs can be exposed.</li>
        <li><strong>Prevention Demo</strong>: Shows CSRF, secure cookies, and session binding.</li>
      </ul>
    </section>
  </div>
</body>
</html>