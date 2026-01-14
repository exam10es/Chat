<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

$pdo = db();
$sessionToken = $_SESSION['exam_token'] ?? null;
$chapterId = (int) ($_POST['chapter_id'] ?? 0);
$studentName = clean_input($_POST['student_name'] ?? '');
$submittedAnswers = $_POST['answers'] ?? null;

if ($chapterId && $studentName) {
    $sessionToken = generate_token(64);
    $_SESSION['exam_token'] = $sessionToken;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM questions WHERE chapter_id = :chapter_id AND is_active = 1');
    $stmt->execute([':chapter_id' => $chapterId]);
    $totalQuestions = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('INSERT INTO exam_sessions (session_token, chapter_id, student_name, total_questions, ip_address, user_agent) VALUES (:token, :chapter_id, :student_name, :total_questions, :ip, :ua)');
    $stmt->execute([
        ':token' => $sessionToken,
        ':chapter_id' => $chapterId,
        ':student_name' => $studentName,
        ':total_questions' => $totalQuestions,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    ]);
}

if ($submittedAnswers && $sessionToken) {
    $answers = json_decode($submittedAnswers, true) ?? [];
    $stmt = $pdo->prepare('SELECT * FROM exam_sessions WHERE session_token = :token');
    $stmt->execute([':token' => $sessionToken]);
    $session = $stmt->fetch();

    if ($session) {
        $score = 0;
        foreach ($answers as $questionId => $answer) {
            $questionStmt = $pdo->prepare('SELECT correct_answer, points FROM questions WHERE id = :id');
            $questionStmt->execute([':id' => (int) $questionId]);
            $question = $questionStmt->fetch();
            if ($question) {
                $isCorrect = strtoupper($answer) === strtoupper($question['correct_answer']);
                if ($isCorrect) {
                    $score += (int) $question['points'];
                }
                $insert = $pdo->prepare('INSERT INTO student_answers (session_id, question_id, student_answer, is_correct) VALUES (:session_id, :question_id, :answer, :is_correct)');
                $insert->execute([
                    ':session_id' => $session['id'],
                    ':question_id' => (int) $questionId,
                    ':answer' => strtoupper($answer),
                    ':is_correct' => $isCorrect ? 1 : 0,
                ]);
            }
        }

        $update = $pdo->prepare('UPDATE exam_sessions SET is_completed = 1, total_score = :score, end_time = NOW(), answers = :answers WHERE id = :id');
        $update->execute([
            ':score' => $score,
            ':answers' => json_encode($answers),
            ':id' => $session['id'],
        ]);

        header('Location: result.php?session_id=' . $session['id']);
        exit;
    }
}

$questions = [];
$chapter = null;
if ($sessionToken) {
    $stmt = $pdo->prepare('SELECT * FROM exam_sessions WHERE session_token = :token');
    $stmt->execute([':token' => $sessionToken]);
    $session = $stmt->fetch();

    if ($session) {
        $chapterStmt = $pdo->prepare('SELECT * FROM chapters WHERE id = :id');
        $chapterStmt->execute([':id' => $session['chapter_id']]);
        $chapter = $chapterStmt->fetch();

        $questionStmt = $pdo->prepare('SELECT * FROM questions WHERE chapter_id = :id AND is_active = 1 ORDER BY display_order, id');
        $questionStmt->execute([':id' => $session['chapter_id']]);
        $questions = $questionStmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الامتحان</title>
    <link rel="stylesheet" href="../assets/css/public.css">
</head>
<body>
<div class="container">
    <?php if ($chapter && $questions): ?>
        <h1>امتحان <?= htmlspecialchars($chapter['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="card">
            <div id="progress"></div>
            <div id="question-container"></div>
            <div class="grid grid-2">
                <button class="btn" type="button" id="prev-btn">السابق</button>
                <button class="btn" type="button" id="next-btn">التالي</button>
            </div>
            <button class="btn" type="button" id="submit-btn">إرسال الامتحان</button>
        </div>
        <form id="exam-form" method="post">
            <input type="hidden" id="answers-input" name="answers">
        </form>
        <script id="exam-data" type="application/json"><?= json_encode($questions, JSON_UNESCAPED_UNICODE); ?></script>
        <script src="../assets/js/exam.js"></script>
    <?php else: ?>
        <div class="card">لا يوجد امتحان متاح.</div>
    <?php endif; ?>
</div>
</body>
</html>
