<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/db.php';

require_login();

$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>Welcome, <?= h($username) ?></h1></header>
    <nav>
      <a href="index.php">Dashboard</a> |
      <a href="profile.php">Profile</a> |
      <a href="../simulation/hijack_demo.php">Hijacking Demo</a> |
      <a href="../prevention/secure_demo.php">Prevention Demo</a> |
      <a href="../auth/logout.php">Logout</a>
    </nav>

    <section class="card">
      <h2>Your Account</h2>
      <p>This dashboard is protected by multiple layers: secure sessions, CSRF protection, and strict output escaping.</p>
    </section>

    <section class="info">
      <h2>Explore</h2>
      <ul>
        <li>Visit the <a href="../simulation/hijack_demo.php">Hijacking Simulation</a> to learn how attacks work.</li>
        <li>Visit the <a href="../prevention/secure_demo.php">Prevention Demo</a> to see protective measures in action.</li>
      </ul>
    </section>
  </div>
</body>
</html>