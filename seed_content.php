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
        'title' => 'Introduction to Cybersecurity',
        'description' => 'Learn the fundamentals of cybersecurity, including the CIA triad, threat actors, and common attack vectors.',
        'type' => 'video',
        'content_body' => '
            <h2>What is Cybersecurity?</h2>
            <p>Cybersecurity is the practice of protecting systems, networks, and programs from digital attacks.</p>
            <h3>The CIA Triad</h3>
            <ul>
                <li><strong>Confidentiality</strong></li>
                <li><strong>Integrity</strong></li>
                <li><strong>Availability</strong></li>
            </ul>
        ',
        'content_url' => 'https://www.youtube.com/embed/nzZkKoREEGo', // Simplilearn
        'thumbnail_url' => 'https://img.youtube.com/vi/nzZkKoREEGo/maxresdefault.jpg'
    ],
    [
        'title' => 'Password Cracking Basics',
        'description' => 'Understand how attackers crack passwords and how to secure them using hashing and salting.',
        'type' => 'video',
        'content_body' => 'In this lesson, we explore common password cracking techniques like Brute Force, Dictionary Attacks, and Rainbow Tables.',
        'content_url' => 'https://www.youtube.com/embed/7U-RbOKanYs', // Computerphile
        'thumbnail_url' => 'https://img.youtube.com/vi/7U-RbOKanYs/maxresdefault.jpg'
    ],
    [
        'title' => 'Linux for Hackers',
        'description' => 'Master the Linux command line, essential for any cybersecurity career.',
        'type' => 'video',
        'content_body' => 'Comprehensive series covering Linux basics, file permissions, bash scripting, and networking.',
        'content_url' => 'https://www.youtube.com/embed/lZAoFs75_cs',
        'thumbnail_url' => 'https://img.youtube.com/vi/lZAoFs75_cs/maxresdefault.jpg'
    ],
    [
        'title' => 'Networking Fundamentals',
        'description' => 'Understand IP addresses, subnets, DNS, and the OSI model.',
        'type' => 'video',
        'content_body' => 'A visual guide to how the internet works.',
        'content_url' => 'https://www.youtube.com/embed/cNwEVYkx2Kk',
        'thumbnail_url' => 'https://img.youtube.com/vi/cNwEVYkx2Kk/maxresdefault.jpg'
    ],
    [
        'title' => 'OWASP Top 10',
        'description' => 'Learn about the 10 most critical web application security risks.',
        'type' => 'video',
        'content_body' => '<h3>The Standard for Web Security</h3><p>The OWASP Top 10 is a standard awareness document for developers and web application security. It represents a broad consensus about the most critical security risks to web applications.</p><ul><li>A01:2021-Broken Access Control</li><li>A02:2021-Cryptographic Failures</li><li>A03:2021-Injection</li></ul><p><a href="https://owasp.org/www-project-top-ten/" target="_blank">Read Full Documentation</a></p>',
        'content_url' => 'https://www.youtube.com/embed/Zc5N3C51G6A',
        'thumbnail_url' => 'https://img.youtube.com/vi/Zc5N3C51G6A/maxresdefault.jpg'
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
    // Check if exists
    $check = $conn->query("SELECT id FROM courses WHERE title = '" . $conn->real_escape_string($c['title']) . "'");
    if ($check->num_rows == 0) {
        $stmt->bind_param("ssssss", $c['title'], $c['description'], $c['type'], $c['content_body'], $c['content_url'], $c['thumbnail_url']);
        if ($stmt->execute()) {
            echo "Inserted: " . $c['title'] . "\n";
        } else {
            echo "Error inserting " . $c['title'] . ": " . $stmt->error . "\n";
        }
    } else {
        // Update existing record
        $id = $check->fetch_assoc()['id'];
        echo "Updating ID: $id with URL: " . $c['content_url'] . "\n";
        $update = $conn->prepare("UPDATE courses SET description=?, type=?, content_body=?, content_url=?, thumbnail_url=? WHERE id=?");
        $update->bind_param("sssssi", $c['description'], $c['type'], $c['content_body'], $c['content_url'], $c['thumbnail_url'], $id);
        if ($update->execute()) {
            echo "Updated: " . $c['title'] . "\n";
        } else {
            echo "Error updating " . $c['title'] . ": " . $update->error . "\n";
        }
    }
}

echo "Backfilling video thumbnails...\n";
$result = $conn->query("SELECT id, content_url FROM courses WHERE type='video' AND (thumbnail_url IS NULL OR thumbnail_url='')");
while ($row = $result->fetch_assoc()) {
    $url = $row['content_url'];
    $thumb = '';
    if (preg_match('/https?:\/\/www\.youtube\.com\/embed\/([A-Za-z0-9_-]+)/', $url, $m)) {
        $vid = $m[1];
        $thumb = 'https://img.youtube.com/vi/' . $vid . '/maxresdefault.jpg';
    } else {
        $thumb = 'https://placehold.co/600x400?text=Video';
    }
    $stmt2 = $conn->prepare("UPDATE courses SET thumbnail_url=? WHERE id=?");
    $stmt2->bind_param('si', $thumb, $row['id']);
    $stmt2->execute();
    echo "Thumbnail set for ID: " . $row['id'] . "\n";
}
echo "Done!\n";
