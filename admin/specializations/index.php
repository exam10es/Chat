<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$specializations = db()->query('SELECT * FROM specializations ORDER BY display_order, id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التخصصات</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>التخصصات</h1>
    <a class="btn" href="create.php">إضافة تخصص</a>
    <table class="table">
        <thead>
        <tr>
            <th>الاسم</th>
            <th>الحالة</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($specializations as $spec): ?>
            <tr>
                <td><?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><span class="badge"><?= $spec['is_active'] ? 'نشط' : 'غير نشط'; ?></span></td>
                <td>
                    <a class="btn" href="edit.php?id=<?= (int) $spec['id']; ?>">تعديل</a>
                    <a class="btn" data-confirm="تأكيد الحذف؟" href="delete.php?id=<?= (int) $spec['id']; ?>">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$specializations): ?>
            <tr><td colspan="3">لا توجد بيانات.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="../../assets/js/admin.js"></script>
</body>
</html>
