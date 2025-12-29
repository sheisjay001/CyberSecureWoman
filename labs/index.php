<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// Fetch Labs
$conn = db();
$result = $conn->query("SELECT * FROM courses WHERE type = 'lab' ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hands-on Labs - CyberSecure Women</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Hands-on Labs</h1>
      </header>
      <p>Practice your skills in safe, simulated environments.</p>

      <section class="cards">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($lab = $result->fetch_assoc()): ?>
              <div class="card">
                  <?php if (!empty($lab['thumbnail_url'])): ?>
                      <img src="<?= sanitize($lab['thumbnail_url']) ?>" alt="Lab" style="max-width:100%; height:auto; border-radius:4px;">
                  <?php endif; ?>
                  <h3><?= sanitize($lab['title']) ?></h3>
                  <p><?= sanitize($lab['description']) ?></p>
                  <!-- If it's a local file (starts with /), link directly. If external, open new tab -->
                  <?php 
                      $is_external = strpos($lab['content_url'], 'http') === 0;
                      $target = $is_external ? '_blank' : '_self';
                  ?>
                  <a class="btn" href="<?= sanitize($lab['content_url']) ?>" target="<?= $target ?>">Launch Lab</a>
              </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No labs available yet.</p>
        <?php endif; ?>
        
        <!-- External Resources Cards -->
        <div class="card" style="border-left: 4px solid #666;">
          <h3>TryHackMe</h3>
          <p>External Sandbox Environments</p>
          <a class="btn btn-secondary" href="https://tryhackme.com" target="_blank" rel="noopener">Visit Site</a>
        </div>
        <div class="card" style="border-left: 4px solid #666;">
          <h3>OverTheWire</h3>
          <p>Wargames for learning Linux & Security</p>
          <a class="btn btn-secondary" href="https://overthewire.org" target="_blank" rel="noopener">Visit Site</a>
        </div>
      </section>
    </main>
  </div>
</body>
</html>

