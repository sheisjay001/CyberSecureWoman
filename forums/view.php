<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$conn = db();

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_content'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }
    
    $content = trim($_POST['reply_content']);
    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO forum_posts (thread_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $userId = $_SESSION['user_id'];
        $stmt->bind_param("iis", $id, $userId, $content);
        $stmt->execute();
        
        // Award Points for replying
        add_points($userId, 5); // 5 points for a reply
        
        // Refresh to show new post
        header("Location: /forums/view.php?id=$id");
        exit;
    }
}

// Fetch Thread Info
$stmt = $conn->prepare("SELECT t.*, u.name as author_name FROM forum_threads t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$thread = $stmt->get_result()->fetch_assoc();

if (!$thread) {
    die("Thread not found.");
}

// Fetch Posts
$stmt = $conn->prepare("SELECT p.*, u.name as author_name, u.gender FROM forum_posts p JOIN users u ON p.user_id = u.id WHERE p.thread_id = ? ORDER BY p.created_at ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$posts = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= sanitize($thread['title']) ?> - CyberSecure Women</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .post { background: #fff; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #eee; }
    .post-meta { color: #666; font-size: 0.9rem; margin-bottom: 0.5rem; border-bottom: 1px solid #f0f0f0; padding-bottom: 0.5rem; }
    .post-author { font-weight: bold; color: var(--primary-color); }
    .reply-box { background: #f9f9f9; padding: 1.5rem; border-radius: 8px; margin-top: 2rem; }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Forum</h1>
      </header>
      <h2><?= sanitize($thread['title']) ?></h2>
      
      <?php while ($post = $posts->fetch_assoc()): ?>
          <div class="post">
              <div class="post-meta">
                  <span class="post-author"><?= sanitize($post['author_name']) ?></span> 
                  • <?= date('M j, Y g:i a', strtotime($post['created_at'])) ?>
              </div>
              <div class="post-content">
                  <?= nl2br(sanitize($post['content'])) ?>
              </div>
          </div>
      <?php endwhile; ?>

      <div class="reply-box">
          <h3>Post a Reply</h3>
          <form method="POST" action="">
              <?= csrf_field() ?>
              <div class="form-group">
                  <textarea name="reply_content" rows="5" required placeholder="Type your reply here..." style="width: 100%; padding: 0.5rem;"></textarea>
              </div>
              <button type="submit" class="btn">Submit Reply</button>
          </form>
      </div>
    </main>
  </div>
</body>
</html>
