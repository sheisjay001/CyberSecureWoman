<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_login();
$user_name = $_SESSION['user_name'] ?? 'Learner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - CyberSecure Women</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="layout-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main-content">
      <header style="margin-bottom: 20px;">
        <h1>Dashboard</h1>
      </header>
      <section>
        <h2>Welcome, <?= sanitize($user_name) ?></h2>
        <p>Track progress, earn badges, and climb the leaderboard.</p>
      </section>
      <section class="cards">
        <div class="card">
          <h3>Your Progress</h3>
          <p>No progress yet.</p>
        </div>
        <div class="card">
          <h3>Badges</h3>
          <p>No badges yet.</p>
        </div>
        <div class="card">
          <h3>Leaderboard</h3>
          <p>Coming soon.</p>
        </div>
      </section>
    </main>
  </div>
</body>
</html>

