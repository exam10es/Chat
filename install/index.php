<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../core/Response.php';

use Core\Response;

$errors = [];
$success = false;

$requirements = [
    'php_version' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'pdo' => extension_loaded('pdo'),
    'mysqli' => extension_loaded('mysqli'),
    'mbstring' => extension_loaded('mbstring'),
    'json' => extension_loaded('json'),
];

$writable = is_writable(__DIR__ . '/../config');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = clean_input($_POST['db_host'] ?? '');
    $dbName = clean_input($_POST['db_name'] ?? '');
    $dbUser = clean_input($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    $adminUser = clean_input($_POST['admin_user'] ?? '');
    $adminEmail = clean_input($_POST['admin_email'] ?? '');
    $adminPass = $_POST['admin_pass'] ?? '';
    $adminName = clean_input($_POST['admin_name'] ?? '');

    if (!$dbHost || !$dbName || !$dbUser) {
        $errors[] = 'يرجى إدخال بيانات قاعدة البيانات كاملة.';
    }
    if (!$adminUser || !$adminEmail || !$adminPass || !$adminName) {
        $errors[] = 'يرجى إدخال بيانات المشرف كاملة.';
    }

    if ($errors === []) {
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $dbHost, $dbName);
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $sql = file_get_contents(__DIR__ . '/database.sql');
            $pdo->exec($sql);

            $hashed = password_hash($adminPass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO admins (username, password, email, full_name) VALUES (:username, :password, :email, :full_name)');
            $stmt->execute([
                ':username' => $adminUser,
                ':password' => $hashed,
                ':email' => $adminEmail,
                ':full_name' => $adminName,
            ]);

            $configTemplate = require __DIR__ . '/config.template.php';
            $configTemplate['db'] = [
                'host' => $dbHost,
                'name' => $dbName,
                'user' => $dbUser,
                'pass' => $dbPass,
                'charset' => 'utf8mb4',
            ];

            $configContent = "<?php\n\nreturn " . var_export($configTemplate, true) . ";\n";
            file_put_contents(__DIR__ . '/../config/config.php', $configContent);

            $success = true;

            $installPath = __DIR__;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($installPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iterator as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }
            rmdir($installPath);

            header('Location: ' . ADMIN_URL . '/login.php');
            exit;
        } catch (Throwable $exception) {
            $errors[] = 'حدث خطأ أثناء التثبيت: ' . $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تثبيت النظام</title>
    <link rel="stylesheet" href="../assets/css/public.css">
</head>
<body>
<div class="container">
    <h1>تثبيت نظام إدارة الامتحانات</h1>

    <div class="card">
        <h2>فحص المتطلبات</h2>
        <ul>
            <li>PHP >= 7.4: <?= $requirements['php_version'] ? '✅' : '❌'; ?></li>
            <li>PDO: <?= $requirements['pdo'] ? '✅' : '❌'; ?></li>
            <li>mysqli: <?= $requirements['mysqli'] ? '✅' : '❌'; ?></li>
            <li>mbstring: <?= $requirements['mbstring'] ? '✅' : '❌'; ?></li>
            <li>json: <?= $requirements['json'] ? '✅' : '❌'; ?></li>
            <li>كتابة config/: <?= $writable ? '✅' : '❌'; ?></li>
        </ul>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="card">
        <h2>بيانات قاعدة البيانات</h2>
        <label>المضيف</label>
        <input type="text" name="db_host" required>
        <label>اسم القاعدة</label>
        <input type="text" name="db_name" required>
        <label>المستخدم</label>
        <input type="text" name="db_user" required>
        <label>كلمة المرور</label>
        <input type="password" name="db_pass">

        <h2>بيانات المشرف الأول</h2>
        <label>اسم المستخدم</label>
        <input type="text" name="admin_user" required>
        <label>البريد الإلكتروني</label>
        <input type="email" name="admin_email" required>
        <label>الاسم الكامل</label>
        <input type="text" name="admin_name" required>
        <label>كلمة المرور</label>
        <input type="password" name="admin_pass" required>

        <button type="submit" class="btn">تثبيت النظام</button>
    </form>
</div>
</body>
</html>
