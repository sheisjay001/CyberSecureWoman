<?php
// Load config manually or just define constants if needed, 
// but we need a custom connection that doesn't fail if DB is missing.
require_once __DIR__ . '/includes/config.php';

$host = env('DB_HOST', '127.0.0.1');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');
$port = (int) env('DB_PORT', 3306);
$dbname = env('DB_NAME', 'cybersecure_women');
$ssl_ca = env('DB_SSL_CA', null);

// 1. Connect without DB (but TiDB requires DB name usually, 'test' is default safe)
$conn = mysqli_init();
if ($ssl_ca) {
    $conn->ssl_set(NULL, NULL, $ssl_ca, NULL, NULL);
}

try {
   // Some cloud providers require DB name in connection string
   $conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
 
 // 2. Create DB (TiDB Cloud usually pre-creates the DB or restricts CREATE DATABASE, so we skip or use 'test')
 // echo "Creating database if not exists...\n";
 // $conn->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
 $conn->select_db($dbname);

// 3. Import Schema (Naive approach: split by semicolon)
echo "Importing schema...\n";
$schemaSql = file_get_contents(__DIR__ . '/database/schema.sql');
$queries = explode(';', $schemaSql);
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        $conn->query($query);
    }
}

// Re-connect using the standard function now that DB exists (for consistency)
require_once __DIR__ . '/includes/functions.php';
// Close raw connection
$conn->close();

// Use app connection
$conn = db();

echo "Checking database structure...\n";

// Modify 'type' enum in courses to include 'lab'
// Note: MODIFY COLUMN needs full definition.
$conn->query("ALTER TABLE courses MODIFY COLUMN type ENUM('video','article','lab') NOT NULL");
echo "Updated courses type enum.\n";

// Add content_body to courses if not exists
$result = $conn->query("SHOW COLUMNS FROM courses LIKE 'content_body'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE courses ADD COLUMN content_body TEXT AFTER description");
    echo "Added content_body to courses table.\n";
}

// Add thumbnail_url to courses if not exists (for card images)
$result = $conn->query("SHOW COLUMNS FROM courses LIKE 'thumbnail_url'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE courses ADD COLUMN thumbnail_url VARCHAR(255) AFTER content_body");
    echo "Added thumbnail_url to courses table.\n";
}

echo "Seeding courses and labs...\n";

$courses = [
    [
        'title' => 'Introduction to Cybersecurity',
        'description' => 'Learn the fundamentals of cybersecurity, including the CIA triad, threat actors, and common attack vectors.',
        'type' => 'article',
        'content_body' => '
            <h2>What is Cybersecurity?</h2>
            <p>Cybersecurity is the practice of protecting systems, networks, and programs from digital attacks. These cyberattacks are usually aimed at accessing, changing, or destroying sensitive information; extorting money from users; or interrupting normal business processes.</p>
            <h3>The CIA Triad</h3>
            <p>The CIA Triad is a model designed to guide policies for information security within an organization. The elements of the triad are considered the three most crucial components of security:</p>
            <ul>
                <li><strong>Confidentiality:</strong> Only authorized parties can access the information.</li>
                <li><strong>Integrity:</strong> Information is not altered by unauthorized parties.</li>
                <li><strong>Availability:</strong> Information is accessible when needed by authorized parties.</li>
            </ul>
        ',
        'content_url' => '',
        'thumbnail_url' => 'https://placehold.co/600x400?text=Intro+to+Cyber'
    ],
    [
        'title' => 'Password Cracking Basics',
        'description' => 'Understand how attackers crack passwords and how to secure them using hashing and salting.',
        'type' => 'video',
        'content_body' => 'In this lesson, we explore common password cracking techniques like Brute Force, Dictionary Attacks, and Rainbow Tables.',
        'content_url' => 'https://www.youtube.com/embed/7U-RbOKanYs', // Example video
        'thumbnail_url' => 'https://placehold.co/600x400?text=Password+Cracking'
    ],
    [
        'title' => 'Phishing Awareness',
        'description' => 'Learn to identify and avoid phishing scams, spear phishing, and social engineering attacks.',
        'type' => 'article',
        'content_body' => '
            <h2>Identifying Phishing</h2>
            <p>Phishing is a type of social engineering where an attacker sends a fraudulent message designed to trick a human victim into revealing sensitive information to the attacker or to deploy malicious software on the victim\'s infrastructure like ransomware.</p>
            <h3>Red Flags</h3>
            <ul>
                <li>Urgent language (e.g., "Account suspended!")</li>
                <li>Mismatched URLs</li>
                <li>Generic greetings</li>
                <li>Requests for personal info</li>
            </ul>
        ',
        'content_url' => '',
        'thumbnail_url' => 'https://placehold.co/600x400?text=Phishing'
    ],
    [
        'title' => 'Network Security 101',
        'description' => 'Basics of firewalls, VPNs, and secure network architecture.',
        'type' => 'article',
        'content_body' => '<p>Network security consists of the policies and practices adopted to prevent and monitor unauthorized access, misuse, modification, or denial of a computer network and network-accessible resources.</p>',
        'content_url' => '',
        'thumbnail_url' => 'https://placehold.co/600x400?text=Network+Sec'
    ],
    // Labs
    [
        'title' => 'Lab: Phishing Simulation',
        'description' => 'Can you spot the fake login page? Test your skills in this interactive simulation.',
        'type' => 'lab',
        'content_body' => 'Click "Launch Lab" to start the simulation.',
        'content_url' => '/labs/phishing.php',
        'thumbnail_url' => 'https://placehold.co/600x400?text=Lab:+Phishing'
    ],
    [
        'title' => 'Lab: Password Strength',
        'description' => 'Test password complexity and learn how quickly simple passwords can be cracked.',
        'type' => 'lab',
        'content_body' => 'Click "Launch Lab" to start the simulation.',
        'content_url' => '/labs/password.php',
        'thumbnail_url' => 'https://placehold.co/600x400?text=Lab:+Passwords'
    ]
];

$stmt = $conn->prepare("INSERT INTO courses (title, description, type, content_body, content_url, thumbnail_url, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

foreach ($courses as $course) {
    // Check if exists
    $check = $conn->prepare("SELECT id FROM courses WHERE title = ?");
    $check->bind_param("s", $course['title']);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $stmt->bind_param("ssssss", $course['title'], $course['description'], $course['type'], $course['content_body'], $course['content_url'], $course['thumbnail_url']);
        $stmt->execute();
        echo "Inserted: {$course['title']}\n";
    } else {
        echo "Skipped (exists): {$course['title']}\n";
    }
}

echo "Seeding badges...\n";

$badges = [
    [
        'name' => 'Cyber Initiate',
        'description' => 'Registered an account.',
        'icon_url' => 'https://placehold.co/100x100?text=Init'
    ],
    [
        'name' => 'Phishing Detective',
        'description' => 'Completed the Phishing Simulation Lab.',
        'icon_url' => 'https://placehold.co/100x100?text=Phishing'
    ],
    [
        'name' => 'Password Protector',
        'description' => 'Completed the Password Strength Lab.',
        'icon_url' => 'https://placehold.co/100x100?text=Pass'
    ]
];

$stmtBadge = $conn->prepare("INSERT INTO badges (name, description, icon_url) VALUES (?, ?, ?)");
foreach ($badges as $badge) {
    $check = $conn->prepare("SELECT id FROM badges WHERE name = ?");
    $check->bind_param("s", $badge['name']);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $stmtBadge->bind_param("sss", $badge['name'], $badge['description'], $badge['icon_url']);
        $stmtBadge->execute();
        echo "Inserted Badge: {$badge['name']}\n";
    } else {
        echo "Skipped Badge (exists): {$badge['name']}\n";
    }
}

echo "Seeding complete.\n";
