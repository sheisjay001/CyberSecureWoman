<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$message = '';
$completed = false;
$simulated_users = [
    ['id' => 1, 'username' => 'admin', 'email' => 'admin@cyberwomen.local', 'password' => 'flag{sqli_master_2024}'],
    ['id' => 2, 'username' => 'jane_doe', 'email' => 'jane@cyberwomen.local', 'password' => 'secret123'],
    ['id' => 3, 'username' => 'alice', 'email' => 'alice@cyberwomen.local', 'password' => 'wonderland']
];

$output = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['userid'] ?? '';
    
    // Simulate SQL Injection vulnerability
    // If input contains basic SQLi patterns like ' OR '1'='1
    if (preg_match("/'\s*OR\s*'1'\s*=\s*'1/i", $input) || preg_match("/'\s*OR\s*1\s*=\s*1/i", $input)) {
        $completed = true;
        $output = $simulated_users;
        
        if (award_badge($_SESSION['user_id'], 'SQL Explorer')) {
            $message = "🎉 You hacked the database! Badge 'SQL Explorer' awarded.";
        } else {
            $message = "🎉 Database dumped successfully!";
        }

        // Record progress
        $conn = db();
        $res = $conn->query("SELECT id FROM courses WHERE title LIKE '%SQL Injection%' LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $cid = $row['id'];
            $uid = $_SESSION['user_id'];
            $stmt = $conn->prepare("REPLACE INTO progress (user_id, course_id, completed, score, updated_at) VALUES (?, ?, 1, 100, NOW())");
            $stmt->bind_param("ii", $uid, $cid);
            $stmt->execute();
        }

    } elseif (is_numeric($input)) {
        // Normal behavior
        foreach ($simulated_users as $u) {
            if ($u['id'] == $input) {
                $output[] = $u;
                break;
            }
        }
        if (empty($output)) $message = "User ID not found.";
    } else {
        $message = "Invalid input (Simulated Error: SQL Syntax Error near '$input')";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lab: SQL Injection</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .lab-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
    .terminal { background: #000; color: #0f0; padding: 1rem; border-radius: 4px; font-family: monospace; margin-top: 1rem; }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>SQL Injection Lab</h1>
      </header>
      <div class="lab-container" style="padding: 0; margin: 0;">
        <div class="card">
            <h3>Objective</h3>
            <p>This search box is vulnerable to SQL Injection. Try to dump all users from the database.</p>
            <p><em>Hint: The query looks like: <code>SELECT * FROM users WHERE id = '$input'</code></em></p>
            <p><em>Try to make the WHERE clause always true.</em></p>
        </div>

        <form method="POST" class="card" style="margin-top: 1rem;">
            <label>
                <span>User ID:</span>
                <input type="text" name="userid" placeholder="e.g., 1" autocomplete="off">
            </label>
            <button type="submit" class="btn">Search</button>
        </form>

        <?php if ($message): ?>
            <div class="alert <?= $completed ? 'alert-success' : 'alert-error' ?>" style="margin-top: 1rem;">
                <?= sanitize($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($output)): ?>
            <div class="terminal">
                <?php foreach ($output as $row): ?>
                    ID: <?= $row['id'] ?> | User: <?= $row['username'] ?> | Email: <?= $row['email'] ?> | Pass: <?= $row['password'] ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
