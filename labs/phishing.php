<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$message = '';
$completed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check answers
    $score = 0;
    if (($_POST['q1'] ?? '') === 'url') $score++;
    if (($_POST['q2'] ?? '') === 'https') $score++;
    if (($_POST['q3'] ?? '') === 'urgent') $score++;

    if ($score === 3) {
        $completed = true;
        // Award Badge
        if (award_badge($_SESSION['user_id'], 'Phishing Detective')) {
            $message = "Congratulations! You passed the lab and earned the 'Phishing Detective' badge!";
        } else {
            $message = "Good job! You passed the lab.";
        }
        
        // Record progress
        $conn = db();
        // Assume Course ID 5 is this lab (based on seed order, but better to lookup)
        // Dynamic lookup:
        $res = $conn->query("SELECT id FROM courses WHERE title LIKE '%Phishing Simulation%' LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $cid = $row['id'];
            $uid = $_SESSION['user_id'];
            $stmt = $conn->prepare("REPLACE INTO progress (user_id, course_id, completed, score, updated_at) VALUES (?, ?, 1, 100, NOW())");
            $stmt->bind_param("ii", $uid, $cid);
            $stmt->execute();
        }

    } else {
        $message = "You got $score/3 correct. Try again!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Phishing Detection Lab - CyberSecure Women</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .lab-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .fake-email { border: 1px solid #ddd; padding: 1rem; border-radius: 4px; background: #fff; margin-bottom: 2rem; }
    .fake-header { border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem; font-size: 0.9rem; color: #555; }
    .fake-body { font-family: sans-serif; }
    .question { margin-bottom: 1.5rem; }
    .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; }
    .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Phishing Lab</h1>
      </header>
      <div class="lab-container" style="padding: 0; margin: 0;">
        <?php if ($message): ?>
            <div class="<?= $completed ? 'success' : 'error' ?>">
                <?= $message ?>
                <?php if ($completed): ?>
                    <br><a href="/profile.php" class="btn btn-secondary">View Profile</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$completed): ?>
            <h2>Scenario: Analyze the Suspicious Email</h2>
            <div class="fake-email">
                <div class="fake-header">
                    <strong>From:</strong> support@faceboook-security-alert.com <span style="color:red">(Look closely!)</span><br>
                    <strong>Subject:</strong> URGENT: Your account will be deleted in 24 hours!!
                </div>
                <div class="fake-body">
                    <p>Dear User,</p>
                    <p>We have detected suspicious activity. Please verify your identity immediately or your account will be permanently closed.</p>
                    <p><a href="#" style="color:blue; text-decoration:underline;">http://verify-facebook-login.com.evil-site.net/login</a></p>
                    <p>Thanks,<br>Security Team</p>
                </div>
            </div>

            <form method="POST">
                <div class="question">
                    <p><strong>1. What is the most obvious indicator in the "From" address?</strong></p>
                    <label><input type="radio" name="q1" value="valid"> It looks valid.</label><br>
                    <label><input type="radio" name="q1" value="url"> Misspelled domain (faceboook) or unofficial domain.</label><br>
                    <label><input type="radio" name="q1" value="none"> Nothing is wrong.</label>
                </div>

                <div class="question">
                    <p><strong>2. Inspect the link. What is suspicious?</strong></p>
                    <label><input type="radio" name="q2" value="https"> It uses HTTP instead of HTTPS and redirects to a different domain.</label><br>
                    <label><input type="radio" name="q2" value="ok"> It says "facebook" so it is safe.</label>
                </div>

                <div class="question">
                    <p><strong>3. What psychological trigger is used?</strong></p>
                    <label><input type="radio" name="q3" value="greed"> Greed (you won money).</label><br>
                    <label><input type="radio" name="q3" value="urgent"> Urgency/Fear (account deletion).</label>
                </div>

                <button type="submit" class="btn">Submit Analysis</button>
            </form>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
