<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$message = '';
$completed = false;
$comment = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    
    // Simulate Reflected XSS
    // Check if input contains <script> tags
    if (preg_match("/<script\b[^>]*>(.*?)<\/script>/is", $comment) || preg_match("/javascript:/i", $comment) || preg_match("/on\w+=/i", $comment)) {
        $completed = true;
        
        if (award_badge($_SESSION['user_id'], 'Script Kiddie')) {
            $message = "🎉 Alert Popped! Badge 'Script Kiddie' awarded.";
        } else {
            $message = "🎉 XSS Vulnerability Found!";
        }

        // Record progress
        $conn = db();
        $res = $conn->query("SELECT id FROM courses WHERE title LIKE '%XSS%' LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $cid = $row['id'];
            $uid = $_SESSION['user_id'];
            $stmt = $conn->prepare("REPLACE INTO progress (user_id, course_id, completed, score, updated_at) VALUES (?, ?, 1, 100, NOW())");
            $stmt->bind_param("ii", $uid, $cid);
            $stmt->execute();
        }

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>XSS Lab - CyberSecure Women</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .lab-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .guestbook { background: #fff; color: #333; padding: 1rem; border-radius: 4px; margin-top: 1rem; border: 1px solid #ccc; }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Reflected XSS Lab</h1>
      </header>
      <div class="lab-container" style="padding: 0; margin: 0;">
        <div class="card">
            <h3>Objective</h3>
            <p>This guestbook reflects your input directly without sanitization. Try to inject JavaScript to pop up an alert box.</p>
            <p><em>Hint: <code>&lt;script&gt;alert(1)&lt;/script&gt;</code></em></p>
        </div>

        <form method="POST" class="card" style="margin-top: 1rem;">
            <label>
                <span>Leave a comment:</span>
                <input type="text" name="comment" placeholder="Hello world!" autocomplete="off">
            </label>
            <button type="submit" class="btn">Post Comment</button>
        </form>

        <?php if ($message): ?>
            <div class="alert alert-success" style="margin-top: 1rem;">
                <?= sanitize($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($comment): ?>
            <h3>Latest Comments:</h3>
            <div class="guestbook">
                <!-- VULNERABILITY: Outputting raw HTML (Simulation) -->
                <?php 
                    if ($completed) {
                        echo "<strong>User says:</strong> " . htmlspecialchars($comment) . " <br><br><em>(In a real vulnerable app, this would have executed!)</em>";
                        // Actually execute it for effect? No, let's keep it safe but visual.
                        echo "<script>alert('XSS Successful! (Simulated)');</script>";
                    } else {
                        echo "<strong>User says:</strong> " . htmlspecialchars($comment);
                    }
                ?>
            </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
