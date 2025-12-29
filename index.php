<?php
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

switch ($request_uri) {
    case '/':
    case '/index.php':
        require __DIR__ . '/home.php';
        break;
    case '/dashboard.php':
        require __DIR__ . '/dashboard.php';
        break;
    case '/profile.php':
        require __DIR__ . '/profile.php';
        break;
    case '/auth/login.php':
        require __DIR__ . '/auth/login.php';
        break;
    case '/auth/register.php':
        require __DIR__ . '/auth/register.php';
        break;
    case '/auth/forgot-password.php':
        require __DIR__ . '/auth/forgot-password.php';
        break;
    case '/auth/reset-password.php':
        require __DIR__ . '/auth/reset-password.php';
        break;
    case '/auth/logout.php':
        require __DIR__ . '/auth/logout.php';
        break;
    case '/courses/index.php':
        require __DIR__ . '/courses/index.php';
        break;
    case '/courses/view.php':
        require __DIR__ . '/courses/view.php';
        break;
    case '/labs/index.php':
        require __DIR__ . '/labs/index.php';
        break;
    case '/labs/phishing.php':
        require __DIR__ . '/labs/phishing.php';
        break;
    case '/labs/password.php':
        require __DIR__ . '/labs/password.php';
        break;
    case '/forums/index.php':
        require __DIR__ . '/forums/index.php';
        break;
    case '/forums/create.php':
        require __DIR__ . '/forums/create.php';
        break;
    case '/forums/view.php':
        require __DIR__ . '/forums/view.php';
        break;
    default:
        // Serve static assets if they exist (Fallback for Vercel/Local)
        $file_path = __DIR__ . $request_uri;
        if (file_exists($file_path) && !is_dir($file_path)) {
            $ext = pathinfo($file_path, PATHINFO_EXTENSION);
            $mimes = [
                'css' => 'text/css',
                'js'  => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg'=> 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon'
            ];
            $mime = $mimes[$ext] ?? 'text/plain';
            header("Content-Type: $mime");
            readfile($file_path);
            exit;
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
        break;
}

