<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!is_admin()) {
    redirect('login.php');
}

$pdo = db();
$stats = [
    'specializations' => (int) $pdo->query('SELECT COUNT(*) FROM specializations')->fetchColumn(),
    'subjects' => (int) $pdo->query('SELECT COUNT(*) FROM subjects')->fetchColumn(),
    'questions' => (int) $pdo->query('SELECT COUNT(*) FROM questions')->fetchColumn(),
    'today_exams' => (int) $pdo->query('SELECT COUNT(*) FROM exam_sessions WHERE DATE(start_time) = CURDATE()')->fetchColumn(),
];

$results = $pdo->query('SELECT * FROM exam_sessions ORDER BY start_time DESC LIMIT 10')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/mobile.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2><?= SYSTEM_NAME; ?></h2>
        <a href="index.php">الرئيسية</a>
        <a href="specializations/index.php">التخصصات</a>
        <a href="subjects/index.php">المواد</a>
        <a href="chapters/index.php">الفصول</a>
        <a href="questions/index.php">الأسئلة</a>
        <a href="results/index.php">النتائج</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>

    <div class="grid grid-2">
        <div class="card">إجمالي التخصصات: <?= $stats['specializations']; ?></div>
        <div class="card">إجمالي المواد: <?= $stats['subjects']; ?></div>
        <div class="card">إجمالي الأسئلة: <?= $stats['questions']; ?></div>
        <div class="card">امتحانات اليوم: <?= $stats['today_exams']; ?></div>
    </div>

    <div class="card">
        <h3>آخر النتائج</h3>
        <table class="table">
            <thead>
            <tr>
                <th>الطالب</th>
                <th>الدرجة</th>
                <th>الأسئلة</th>
                <th>التاريخ</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?= htmlspecialchars($result['student_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= (int) $result['total_score']; ?></td>
                    <td><?= (int) $result['total_questions']; ?></td>
                    <td><?= htmlspecialchars($result['start_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$results): ?>
                <tr><td colspan="4">لا توجد نتائج.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
