<?php
// Script to seed the database with free resources
require_once __DIR__ . '/includes/functions.php';

$conn = db();
echo "Connecting to database...\n";

// 1. Add new Badges
$badges = ['SQL Explorer', 'Script Kiddie', 'Linux Pro', 'Network Ninja'];
echo "Seeding badges...\n";
$stmt = $conn->prepare("INSERT IGNORE INTO badges (name, icon_url, description) VALUES (?, ?, ?)");
foreach ($badges as $badge) {
    $icon = strtolower(str_replace(' ', '_', $badge)) . '.png';
    $desc = "Awarded for completing $badge module.";
    $stmt->bind_param("sss", $badge, $icon, $desc);
    $stmt->execute();
}

// 2. Add New Content (Courses & Labs)
// Type: 'video', 'article', 'lab'
$new_content = [
    [
        'title' => 'Linux for Hackers',
        'description' => 'Master the Linux command line, essential for any cybersecurity career.',
        'type' => 'video',
        'content_body' => 'Comprehensive series covering Linux basics, file permissions, bash scripting, and networking.',
        'content_url' => 'https://www.youtube.com/embed/wBp0Rb-ZJak', // NetworkChuck
        'thumbnail_url' => 'https://img.youtube.com/vi/wBp0Rb-ZJak/maxresdefault.jpg'
    ],
    [
        'title' => 'Networking Fundamentals',
        'description' => 'Understand IP addresses, subnets, DNS, and the OSI model.',
        'type' => 'video',
        'content_body' => 'A visual guide to how the internet works.',
        'content_url' => 'https://www.youtube.com/embed/3b_T9FBCX9w', // PowerCert
        'thumbnail_url' => 'https://img.youtube.com/vi/3b_T9FBCX9w/maxresdefault.jpg'
    ],
    [
        'title' => 'OWASP Top 10',
        'description' => 'Learn about the 10 most critical web application security risks.',
        'type' => 'article',
        'content_body' => '<h3>The Standard for Web Security</h3><p>The OWASP Top 10 is a standard awareness document for developers and web application security. It represents a broad consensus about the most critical security risks to web applications.</p><ul><li>A01:2021-Broken Access Control</li><li>A02:2021-Cryptographic Failures</li><li>A03:2021-Injection</li></ul><p><a href="https://owasp.org/www-project-top-ten/" target="_blank">Read Full Documentation</a></p>',
        'content_url' => '',
        'thumbnail_url' => 'https://placehold.co/600x400?text=OWASP+Top+10'
    ],
    [
        'title' => 'Lab: SQL Injection',
        'description' => 'Hands-on practice exploiting SQL Injection vulnerabilities to bypass authentication.',
        'type' => 'lab',
        'content_body' => 'Internal Lab simulation.',
        'content_url' => '/labs/sqli.php',
        'thumbnail_url' => 'https://placehold.co/600x400?text=SQL+Injection'
    ],
    [
        'title' => 'Lab: Reflected XSS',
        'description' => 'Learn how to inject malicious scripts into web pages.',
        'type' => 'lab',
        'content_body' => 'Internal Lab simulation.',
        'content_url' => '/labs/xss.php',
        'thumbnail_url' => 'https://placehold.co/600x400?text=XSS+Lab'
    ],
    [
        'title' => 'TryHackMe: Complete Beginner',
        'description' => 'A guided path for complete beginners to start their cyber security journey.',
        'type' => 'lab',
        'content_body' => 'External Resource',
        'content_url' => 'https://tryhackme.com/path/outline/beginner',
        'thumbnail_url' => 'https://tryhackme-images.s3.amazonaws.com/room-icons/1654a962453472f8742d6274e797537c.png'
    ],
    [
        'title' => 'OverTheWire: Bandit',
        'description' => 'A wargame aimed at absolute beginners. It will teach you the basics needed to be able to play other wargames.',
        'type' => 'lab',
        'content_body' => 'External Resource. Connect via SSH.',
        'content_url' => 'https://overthewire.org/wargames/bandit/',
        'thumbnail_url' => 'https://placehold.co/600x400?text=Bandit+Wargame'
    ]
];

echo "Seeding courses and labs...\n";
$stmt = $conn->prepare("INSERT INTO courses (title, description, type, content_body, content_url, thumbnail_url, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE content_url=VALUES(content_url), thumbnail_url=VALUES(thumbnail_url)");

foreach ($new_content as $c) {
    // Check if exists first to avoid duplicate key errors if title isn't unique index (it usually isn't)
    // But for simplicity, we'll check by title
    $check = $conn->query("SELECT id FROM courses WHERE title = '" . $conn->real_escape_string($c['title']) . "'");
    if ($check->num_rows == 0) {
        $stmt->bind_param("ssssss", $c['title'], $c['description'], $c['type'], $c['content_body'], $c['content_url'], $c['thumbnail_url']);
        if ($stmt->execute()) {
            echo "Inserted: " . $c['title'] . "\n";
        } else {
            echo "Error inserting " . $c['title'] . ": " . $stmt->error . "\n";
        }
    } else {
        echo "Skipped (Exists): " . $c['title'] . "\n";
    }
}

echo "Done!\n";
