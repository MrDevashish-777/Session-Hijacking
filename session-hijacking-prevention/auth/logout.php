<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/session.php';

logout();
header('Location: /Session-Hijacking/session-hijacking-prevention/index.php');
exit;