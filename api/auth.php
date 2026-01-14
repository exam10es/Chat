<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Response;
use Core\Session;

if (!enforce_rate_limit('api_auth', 100, 60)) {
    Response::json(false, 'تم تجاوز حد الطلبات', [], [], 429);
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($action === 'login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $username = clean_input($data['username'] ?? '');
    $password = $data['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM admins WHERE username = :username AND is_active = 1');
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        Session::regenerate();
        Session::set('admin_id', $admin['id']);
        Session::set('admin_name', $admin['full_name']);
        Response::json(true, 'تم تسجيل الدخول', ['csrf_token' => csrf_token()]);
    }

    Response::json(false, 'بيانات الدخول غير صحيحة', [], ['auth' => ['invalid']], 401);
}

if ($action === 'logout' && $method === 'POST') {
    Session::destroy();
    Response::json(true, 'تم تسجيل الخروج');
}

Response::json(false, 'طلب غير صالح', [], [], 400);
