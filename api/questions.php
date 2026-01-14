<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Response;

if (!enforce_rate_limit('api_questions', 100, 60)) {
    Response::json(false, 'تم تجاوز حد الطلبات', [], [], 429);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $chapterId = (int) ($_GET['chapter_id'] ?? 0);
    if ($chapterId) {
        $stmt = db()->prepare('SELECT * FROM questions WHERE chapter_id = :id AND is_active = 1 ORDER BY display_order, id');
        $stmt->execute([':id' => $chapterId]);
        Response::json(true, 'تم التحميل', $stmt->fetchAll());
    }
    $stmt = db()->query('SELECT * FROM questions ORDER BY display_order, id');
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
    $stmt = db()->prepare('INSERT INTO questions (chapter_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, difficulty, points, display_order, is_active) VALUES (:chapter_id, :question_text, :question_type, :option_a, :option_b, :option_c, :option_d, :correct_answer, :difficulty, :points, :display_order, :is_active)');
    $stmt->execute([
        ':chapter_id' => (int) ($input['chapter_id'] ?? 0),
        ':question_text' => clean_input($input['question_text'] ?? ''),
        ':question_type' => clean_input($input['question_type'] ?? 'multiple_choice'),
        ':option_a' => clean_input($input['option_a'] ?? ''),
        ':option_b' => clean_input($input['option_b'] ?? ''),
        ':option_c' => clean_input($input['option_c'] ?? ''),
        ':option_d' => clean_input($input['option_d'] ?? ''),
        ':correct_answer' => strtoupper(clean_input($input['correct_answer'] ?? '')),
        ':difficulty' => clean_input($input['difficulty'] ?? 'medium'),
        ':points' => (int) ($input['points'] ?? 1),
        ':display_order' => (int) ($input['display_order'] ?? 0),
        ':is_active' => isset($input['is_active']) ? 1 : 0,
    ]);
    Response::json(true, 'تمت الإضافة');
}

if ($method === 'PUT') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare('UPDATE questions SET chapter_id = :chapter_id, question_text = :question_text, question_type = :question_type, option_a = :option_a, option_b = :option_b, option_c = :option_c, option_d = :option_d, correct_answer = :correct_answer, difficulty = :difficulty, points = :points, display_order = :display_order, is_active = :is_active WHERE id = :id');
    $stmt->execute([
        ':chapter_id' => (int) ($input['chapter_id'] ?? 0),
        ':question_text' => clean_input($input['question_text'] ?? ''),
        ':question_type' => clean_input($input['question_type'] ?? 'multiple_choice'),
        ':option_a' => clean_input($input['option_a'] ?? ''),
        ':option_b' => clean_input($input['option_b'] ?? ''),
        ':option_c' => clean_input($input['option_c'] ?? ''),
        ':option_d' => clean_input($input['option_d'] ?? ''),
        ':correct_answer' => strtoupper(clean_input($input['correct_answer'] ?? '')),
        ':difficulty' => clean_input($input['difficulty'] ?? 'medium'),
        ':points' => (int) ($input['points'] ?? 1),
        ':display_order' => (int) ($input['display_order'] ?? 0),
        ':is_active' => isset($input['is_active']) ? 1 : 0,
        ':id' => $id,
    ]);
    Response::json(true, 'تم التحديث');
}

if ($method === 'DELETE') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM questions WHERE id = :id');
    $stmt->execute([':id' => $id]);
    Response::json(true, 'تم الحذف');
}

Response::json(false, 'طلب غير صالح', [], [], 400);
