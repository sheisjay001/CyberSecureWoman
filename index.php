<?php
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

switch ($request_uri) {
    case '/':
    case '/index.php':
        require __DIR__ . '/dashboard.php';
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
        // Serve static assets or 404
        if (file_exists(__DIR__ . $request_uri) && !is_dir(__DIR__ . $request_uri)) {
             // Let the server handle static files (handled by vercel.json routes usually, but for PHP router)
             // In Vercel, static files are served before this router if matched.
             // If we are here, it's likely a 404 or a dynamic route miss.
             http_response_code(404);
             echo "404 Not Found";
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
        break;
}

