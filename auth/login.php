<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';
  if ($email === '' || $password === '') {
    $error = 'Email and password are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email address.';
  } else {
    if (authenticate($email, $password)) {
      header('Location: /dashboard.php');
      exit;
    } else {
      $error = 'Invalid credentials.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>Login</h1>
      <nav>
        <a href="/index.php">Home</a>
        <a href="/auth/register.php">Register</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <?php if ($error): ?>
      <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>
    <form method="post" class="card">
      <label>
        <span>Email</span>
        <input type="email" name="email" required>
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" required>
      </label>
      <button class="btn" type="submit">Sign In</button>
    </form>
  </main>
</body>
</html>

