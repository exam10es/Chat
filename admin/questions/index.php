<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$questions = db()->query('SELECT questions.*, chapters.name AS chapter_name FROM questions JOIN chapters ON chapters.id = questions.chapter_id ORDER BY questions.display_order, questions.id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الأسئلة</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>الأسئلة</h1>
    <a class="btn" href="create.php">إضافة سؤال</a>
    <table class="table">
        <thead>
        <tr>
            <th>السؤال</th>
            <th>الفصل</th>
            <th>الصعوبة</th>
            <th>الحالة</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($questions as $question): ?>
            <tr>
                <td><?= htmlspecialchars(mb_strimwidth($question['question_text'], 0, 50, '...'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($question['chapter_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($question['difficulty'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><span class="badge"><?= $question['is_active'] ? 'نشط' : 'غير نشط'; ?></span></td>
                <td>
                    <a class="btn" href="edit.php?id=<?= (int) $question['id']; ?>">تعديل</a>
                    <a class="btn" data-confirm="تأكيد الحذف؟" href="delete.php?id=<?= (int) $question['id']; ?>">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$questions): ?>
            <tr><td colspan="5">لا توجد بيانات.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/js/admin.js"></script>
</body>
</html>
