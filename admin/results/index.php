<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$results = db()->query('SELECT * FROM exam_sessions ORDER BY start_time DESC LIMIT 50')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>النتائج</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>النتائج</h1>
    <table class="table">
        <thead>
        <tr>
            <th>الطالب</th>
            <th>الدرجة</th>
            <th>الأسئلة</th>
            <th>الحالة</th>
            <th>التاريخ</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $result): ?>
            <tr>
                <td><?= htmlspecialchars($result['student_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= (int) $result['total_score']; ?></td>
                <td><?= (int) $result['total_questions']; ?></td>
                <td><?= $result['is_completed'] ? 'مكتمل' : 'قيد التقدم'; ?></td>
                <td><?= htmlspecialchars($result['start_time'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$results): ?>
            <tr><td colspan="5">لا توجد نتائج.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
