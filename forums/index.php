<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
require_login(); // Private community

$conn = db();
$sql = "SELECT t.id, t.title, t.created_at, u.name as author_name, 
        (SELECT COUNT(*) FROM forum_posts p WHERE p.thread_id = t.id) as reply_count 
        FROM forum_threads t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forum - CyberSecure Women</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .forum-list { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .forum-list th, .forum-list td { text-align: left; padding: 1rem; border-bottom: 1px solid #ddd; }
    .forum-list tr:hover { background-color: #f9f9f9; }
    .thread-title { font-weight: bold; color: var(--primary-color); text-decoration: none; font-size: 1.1rem; }
    .thread-meta { font-size: 0.9rem; color: #666; }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Community Forum</h1>
      </header>
      <div style="display: flex; justify-content: space-between; align-items: center;">
          <h2>Discussions</h2>
          <a href="/forums/create.php" class="btn">Start New Thread</a>
      </div>

      <table class="forum-list">
        <thead>
          <tr>
            <th>Topic</th>
            <th>Replies</th>
            <th>Last Activity</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <a href="/forums/view.php?id=<?= $row['id'] ?>" class="thread-title"><?= sanitize($row['title']) ?></a>
                  <div class="thread-meta">By <?= sanitize($row['author_name']) ?></div>
                </td>
                <td><?= $row['reply_count'] ?></td>
                <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" style="text-align: center; padding: 2rem;">No discussions yet. Be the first to start one!</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>

