<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

use Core\Response;

if (!enforce_rate_limit('api_exam', 100, 60)) {
    Response::json(false, 'تم تجاوز حد الطلبات', [], [], 429);
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($action === 'start' && $method === 'POST') {
    $chapterId = (int) ($_GET['chapter_id'] ?? 0);
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $studentName = clean_input($data['student_name'] ?? '');

    if (!$chapterId) {
        Response::json(false, 'معرف الفصل مطلوب', [], [], 422);
    }

    $stmt = db()->prepare('SELECT COUNT(*) FROM questions WHERE chapter_id = :id AND is_active = 1');
    $stmt->execute([':id' => $chapterId]);
    $totalQuestions = (int) $stmt->fetchColumn();

    $token = generate_token(64);
    $insert = db()->prepare('INSERT INTO exam_sessions (session_token, chapter_id, student_name, total_questions, ip_address, user_agent) VALUES (:token, :chapter_id, :student_name, :total_questions, :ip, :ua)');
    $insert->execute([
        ':token' => $token,
        ':chapter_id' => $chapterId,
        ':student_name' => $studentName,
        ':total_questions' => $totalQuestions,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    ]);

    $questions = db()->prepare('SELECT * FROM questions WHERE chapter_id = :id AND is_active = 1 ORDER BY display_order, id');
    $questions->execute([':id' => $chapterId]);

    Response::json(true, 'تم بدء الامتحان', [
        'token' => $token,
        'questions' => $questions->fetchAll(),
    ]);
}

if ($action === 'answer' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $token = $data['token'] ?? '';
    $answers = $data['answers'] ?? [];

    $stmt = db()->prepare('UPDATE exam_sessions SET answers = :answers, current_question_index = :current WHERE session_token = :token');
    $stmt->execute([
        ':answers' => json_encode($answers),
        ':current' => (int) ($data['current_question_index'] ?? 0),
        ':token' => $token,
    ]);

    Response::json(true, 'تم حفظ الإجابة');
}

if ($action === 'submit' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $token = $data['token'] ?? '';
    $answers = $data['answers'] ?? [];

    $stmt = db()->prepare('SELECT * FROM exam_sessions WHERE session_token = :token');
    $stmt->execute([':token' => $token]);
    $session = $stmt->fetch();

    if (!$session) {
        Response::json(false, 'الجلسة غير موجودة', [], [], 404);
    }

    $score = 0;
    foreach ($answers as $questionId => $answer) {
        $questionStmt = db()->prepare('SELECT correct_answer, points FROM questions WHERE id = :id');
        $questionStmt->execute([':id' => (int) $questionId]);
        $question = $questionStmt->fetch();
        if ($question) {
            $isCorrect = strtoupper($answer) === strtoupper($question['correct_answer']);
            if ($isCorrect) {
                $score += (int) $question['points'];
            }
            $insert = db()->prepare('INSERT INTO student_answers (session_id, question_id, student_answer, is_correct) VALUES (:session_id, :question_id, :answer, :is_correct)');
            $insert->execute([
                ':session_id' => $session['id'],
                ':question_id' => (int) $questionId,
                ':answer' => strtoupper($answer),
                ':is_correct' => $isCorrect ? 1 : 0,
            ]);
        }
    }

    $update = db()->prepare('UPDATE exam_sessions SET is_completed = 1, total_score = :score, end_time = NOW(), answers = :answers WHERE id = :id');
    $update->execute([
        ':score' => $score,
        ':answers' => json_encode($answers),
        ':id' => $session['id'],
    ]);

    Response::json(true, 'تم إرسال الامتحان', ['score' => $score, 'session_id' => $session['id']]);
}

if ($action === 'resume' && $method === 'GET') {
    $token = $_GET['token'] ?? '';
    $stmt = db()->prepare('SELECT * FROM exam_sessions WHERE session_token = :token');
    $stmt->execute([':token' => $token]);
    $session = $stmt->fetch();

    if (!$session) {
        Response::json(false, 'الجلسة غير موجودة', [], [], 404);
    }

    Response::json(true, 'تم الاسترجاع', $session);
}

Response::json(false, 'طلب غير صالح', [], [], 400);
