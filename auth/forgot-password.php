<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    
    if ($email === '') {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        // Check if user exists (security note: usually we shouldn't reveal this, but for this app it might be fine)
        // For security best practices, we usually say "If an account exists..."
        $user = find_user_by_email($email);
        
        if ($user) {
            $token = create_password_reset_token($email);
            if ($token) {
                // Construct reset link
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'];
                $link = $protocol . $host . "/auth/reset-password.php?token=" . $token;
                
                $message = "
                <html>
                <head>
                    <title>Password Reset</title>
                </head>
                <body>
                    <p>Hello " . sanitize($user['name']) . ",</p>
                    <p>We received a request to reset your password.</p>
                    <p>Click the link below to reset it:</p>
                    <p><a href='" . $link . "'>" . $link . "</a></p>
                    <p>This link expires in 1 hour.</p>
                    <p>If you did not request this, please ignore this email.</p>
                </body>
                </html>
                ";
                
                if (send_email($email, "Reset Your Password - CyberSecure Women", $message)) {
                     $success = 'Password reset link has been sent to your email. Please check your inbox (and spam folder).';
                } else {
                     $error = 'Failed to send email. Please ensure your mail server is configured.';
                     // For local dev, maybe show link? No, that's insecure even for dev unless explicitly asked.
                     // But user said "let users receive reset link in their mail box", so we try to send.
                }
            } else {
                $error = 'Could not generate reset token. Please try again.';
            }
        } else {
            // Generic message
            $success = 'If an account exists with this email, you will receive a reset link.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - CyberSecure Women</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
      .back-link {
          display: block;
          text-align: center;
          margin-top: 15px;
          color: var(--text);
          text-decoration: none;
          font-size: 0.9rem;
      }
      .back-link:hover {
          color: var(--primary);
      }
  </style>
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>Forgot Password</h1>
      <nav>
        <a href="/index.php">Home</a>
        <a href="/auth/login.php">Login</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <?php if ($error): ?>
      <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert alert-success"><?= sanitize($success) ?></div>
    <?php else: ?>
        <form method="post" class="card">
            <p style="margin-bottom: 20px; color: var(--muted); text-align: center;">
                Enter your email address and we'll send you a link to reset your password.
            </p>
            <label>
                <span>Email Address</span>
                <input type="email" name="email" required placeholder="name@example.com">
            </label>
            <button class="btn" type="submit" style="width: 100%;">Send Reset Link</button>
            
            <a href="/auth/login.php" class="back-link">Back to Login</a>
        </form>
    <?php endif; ?>
  </main>
</body>
</html>
