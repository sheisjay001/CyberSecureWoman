<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_gender = $_SESSION['user_gender'];

// Award "Cyber Initiate" badge just for visiting profile (or logging in)
if (award_badge($user_id, 'Cyber Initiate')) {
    $message = "You earned the 'Cyber Initiate' badge!";
}

$badges = get_user_badges($user_id);
$progress = get_user_progress($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - CyberSecure Women</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .profile-header { background: #fff; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .badge-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .badge-card { background: #f9f9f9; padding: 1rem; border-radius: 8px; text-align: center; border: 1px solid #eee; }
    .badge-card img { width: 50px; height: 50px; margin-bottom: 0.5rem; border-radius: 50%; }
    .badge-name { font-size: 0.8rem; font-weight: bold; }
    .progress-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .progress-table th, .progress-table td { padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left; }
  </style>
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>My Profile</h1>
      <nav>
        <a href="/index.php">Home</a>
        <a href="/dashboard.php">Dashboard</a>
        <a href="/auth/logout.php">Logout</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <?php if (isset($message)): ?>
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="profile-header">
        <h2><?= sanitize($user_name) ?></h2>
        <p>Email: <?= sanitize(find_user_by_email($_SESSION['user_name'] ?? '')['email'] ?? 'Hidden') ?></p>
        <p>Status: Member since <?= date('Y') ?></p>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Earned Badges</h3>
            <div class="badge-grid">
                <?php if ($badges->num_rows > 0): ?>
                    <?php while ($b = $badges->fetch_assoc()): ?>
                        <div class="badge-card" title="<?= sanitize($b['description']) ?>">
                            <img src="<?= sanitize($b['icon_url']) ?>" alt="Badge">
                            <div class="badge-name"><?= sanitize($b['name']) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No badges yet. Start learning to earn them!</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>Learning Progress</h3>
            <table class="progress-table">
                <thead>
                    <tr>
                        <th>Course/Lab</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($progress->num_rows > 0): ?>
                        <?php while ($p = $progress->fetch_assoc()): ?>
                            <tr>
                                <td><?= sanitize($p['title']) ?></td>
                                <td><?= ucfirst(sanitize($p['type'])) ?></td>
                                <td><?= $p['completed'] ? '<span style="color:green">Completed</span>' : 'In Progress' ?></td>
                                <td><?= $p['score'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No activity recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </main>
</body>
</html>
