<?php
define('APP_NAME', 'CyberSecure Women');
define('BASE_URL', '/');
function env($key, $default = null) {
  $v = getenv($key);
  if ($v === false || $v === '') return $default;
  return $v;
}

// Rename this file to config.php and fill in your details
putenv('DB_HOST=127.0.0.1');
putenv('DB_USER=root');
putenv('DB_PASS=');
putenv('DB_NAME=cybersecure_women');
putenv('DB_PORT=3306');
// putenv('DB_SSL_CA=' . __DIR__ . '/../isrgrootx1.pem'); // Uncomment if using SSL
