<aside class="sidebar">
    <div class="sidebar-header">
        <h2>CyberSecure Women</h2>
    </div>
    <nav class="sidebar-nav">
        <a href="/dashboard.php" class="<?= $_SERVER['REQUEST_URI'] === '/dashboard.php' || $_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="/profile.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/profile.php') !== false ? 'active' : '' ?>">My Profile</a>
        <a href="/courses/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/courses/') !== false ? 'active' : '' ?>">Tutorials</a>
        <a href="/labs/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/labs/') !== false ? 'active' : '' ?>">Labs</a>
        <a href="/forums/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/forums/') !== false ? 'active' : '' ?>">Forum</a>
        <a href="/auth/logout.php" class="logout">Logout</a>
    </nav>
</aside>
