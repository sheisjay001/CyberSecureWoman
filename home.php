<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberSecure Women - Empowering Women in Cybersecurity</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>CyberSecure Women</h1>
                <nav>
                    <a href="/auth/login.php">Login</a>
                    <a href="/auth/register.php" class="btn">Register</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container" style="text-align: center; padding: 60px 20px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px; color: var(--primary);">Master Cybersecurity with Confidence</h2>
                <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto 30px; line-height: 1.6;">
                    Join a supportive community designed to empower women in cybersecurity. 
                    Learn through interactive courses, hands-on labs, and connect with peers to advance your career.
                </p>
                <div class="cta" style="justify-content: center;">
                    <a href="/auth/register.php" class="btn" style="font-size: 1.1rem; padding: 12px 24px;">Get Started</a>
                    <a href="/auth/login.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 12px 24px;">Explore Courses</a>
                </div>
            </div>
        </section>

        <section class="container">
            <h3 style="text-align: center; font-size: 2rem; margin-bottom: 40px;">Everything You Need to Succeed</h3>
            <div class="features">
                <div class="feature">
                    <i class="fas fa-graduation-cap" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <h3>Interactive Courses</h3>
                    <p>Comprehensive tutorials covering everything from basics to advanced security concepts.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-flask" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <h3>Hands-on Labs</h3>
                    <p>Practice what you learn in safe, simulated environments like Phishing detection and Password security.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-users" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <h3>Community Forum</h3>
                    <p>Connect, share knowledge, and get help from a supportive community of learners and experts.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <h3>Progress Tracking</h3>
                    <p>Earn badges, track your achievements, and visualize your growth as you master new skills.</p>
                </div>
            </div>
        </section>
        
        <section class="container" style="margin-top: 60px; margin-bottom: 60px; text-align: center;">
            <div style="background: var(--card); padding: 40px; border-radius: 12px; border: 1px solid #232647;">
                <h3>Ready to start your journey?</h3>
                <p style="margin-bottom: 20px;">Join thousands of women making their mark in cybersecurity.</p>
                <a href="/auth/register.php" class="btn">Create Free Account</a>
            </div>
        </section>
    </main>

    <footer style="background: #0d1020; padding: 40px 0; margin-top: auto; border-top: 1px solid #232647;">
        <div class="container" style="text-align: center; color: var(--muted);">
            <p>&copy; <?= date('Y') ?> CyberSecure Women. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
