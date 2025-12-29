<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$email = '';

// Verify token
if ($token) {
    $email = verify_reset_token($token);
    if (!$email) {
        $error = 'Invalid or expired password reset link.';
    }
} else {
    $error = 'Missing reset token.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $email && !$error) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        if (reset_password($email, $password)) {
            $success = 'Your password has been reset successfully. You can now login.';
            // Clear token from session or URL params visually handled by redirect
            header('Refresh: 3; url=/auth/login.php');
        } else {
            $error = 'Failed to reset password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - CyberSecure Women</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>Reset Password</h1>
      <nav>
        <a href="/index.php">Home</a>
        <a href="/auth/login.php">Login</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <?php if ($error): ?>
      <div class="alert alert-error"><?= sanitize($error) ?></div>
      <?php if (strpos($error, 'Invalid') !== false): ?>
          <p style="text-align: center;"><a href="/auth/forgot-password.php" class="btn">Request New Link</a></p>
      <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert alert-success"><?= sanitize($success) ?></div>
      <p style="text-align: center;">Redirecting to login in 3 seconds...</p>
    <?php elseif ($email && !$error): ?>
        <form method="post" class="card">
            <label>
                <span>New Password</span>
                <input type="password" name="password" required minlength="6">
            </label>
            <label>
                <span>Confirm New Password</span>
                <input type="password" name="confirm_password" required minlength="6">
            </label>
            <button class="btn" type="submit" style="width: 100%;">Reset Password</button>
        </form>
    <?php endif; ?>
  </main>
</body>
</html>
