<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$subjects = db()->query('SELECT subjects.*, specializations.name AS specialization_name FROM subjects JOIN specializations ON specializations.id = subjects.specialization_id ORDER BY subjects.display_order, subjects.id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>المواد</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>المواد</h1>
    <a class="btn" href="create.php">إضافة مادة</a>
    <table class="table">
        <thead>
        <tr>
            <th>المادة</th>
            <th>التخصص</th>
            <th>الحالة</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($subjects as $subject): ?>
            <tr>
                <td><?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($subject['specialization_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><span class="badge"><?= $subject['is_active'] ? 'نشط' : 'غير نشط'; ?></span></td>
                <td>
                    <a class="btn" href="edit.php?id=<?= (int) $subject['id']; ?>">تعديل</a>
                    <a class="btn" data-confirm="تأكيد الحذف؟" href="delete.php?id=<?= (int) $subject['id']; ?>">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$subjects): ?>
            <tr><td colspan="4">لا توجد بيانات.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/js/admin.js"></script>
</body>
</html>
