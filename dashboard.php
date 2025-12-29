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
  <title>Dashboard</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>Dashboard</h1>
      <nav>
        <a href="/index.php">Home</a>
        <a href="/courses/index.php">Tutorials</a>
        <a href="/labs/index.php">Labs</a>
        <a href="/forums/index.php">Forum</a>
        <a href="/auth/logout.php">Logout</a>
      </nav>
    </div>
  </header>
  <main class="container">
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
</body>
</html>

