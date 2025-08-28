<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/db.php';

require_login();

$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT username, email, created_at FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch() ?: ['username' => $_SESSION['username'] ?? 'User', 'email' => 'unknown', 'created_at' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profile - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>Your Profile</h1></header>
    <nav>
      <a href="index.php">Dashboard</a> |
      <a href="profile.php">Profile</a> |
      <a href="../simulation/hijack_demo.php">Hijacking Demo</a> |
      <a href="../prevention/secure_demo.php">Prevention Demo</a> |
      <a href="../auth/logout.php">Logout</a>
    </nav>

    <section class="card">
      <p><strong>Username:</strong> <?= h($user['username']) ?></p>
      <p><strong>Email:</strong> <?= h($user['email']) ?></p>
      <p><strong>Member Since:</strong> <?= h((string)$user['created_at']) ?></p>
    </section>

    <section class="info">
      <h2>Security Tip</h2>
      <p>Never share your session ID. Avoid logging in from public or untrusted networks without HTTPS.</p>
    </section>
  </div>
</body>
</html>