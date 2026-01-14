<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Response;

if (!enforce_rate_limit('api_results', 100, 60)) {
    Response::json(false, 'تم تجاوز حد الطلبات', [], [], 429);
}

$action = $_GET['action'] ?? '';
$sessionId = (int) ($_GET['session_id'] ?? 0);

if ($action === 'statistics') {
    $stats = db()->query('SELECT * FROM statistics ORDER BY stat_date DESC LIMIT 7')->fetchAll();
    Response::json(true, 'تم التحميل', $stats);
}

if ($sessionId) {
    $stmt = db()->prepare('SELECT * FROM exam_sessions WHERE id = :id');
    $stmt->execute([':id' => $sessionId]);
    $session = $stmt->fetch();
    if ($session) {
        Response::json(true, 'تم التحميل', $session);
    }
    Response::json(false, 'النتيجة غير موجودة', [], [], 404);
}

Response::json(false, 'طلب غير صالح', [], [], 400);
