<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CyberSecure Women</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>CyberSecure Women</h1>
      <nav>
        <a href="index.php">Home</a>
        <?php if (is_logged_in()): ?>
          <a href="dashboard.php">Dashboard</a>
          <a href="auth/logout.php">Logout</a>
        <?php else: ?>
          <a href="auth/login.php">Login</a>
          <a href="auth/register.php">Register</a>
        <?php endif; ?>
        <a href="courses/index.php">Tutorials</a>
        <a href="labs/index.php">Labs</a>
        <a href="forums/index.php">Forum</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <section class="hero">
      <h2>Learn cybersecurity in a women-only community</h2>
      <p>Video lessons, hands-on labs, sandbox integrations, and a supportive forum.</p>
      <?php if (!is_logged_in()): ?>
        <div class="cta">
          <a class="btn" href="auth/register.php">Join Now</a>
          <a class="btn btn-secondary" href="auth/login.php">Sign In</a>
        </div>
      <?php else: ?>
        <div class="cta">
          <a class="btn" href="dashboard.php">Go to Dashboard</a>
        </div>
      <?php endif; ?>
    </section>
    <section class="features">
      <div class="feature">
        <h3>Women-only access</h3>
        <p>Registration is restricted to women to build a safe learning space.</p>
      </div>
      <div class="feature">
        <h3>Courses</h3>
        <p>Video tutorials and article-based lessons to build practical skills.</p>
      </div>
      <div class="feature">
        <h3>Labs</h3>
        <p>Practice with password cracking and phishing simulations.</p>
      </div>
      <div class="feature">
        <h3>Sandbox</h3>
        <p>Links to TryHackMe and OverTheWire for real-world practice.</p>
      </div>
      <div class="feature">
        <h3>Leaderboards</h3>
        <p>Earn points and badges as you progress through the platform.</p>
      </div>
      <div class="feature">
        <h3>Forums</h3>
        <p>Discuss topics, ask questions, and support other learners.</p>
      </div>
    </section>
  </main>
  <script src="assets/js/app.js"></script>
</body>
</html>

