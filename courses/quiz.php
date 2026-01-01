<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$conn = db();

// Fetch Quiz
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ? LIMIT 1");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    // If no quiz exists, redirect back or show error
    flash('error', 'No quiz available for this course.', 'danger');
    header("Location: /courses/view.php?id=$course_id");
    exit;
}

// Fetch Questions
$stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz['id']);
$stmt->execute();
$questions_result = $stmt->get_result();
$questions = [];
while ($q = $questions_result->fetch_assoc()) {
    // Fetch Options for each question
    $opt_stmt = $conn->prepare("SELECT id, option_text FROM quiz_options WHERE question_id = ?");
    $opt_stmt->bind_param("i", $q['id']);
    $opt_stmt->execute();
    $q['options'] = $opt_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $questions[] = $q;
}

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $score = 0;
    $total_questions = count($questions);
    
    foreach ($questions as $q) {
        $user_answer = $_POST['q_' . $q['id']] ?? null;
        if ($user_answer) {
            // Check if correct
            $check = $conn->prepare("SELECT is_correct FROM quiz_options WHERE id = ? AND question_id = ?");
            $check->bind_param("ii", $user_answer, $q['id']);
            $check->execute();
            $res = $check->get_result()->fetch_assoc();
            if ($res && $res['is_correct']) {
                $score++;
            }
        }
    }

    $passed = ($score / $total_questions) >= 0.7; // 70% to pass
    
    // Record Attempt
    $stmt = $conn->prepare("INSERT INTO user_quiz_attempts (user_id, quiz_id, score, passed, completed_at) VALUES (?, ?, ?, ?, NOW())");
    $uid = $_SESSION['user_id'];
    $qid = $quiz['id'];
    $passed_int = $passed ? 1 : 0;
    $stmt->bind_param("iiii", $uid, $qid, $score, $passed_int);
    $stmt->execute();

    if ($passed) {
        // Award Points (e.g., 50 points for passing)
        // Check if already passed? For now, just award every time or maybe limit it.
        // Let's award only if it's the first time passing to prevent farming.
        $check_pass = $conn->prepare("SELECT COUNT(*) as c FROM user_quiz_attempts WHERE user_id = ? AND quiz_id = ? AND passed = 1 AND id != ?");
        $last_id = $conn->insert_id;
        $check_pass->bind_param("iii", $uid, $qid, $last_id);
        $check_pass->execute();
        $already_passed = $check_pass->get_result()->fetch_assoc()['c'] > 0;
        
        if (!$already_passed) {
            add_points($uid, 50);
            flash('success', "Quiz passed! You earned 50 points.", 'success');
        } else {
            flash('success', "Quiz passed again! Good job keeping your skills sharp.", 'success');
        }
    } else {
        flash('error', "You scored $score/$total_questions. Try again!", 'warning');
    }

    header("Location: /courses/view.php?id=$course_id");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= sanitize($quiz['title']) ?> - Quiz</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <h1><?= sanitize($quiz['title']) ?></h1>
      
      <form method="POST" class="card">
        <?= csrf_field() ?>
        
        <?php foreach ($questions as $index => $q): ?>
            <div style="margin-bottom: 20px;">
                <p style="font-weight: bold;"><?= ($index + 1) . '. ' . sanitize($q['question_text']) ?></p>
                <?php foreach ($q['options'] as $opt): ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $opt['id'] ?>" required>
                        <?= sanitize($opt['option_text']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn">Submit Quiz</button>
      </form>
    </main>
  </div>
</body>
</html>
