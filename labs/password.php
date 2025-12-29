<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$message = '';
$completed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cracked'])) {
    $completed = true;
    if (award_badge($_SESSION['user_id'], 'Password Protector')) {
        $message = "Congratulations! You earned the 'Password Protector' badge!";
    } else {
        $message = "Lab completed successfully!";
    }

    // Record progress
    $conn = db();
    $res = $conn->query("SELECT id FROM courses WHERE title LIKE '%Password Strength%' LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $cid = $row['id'];
        $uid = $_SESSION['user_id'];
        $stmt = $conn->prepare("REPLACE INTO progress (user_id, course_id, completed, score, updated_at) VALUES (?, ?, 1, 100, NOW())");
        $stmt->bind_param("ii", $uid, $cid);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lab: Password Strength</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .lab-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .cracker-box { background: #333; color: #0f0; padding: 2rem; border-radius: 8px; font-family: monospace; margin-bottom: 2rem; }
    .input-group { margin-bottom: 1rem; }
    input[type="text"] { padding: 0.5rem; width: 100%; max-width: 300px; }
    #result { margin-top: 1rem; font-weight: bold; }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Password Strength Lab</h1>
      </header>
      <div class="lab-container" style="padding: 0; margin: 0;">
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= $message ?>
                <br><a href="/profile.php" class="btn btn-secondary">View Profile</a>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Objective</h3>
            <p>Type a password below to see how long it would take a basic brute-force attack to crack it. Try to find a password that takes more than <strong>1 million years</strong> to crack to pass this lab.</p>
        </div>

        <div class="cracker-box">
            <div class="input-group">
                <label>Test Password:</label><br>
                <input type="text" id="passwordInput" placeholder="Type here..." onkeyup="checkStrength()">
            </div>
            <div id="result">Time to crack: 0 seconds</div>
        </div>

        <form method="POST" id="completeForm" style="display:none;">
            <input type="hidden" name="cracked" value="1">
            <button type="submit" class="btn">Submit & Claim Badge</button>
        </form>

        <script>
            function checkStrength() {
                const pass = document.getElementById('passwordInput').value;
                if (!pass) {
                    document.getElementById('result').innerText = "Time to crack: 0 seconds";
                    return;
                }

                // Simplified entropy calculation
                let pool = 0;
                if (/[a-z]/.test(pass)) pool += 26;
                if (/[A-Z]/.test(pass)) pool += 26;
                if (/[0-9]/.test(pass)) pool += 10;
                if (/[^a-zA-Z0-9]/.test(pass)) pool += 32;

                const entropy = Math.log2(Math.pow(pool, pass.length));
                // Assume 1 billion guesses per second (fast GPU)
                const seconds = Math.pow(2, entropy) / 1e9;
                
                let timeString = "";
                if (seconds < 60) timeString = "Instantly!";
                else if (seconds < 3600) timeString = Math.round(seconds/60) + " minutes";
                else if (seconds < 86400) timeString = Math.round(seconds/3600) + " hours";
                else if (seconds < 31536000) timeString = Math.round(seconds/86400) + " days";
                else timeString = Math.round(seconds/31536000) + " years";

                document.getElementById('result').innerText = "Time to crack: " + timeString;
                document.getElementById('result').style.color = seconds > 31536000000000 ? '#0f0' : (seconds < 100 ? 'red' : 'yellow');

                // Pass condition: > 1 million years (approx 3e13 seconds)
                if (seconds > 3e13) { // 1,000,000 years
                    document.getElementById('completeForm').style.display = 'block';
                } else {
                    document.getElementById('completeForm').style.display = 'none';
                }
            }
        </script>
      </div>
    </main>
  </div>
</body>
</html>
