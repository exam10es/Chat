<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Session;

if (is_admin()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!enforce_rate_limit('login', MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME)) {
        $error = 'تم تجاوز عدد المحاولات، حاول لاحقًا.';
    } else {
        $stmt = db()->prepare('SELECT * FROM admins WHERE username = :username AND is_active = 1');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            Session::regenerate();
            Session::set('admin_id', $admin['id']);
            Session::set('admin_name', $admin['full_name']);
            $update = db()->prepare('UPDATE admins SET last_login = NOW() WHERE id = :id');
            $update->execute([':id' => $admin['id']]);
            redirect('index.php');
        } else {
            $error = 'بيانات الدخول غير صحيحة.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>تسجيل الدخول</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" class="card">
        <label>اسم المستخدم</label>
        <input type="text" name="username" required>
        <label>كلمة المرور</label>
        <input type="password" name="password" required>
        <button class="btn" type="submit">دخول</button>
    </form>
</div>
</body>
</html>
