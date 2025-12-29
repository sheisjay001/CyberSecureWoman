<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// Ensure user is logged in to view content
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$conn = db();
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    http_response_code(404);
    die("Course not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= sanitize($course['title']) ?> - CyberSecure Women</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .course-content {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 2rem;
    }
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        height: 0;
        margin-bottom: 1.5rem;
    }
    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 4px;
    }
    .back-link {
        display: inline-block;
        margin-bottom: 1rem;
        color: var(--primary-color);
        text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Tutorials</h1>
      </header>
      <a href="/courses/index.php" class="back-link">&larr; Back to Tutorials</a>
      
      <article class="course-content">
          <h2><?= sanitize($course['title']) ?></h2>
          <p class="meta">Type: <?= ucfirst(sanitize($course['type'])) ?></p>
          
          <hr>

          <?php if ($course['type'] === 'video' && !empty($course['content_url'])): ?>
              <div class="video-wrapper">
                  <iframe src="<?= sanitize($course['content_url']) ?>" allowfullscreen></iframe>
              </div>
              <?php if (!empty($course['content_body'])): ?>
                  <div class="description">
                      <?= $course['content_body'] // Allow HTML for description/body ?>
                  </div>
              <?php endif; ?>
          <?php else: ?>
              <div class="article-body">
                  <?= $course['content_body'] // Allow HTML for article content ?>
              </div>
          <?php endif; ?>

          <div style="margin-top: 2rem;">
              <button class="btn" onclick="alert('Progress tracking coming soon!')">Mark as Complete</button>
          </div>
      </article>
    </main>
  </div>
</body>
</html>
