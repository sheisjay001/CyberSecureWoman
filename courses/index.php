<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// Fetch courses from DB
$conn = db();
$result = $conn->query("SELECT * FROM courses ORDER BY created_at ASC");
$courses = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutorials - CyberSecure Women</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Tutorials</h1>
      </header>
      <p>Explore our library of cybersecurity tutorials designed for you.</p>
      
      <section class="cards">
        <?php if (empty($courses)): ?>
          <p>No tutorials available yet.</p>
        <?php else: ?>
          <?php foreach ($courses as $course): ?>
            <div class="card">
              <?php if (!empty($course['thumbnail_url'])): ?>
                  <img src="<?= sanitize($course['thumbnail_url']) ?>" alt="Thumbnail" style="max-width:100%; height:auto; border-radius:4px;">
              <?php endif; ?>
              <h3><?= sanitize($course['title']) ?></h3>
              <p><small><?= ucfirst(sanitize($course['type'])) ?></small></p>
              <p><?= sanitize($course['description']) ?></p>
              <a class="btn btn-secondary" href="/courses/view.php?id=<?= $course['id'] ?>">
                  <?= $course['type'] === 'video' ? 'Watch Video' : 'Read Article' ?>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>

