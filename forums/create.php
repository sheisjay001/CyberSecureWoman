<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid CSRF token.";
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($title) || empty($content)) {
            $error = 'Please fill in both title and content.';
        } else {
        $conn = db();
        // Start transaction
        $conn->begin_transaction();
        try {
            // Insert Thread
            $stmt = $conn->prepare("INSERT INTO forum_threads (user_id, title, created_at) VALUES (?, ?, NOW())");
            $userId = $_SESSION['user_id'];
            $stmt->bind_param("is", $userId, $title);
            $stmt->execute();
            $threadId = $conn->insert_id;

            // Insert First Post
            $stmt = $conn->prepare("INSERT INTO forum_posts (thread_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $threadId, $userId, $content);
            $stmt->execute();
            
            // Award Points for creating a thread
            add_points($userId, 10); // 10 points for a new thread

            $conn->commit();
            header("Location: /forums/view.php?id=$threadId");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Failed to create thread. Please try again.';
        }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Topic - CyberSecure Women</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>New Discussion</h1>
      </header>
      <div class="card" style="max-width: 800px; margin: 0 auto;">
          <?php if ($error): ?>
              <div style="background:#fee; color:#c00; padding:1rem; border-radius:4px; margin-bottom:1rem;">
                  <?= sanitize($error) ?>
              </div>
          <?php endif; ?>

          <form method="POST" action="">
              <?= csrf_field() ?>
              <div class="form-group">
                  <label for="title">Topic Title</label>
                  <input type="text" id="title" name="title" required placeholder="e.g., How do I get started with CTFs?">
              </div>
              <div class="form-group">
                  <label for="content">Message</label>
                  <textarea id="content" name="content" rows="10" required placeholder="Write your post here..."></textarea>
              </div>
              <button type="submit" class="btn">Post Thread</button>
          </form>
      </div>
    </main>
  </div>
</body>
</html>
