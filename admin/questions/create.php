<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (!is_admin()) {
    redirect('../login.php');
}

$chapters = db()->query('SELECT * FROM chapters ORDER BY name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'رمز التحقق غير صالح.';
    } else {
        $chapterId = (int) ($_POST['chapter_id'] ?? 0);
        $questionText = clean_input($_POST['question_text'] ?? '');
        $type = clean_input($_POST['question_type'] ?? 'multiple_choice');
        $optionA = clean_input($_POST['option_a'] ?? '');
        $optionB = clean_input($_POST['option_b'] ?? '');
        $optionC = clean_input($_POST['option_c'] ?? '');
        $optionD = clean_input($_POST['option_d'] ?? '');
        $correct = clean_input($_POST['correct_answer'] ?? '');
        $difficulty = clean_input($_POST['difficulty'] ?? 'medium');
        $points = (int) ($_POST['points'] ?? 1);
        $order = (int) ($_POST['display_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($chapterId && $questionText && $correct) {
            $stmt = db()->prepare('INSERT INTO questions (chapter_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, difficulty, points, display_order, is_active) VALUES (:chapter_id, :question_text, :question_type, :option_a, :option_b, :option_c, :option_d, :correct_answer, :difficulty, :points, :display_order, :is_active)');
            $stmt->execute([
                ':chapter_id' => $chapterId,
                ':question_text' => $questionText,
                ':question_type' => $type,
                ':option_a' => $optionA,
                ':option_b' => $optionB,
                ':option_c' => $optionC,
                ':option_d' => $optionD,
                ':correct_answer' => strtoupper($correct),
                ':difficulty' => $difficulty,
                ':points' => $points,
                ':display_order' => $order,
                ':is_active' => $isActive,
            ]);
            redirect('index.php');
        } else {
            $error = 'يرجى إدخال جميع البيانات.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة سؤال</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>إضافة سؤال</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" class="card">
        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
        <label>الفصل</label>
        <select name="chapter_id" required>
            <option value="">اختر</option>
            <?php foreach ($chapters as $chapter): ?>
                <option value="<?= (int) $chapter['id']; ?>"><?= htmlspecialchars($chapter['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
        <label>نص السؤال</label>
        <textarea name="question_text" required></textarea>
        <label>نوع السؤال</label>
        <select name="question_type">
            <option value="multiple_choice">اختيار من متعدد</option>
            <option value="true_false">صح/خطأ</option>
        </select>
        <label>الخيار A</label>
        <input type="text" name="option_a">
        <label>الخيار B</label>
        <input type="text" name="option_b">
        <label>الخيار C</label>
        <input type="text" name="option_c">
        <label>الخيار D</label>
        <input type="text" name="option_d">
        <label>الإجابة الصحيحة</label>
        <input type="text" name="correct_answer" required>
        <label>الصعوبة</label>
        <select name="difficulty">
            <option value="easy">سهل</option>
            <option value="medium" selected>متوسط</option>
            <option value="hard">صعب</option>
        </select>
        <label>الدرجة</label>
        <input type="number" name="points" value="1">
        <label>ترتيب العرض</label>
        <input type="number" name="display_order" value="0">
        <label>
            <input type="checkbox" name="is_active" checked> نشط
        </label>
        <button class="btn" type="submit">حفظ</button>
    </form>
</div>
</body>
</html>
