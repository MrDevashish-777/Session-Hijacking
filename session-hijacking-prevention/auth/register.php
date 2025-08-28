<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../prevention/csrf_token.php';

secure_session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid CSRF token.';
    }

    if ($username === '' || !preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $errors[] = 'Username must be 3-50 chars, letters/numbers/underscore only.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (!$errors) {
        try {
            $pdo = get_pdo();
            // Check uniqueness
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
            $stmt->execute([':u' => $username, ':e' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:u, :e, :p)');
                $ins->execute([':u' => $username, ':e' => $email, ':p' => $hash]);
                $success = true;
            }
        } catch (Throwable $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>Create Account</h1></header>
    <nav><a href="../index.php">Home</a> | <a href="login.php">Login</a></nav>

    <?php if ($success): ?>
      <div class="alert success">Registration successful. <a href="login.php">Login now</a>.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="alert error">
        <ul>
        <?php foreach ($errors as $err): ?><li><?= h($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" class="card">
      <?= csrf_field() ?>
      <label>Username
        <input type="text" name="username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+" value="<?= h($_POST['username'] ?? '') ?>" />
      </label>
      <label>Email
        <input type="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>" />
      </label>
      <label>Password
        <input type="password" name="password" required minlength="8" />
      </label>
      <button type="submit" class="btn primary">Register</button>
    </form>

    <section class="info">
      <h2>Security Notes</h2>
      <ul>
        <li>Passwords are hashed with password_hash() using a modern algorithm.</li>
        <li>All database operations use prepared statements to prevent SQL injection.</li>
        <li>CSRF token is required to submit this form.</li>
      </ul>
    </section>
  </div>
</body>
</html>