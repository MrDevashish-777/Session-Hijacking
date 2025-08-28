<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../prevention/csrf_token.php';

secure_session_start();
// TEMP: enable inline debug to surface the exact error during login
putenv('APP_DEBUG=1');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // username or email
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid CSRF token.';
    }

    if ($identifier === '' || $password === '') {
        $errors[] = 'All fields are required.';
    }

    if (!$errors) {
        try {
            $pdo = get_pdo();
            $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $sql = "SELECT id, username, email, password_hash FROM users WHERE $field = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$identifier]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                login_user((int)$user['id'], $user['username']);
                header('Location: /session-hijacking-prevention/dashboard/index.php');
                exit;
            } else {
                $errors[] = 'Invalid credentials.';
            }
        } catch (PDOException $e) {
            error_log('[LOGIN][PDO] ' . $e->getMessage());
            if (getenv('APP_DEBUG') === '1') {
                $errors[] = 'DEBUG: ' . $e->getMessage();
            } else {
                $errors[] = 'Login failed. Please try again.';
            }
        } catch (Throwable $e) {
            error_log('[LOGIN] ' . $e->getMessage());
            if (getenv('APP_DEBUG') === '1') {
                $errors[] = 'DEBUG: ' . $e->getMessage();
            } else {
                $errors[] = 'Login failed. Please try again.';
            }
        }
    }
}
$msg = '';
if (isset($_GET['m'])) {
    $map = [
        'timeout' => 'Session timed out due to inactivity.',
        'expired' => 'Session expired. Please login again.',
        'session_reset' => 'Session security check failed. Please login again.',
    ];
    $msg = $map[$_GET['m']] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="container">
    <header><h1>Login</h1></header>
    <nav><a href="../index.php">Home</a> | <a href="register.php">Register</a></nav>

    <?php if ($msg): ?><div class="alert info"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert error">
        <ul><?php foreach ($errors as $err): ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" class="card">
      <?= csrf_field() ?>
      <label>Username or Email
        <input type="text" name="identifier" required value="<?= h($_POST['identifier'] ?? '') ?>" />
      </label>
      <label>Password
        <input type="password" name="password" required />
      </label>
      <button type="submit" class="btn primary">Login</button>
    </form>

    <section class="info">
      <h2>Security Highlights</h2>
      <ul>
        <li>Session ID is regenerated after successful login.</li>
        <li>Cookies set with HttpOnly, Secure (when HTTPS), SameSite=Lax.</li>
        <li>Device fingerprint (IP + User-Agent) checked on every request.</li>
      </ul>
    </section>
  </div>
</body>
</html>