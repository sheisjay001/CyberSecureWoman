<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_login();
$user_name = $_SESSION['user_name'] ?? 'Learner';

// Fetch User Stats
$conn = db();
$user_id = $_SESSION['user_id'];

// Get Points and Streak
$stmt = $conn->prepare("SELECT points, current_streak FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_stats = $stmt->get_result()->fetch_assoc();

// Get Rank (simple count of users with more points)
$stmt = $conn->prepare("SELECT COUNT(*) as rank_count FROM users WHERE points > ?");
$stmt->bind_param("i", $user_stats['points']);
$stmt->execute();
$rank_data = $stmt->get_result()->fetch_assoc();
$global_rank = $rank_data['rank_count'] + 1;

// Get Courses Completed (Placeholder for now, assumes 0 if not tracked yet)
$courses_completed = 0; 

// Get Leaderboard
$leaderboard = get_leaderboard(5);

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
      <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h1 style="font-size: 2rem; margin-bottom: 5px;">Welcome back, <?= sanitize($user_name) ?>!</h1>
          <p style="color: var(--muted); margin: 0;">Here's what's happening with your learning journey.</p>
        </div>
        <a href="/courses/index.php" class="btn"><i class="fas fa-play"></i> Resume Learning</a>
      </header>
      
      <!-- Stats Row -->
      <section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
          <h3>Courses Completed</h3>
          <div class="value"><?= $courses_completed ?></div>
          <i class="fas fa-check-circle icon"></i>
        </div>
        <div class="stat-card">
          <h3>Points Earned</h3>
          <div class="value"><?= number_format($user_stats['points']) ?></div>
          <i class="fas fa-medal icon"></i>
        </div>
        <div class="stat-card">
          <h3>Current Streak</h3>
          <div class="value"><?= $user_stats['current_streak'] ?> <span style="font-size: 1rem; color: var(--muted); font-weight: normal;">day<?= $user_stats['current_streak'] == 1 ? '' : 's' ?></span></div>
          <i class="fas fa-fire icon"></i>
        </div>
        <div class="stat-card">
          <h3>Global Rank</h3>
          <div class="value">#<?= $global_rank ?></div>
          <i class="fas fa-trophy icon"></i>
        </div>
      </section>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Left Column -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
          <section class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
              <h2 style="margin: 0; font-size: 1.2rem;">Recent Activity</h2>
              <a href="#" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">View All</a>
            </div>
            <div style="color: var(--muted); padding: 20px; text-align: center; border: 1px dashed #2b2f54; border-radius: 8px;">
              <i class="fas fa-history" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
              No recent activity. Start a course to see it here!
            </div>
          </section>
          
          <section class="card">
            <h2 style="margin: 0 0 15px 0; font-size: 1.2rem;">Leaderboard</h2>
            <div style="display: grid; gap: 10px;">
              <?php 
              $rank = 1;
              while($row = $leaderboard->fetch_assoc()): 
                $is_me = ($row['name'] === $user_name);
              ?>
              <div style="display: flex; justify-content: space-between; padding: 10px; background: <?= $is_me ? 'rgba(156, 39, 176, 0.2)' : 'rgba(255,255,255,0.03)' ?>; border-radius: 8px; border: <?= $is_me ? '1px solid var(--primary)' : 'none' ?>;">
                <div style="display: flex; gap: 10px; align-items: center;">
                  <span style="font-weight: bold; width: 20px; color: var(--muted);">#<?= $rank++ ?></span>
                  <span><?= sanitize($row['name']) ?></span>
                </div>
                <div style="font-weight: bold; color: var(--success);"><?= number_format($row['points']) ?> pts</div>
              </div>
              <?php endwhile; ?>
            </div>
          </section>
        </div>
        
        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
           <section class="card">
            <h2 style="margin: 0 0 15px 0; font-size: 1.2rem;">Community Spotlight</h2>
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 15px;">Join the discussion in our forums.</p>
            <a href="/forums/index.php" class="btn-secondary" style="width: 100%; text-align: center; display: block; padding: 10px; border-radius: 8px; background: rgba(255,255,255,0.05); color: var(--text);">
              <i class="fas fa-comments"></i> Visit Forum
            </a>
          </section>
          
          <section class="card" style="background: linear-gradient(180deg, var(--card) 0%, rgba(156, 39, 176, 0.1) 100%);">
            <h2 style="margin: 0 0 10px 0; font-size: 1.2rem;">Pro Tip</h2>
            <p style="font-size: 0.9rem; line-height: 1.5; color: var(--muted);">
              "Enable 2FA on all your accounts. It's the single most effective way to prevent unauthorized access."
            </p>
          </section>
        </div>
      </div>
    </main>
  </div>
</body>
</html>

