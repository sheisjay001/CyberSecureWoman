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
        <a href="/dashboard.php" class="<?= $_SERVER['REQUEST_URI'] === '/dashboard.php' || $_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="/profile.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/profile.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-user"></i> My Profile
        </a>
        <a href="/courses/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/courses/') !== false ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i> Tutorials
        </a>
        <a href="/labs/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/labs/') !== false ? 'active' : '' ?>">
            <i class="fas fa-flask"></i> Labs
        </a>
        <a href="/forums/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], '/forums/') !== false ? 'active' : '' ?>">
            <i class="fas fa-comments"></i> Forum
        </a>
        <a href="/auth/logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebar-toggle');
            const nav = document.getElementById('sidebar-nav');
            
            if (toggle && nav) {
                // Initial state check - ensure it's hidden on load for mobile
                if (window.innerWidth <= 768) {
                    nav.classList.remove('active');
                }

                toggle.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent event bubbling
                    nav.classList.toggle('active');
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInside = nav.contains(event.target) || toggle.contains(event.target);
                    if (!isClickInside && nav.classList.contains('active') && window.innerWidth <= 768) {
                        nav.classList.remove('active');
                    }
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        nav.classList.remove('active');
                        nav.style.display = ''; // Reset display property
                    }
                });
            }
        });
    </script>
</aside>
