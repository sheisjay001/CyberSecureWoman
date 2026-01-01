<?php
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

if (!function_exists('env')) {
    function env($key, $default = null) {
        $v = getenv($key);
        if ($v === false || $v === '') return $default;
        return $v;
    }
}

function db() {
  static $conn = null;
  if ($conn) return $conn;
  
  // Enable exception reporting for mysqli
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  $host = env('DB_HOST', '127.0.0.1');
  $user = env('DB_USER', 'root');
  $pass = env('DB_PASS', '');
  $name = env('DB_NAME', 'cybersecure_women');
  $port = (int) env('DB_PORT', '3306');
  $ssl_ca = env('DB_SSL_CA', null);

  // Auto-detect SSL CA if not explicitly set but file exists in root
  if (empty($ssl_ca) && file_exists(__DIR__ . '/../isrgrootx1.pem')) {
      $ssl_ca = __DIR__ . '/../isrgrootx1.pem';
  } elseif (!empty($ssl_ca) && !file_exists($ssl_ca) && file_exists(__DIR__ . '/../' . $ssl_ca)) {
      // Handle relative path case from env var
      $ssl_ca = __DIR__ . '/../' . $ssl_ca;
  }

  try {
      $conn = mysqli_init();
      if ($ssl_ca) {
          $conn->ssl_set(NULL, NULL, $ssl_ca, NULL, NULL);
      }
      $conn->real_connect($host, $user, $pass, $name, $port, NULL, MYSQLI_CLIENT_SSL);
      $conn->set_charset('utf8mb4');
      return $conn;
  } catch (mysqli_sql_exception $e) {
      // 1049 is "Unknown database"
      if ($e->getCode() === 1049) {
          http_response_code(500);
          exit("<h1>Database Not Found</h1><p>Please run <code>php setup_data.php</code> to create the database.</p>");
      }
      http_response_code(500);
      exit('Database connection error: ' . $e->getMessage());
  }
}

function is_logged_in() {
  return isset($_SESSION['user_id']);
}

function require_login() {
  if (!is_logged_in()) {
    header('Location: /auth/login.php');
    exit;
  }
}

function sanitize($s) {
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function find_user_by_email($email) {
  $stmt = db()->prepare('SELECT id, name, email, password_hash, gender FROM users WHERE email = ? LIMIT 1');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc();
}

function create_user($name, $email, $password, $gender) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, gender, created_at) VALUES (?, ?, ?, ?, NOW())');
  $stmt->bind_param('ssss', $name, $email, $hash, $gender);
  return $stmt->execute();
}

function authenticate($email, $password) {
  $user = find_user_by_email($email);
  if (!$user) return false;
  if (!password_verify($password, $user['password_hash'])) return false;
  $_SESSION['user_id'] = (int) $user['id'];
  $_SESSION['user_name'] = $user['name'];
  $_SESSION['user_gender'] = $user['gender'];
  return true;
}

function logout() {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
  }
  session_destroy();
}

function award_badge($user_id, $badge_name) {
    $conn = db();
    // Get badge ID
    $stmt = $conn->prepare("SELECT id FROM badges WHERE name = ?");
    $stmt->bind_param("s", $badge_name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) return false;
    $badge = $res->fetch_assoc();
    $badge_id = $badge['id'];

    // Check if already awarded
    $stmt = $conn->prepare("SELECT 1 FROM user_badges WHERE user_id = ? AND badge_id = ?");
    $stmt->bind_param("ii", $user_id, $badge_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) return false;

    // Award
    $stmt = $conn->prepare("INSERT INTO user_badges (user_id, badge_id, awarded_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $badge_id);
    return $stmt->execute();
}

function send_email($to, $subject, $message) {
    // Basic mail function wrapper.
    // NOTE: For this to work on localhost (XAMPP), you must configure sendmail in php.ini and sendmail.ini
    // or use a tool like Mailhog/Papercut.
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: CyberSecure Women <no-reply@cybersecurewomen.local>' . "\r\n";
    
    // In a production environment, use PHPMailer or similar.
    return mail($to, $subject, $message, $headers);
}

function create_password_reset_token($email) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $conn = db();
    // Invalidate old tokens
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Create new token
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $email, $token, $expires);
    
    if ($stmt->execute()) {
        return $token;
    }
    return false;
}

function verify_reset_token($token) {
    $conn = db();
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['email'];
    }
    return false;
}

function reset_password($email, $new_password) {
    $conn = db();
    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update user password
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $hash, $email);
    
    if ($stmt->execute()) {
        // Delete used token
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return true;
    }
    return false;
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function flash($key, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
    } else {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
    }
    return null;
}

function get_user_badges($user_id) {
    $conn = db();
    $stmt = $conn->prepare("SELECT b.*, ub.awarded_at FROM badges b JOIN user_badges ub ON b.id = ub.badge_id WHERE ub.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function get_user_progress($user_id) {
    $conn = db();
    $stmt = $conn->prepare("SELECT c.title, c.type, p.completed, p.score FROM courses c JOIN progress p ON c.id = p.course_id WHERE p.user_id = ? ORDER BY p.updated_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
