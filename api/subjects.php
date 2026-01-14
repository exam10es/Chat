<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Response;

if (!enforce_rate_limit('api_subjects', 100, 60)) {
    Response::json(false, 'تم تجاوز حد الطلبات', [], [], 429);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $specId = (int) ($_GET['specialization_id'] ?? 0);
    if ($specId) {
        $stmt = db()->prepare('SELECT * FROM subjects WHERE specialization_id = :id AND is_active = 1 ORDER BY display_order, id');
        $stmt->execute([':id' => $specId]);
        Response::json(true, 'تم التحميل', $stmt->fetchAll());
    }
    $stmt = db()->query('SELECT * FROM subjects ORDER BY display_order, id');
    Response::json(true, 'تم التحميل', $stmt->fetchAll());
}

if (!is_admin()) {
    Response::json(false, 'غير مصرح', [], [], 403);
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

if (in_array($method, ['POST', 'PUT', 'DELETE'], true) && !verify_csrf($input['csrf_token'] ?? '')) {
    Response::json(false, 'رمز التحقق غير صالح', [], [], 422);
}

if ($method === 'POST') {
    $stmt = db()->prepare('INSERT INTO subjects (specialization_id, name, description, display_order, is_active) VALUES (:specialization_id, :name, :description, :display_order, :is_active)');
    $stmt->execute([
        ':specialization_id' => (int) ($input['specialization_id'] ?? 0),
        ':name' => clean_input($input['name'] ?? ''),
        ':description' => clean_input($input['description'] ?? ''),
        ':display_order' => (int) ($input['display_order'] ?? 0),
        ':is_active' => isset($input['is_active']) ? 1 : 0,
    ]);
    Response::json(true, 'تمت الإضافة');
}

if ($method === 'PUT') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare('UPDATE subjects SET specialization_id = :specialization_id, name = :name, description = :description, display_order = :display_order, is_active = :is_active WHERE id = :id');
    $stmt->execute([
        ':specialization_id' => (int) ($input['specialization_id'] ?? 0),
        ':name' => clean_input($input['name'] ?? ''),
        ':description' => clean_input($input['description'] ?? ''),
        ':display_order' => (int) ($input['display_order'] ?? 0),
        ':is_active' => isset($input['is_active']) ? 1 : 0,
        ':id' => $id,
    ]);
    Response::json(true, 'تم التحديث');
}

if ($method === 'DELETE') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM subjects WHERE id = :id');
    $stmt->execute([':id' => $id]);
    Response::json(true, 'تم الحذف');
}

Response::json(false, 'طلب غير صالح', [], [], 400);
