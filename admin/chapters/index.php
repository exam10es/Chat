<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$chapters = db()->query('SELECT chapters.*, subjects.name AS subject_name FROM chapters JOIN subjects ON subjects.id = chapters.subject_id ORDER BY chapters.display_order, chapters.id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الفصول</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>الفصول</h1>
    <a class="btn" href="create.php">إضافة فصل</a>
    <table class="table">
        <thead>
        <tr>
            <th>الفصل</th>
            <th>المادة</th>
            <th>الحالة</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($chapters as $chapter): ?>
            <tr>
                <td><?= htmlspecialchars($chapter['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($chapter['subject_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><span class="badge"><?= $chapter['is_active'] ? 'نشط' : 'غير نشط'; ?></span></td>
                <td>
                    <a class="btn" href="edit.php?id=<?= (int) $chapter['id']; ?>">تعديل</a>
                    <a class="btn" data-confirm="تأكيد الحذف؟" href="delete.php?id=<?= (int) $chapter['id']; ?>">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$chapters): ?>
            <tr><td colspan="4">لا توجد بيانات.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/js/admin.js"></script>
</body>
</html>
