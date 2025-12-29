<aside class="sidebar">
    <div class="sidebar-header">
        <h2>CyberSecure Women</h2>
        <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    <nav class="sidebar-nav" id="sidebar-nav">
        <a href="/dashboard.php" class="<?= $_SERVER['REQUEST_URI'] === '/dashboard.php' || $_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="/profile.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/profile.php') !== false ? 'active' : '' ?>">My Profile</a>
        <a href="/courses/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/courses/') !== false ? 'active' : '' ?>">Tutorials</a>
        <a href="/labs/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/labs/') !== false ? 'active' : '' ?>">Labs</a>
        <a href="/forums/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/forums/') !== false ? 'active' : '' ?>">Forum</a>
        <a href="/auth/logout.php" class="logout">Logout</a>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebar-toggle');
            const nav = document.getElementById('sidebar-nav');
            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    nav.classList.toggle('active');
                });
            }
        });
    </script>
</aside>
