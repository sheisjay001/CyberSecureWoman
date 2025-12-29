<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirm'] ?? '';
  $gender = strtolower(trim($_POST['gender'] ?? ''));
  if ($gender !== 'female' && $gender !== 'woman') {
    $error = 'Registration is restricted to women.';
  } elseif ($name === '' || $email === '' || $password === '' || $confirm === '') {
    $error = 'All fields are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email address.';
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
  } elseif (find_user_by_email($email)) {
    $error = 'An account with this email already exists.';
  } else {
    $gender_val = 'female';
    if (create_user($name, $email, $password, $gender_val)) {
      $success = 'Registration successful. You can now log in.';
    } else {
      $error = 'Registration failed.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>Register</h1>
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
    <?php endif; ?>
    <form method="post" class="card">
      <label>
        <span>Name</span>
        <input type="text" name="name" required>
      </label>
      <label>
        <span>Email</span>
        <input type="email" name="email" required>
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" required minlength="6">
      </label>
      <label>
        <span>Confirm Password</span>
        <input type="password" name="confirm" required minlength="6">
      </label>
      <label>
        <span>Confirm you are a woman</span>
        <select name="gender" required>
          <option value="">Select</option>
          <option value="female">I am a woman</option>
        </select>
      </label>
      <button class="btn" type="submit">Create Account</button>
    </form>
  </main>
</body>
</html>

