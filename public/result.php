<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

$sessionId = (int) ($_GET['session_id'] ?? 0);
$session = null;

if ($sessionId) {
    $stmt = db()->prepare('SELECT * FROM exam_sessions WHERE id = :id');
    $stmt->execute([':id' => $sessionId]);
    $session = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>النتيجة</title>
    <link rel="stylesheet" href="../assets/css/public.css">
</head>
<body>
<div class="container">
    <h1>نتيجة الامتحان</h1>
    <?php if ($session): ?>
        <div class="card">
            <p>الطالب: <?= htmlspecialchars($session['student_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
            <p>الدرجة: <?= (int) $session['total_score']; ?></p>
            <p>عدد الأسئلة: <?= (int) $session['total_questions']; ?></p>
            <a class="btn" href="index.php">العودة للرئيسية</a>
        </div>
    <?php else: ?>
        <div class="card">النتيجة غير موجودة.</div>
    <?php endif; ?>
</div>
</body>
</html>
