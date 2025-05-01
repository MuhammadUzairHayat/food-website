<?php
session_start();
include '../config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Food Order System</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
    </style>
</head>

<body>
    <?php
    // Display messages if they exist
    if (isset($_SESSION['login_success'])) {
        echo '<div class="success-alert"><i class="fas fa-check-circle"></i> ' . $_SESSION['login_success'] . '</div>';
        unset($_SESSION['login_success']);
    }
    if (isset($_SESSION['login_error'])) {
        echo '<div class="error-alert"><i class="fas fa-exclamation-circle"></i> ' . $_SESSION['login_error'] . '</div>';
        unset($_SESSION['login_error']);
    }
    ?>
    <div class="login-container">
        <div class="login-left">
            <h1>Welcome Back!</h1>
            <p>Sign in to access your admin dashboard and manage your food ordering system with ease.</p>

            <div class="features">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure authentication system</span>
                </div>
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <span>Fast and responsive interface</span>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <span>Real-time analytics dashboard</span>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="logo">
                <!-- Replace with your logo -->
                <img style="width: 100px; height: 100px; object-fit: cover" src="https://media.istockphoto.com/id/2203688325/video/chef-in-cook-hat-and-baker-cook-fork-spoon-and-leaves-graphic-animation-colored-transparent.avif?s=640x640&k=20&c=SsywiUO3TYpYcoz1gez4GV3FpcnU3ibH9pbDD8wgBvI=" alt="Food Order System">
                <p>mock-food</p>
            </div>

            <form class="login-form" action="login-check.php" method="POST">
                <h2>Admin Login</h2>

                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <div class="options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot password?</a>
                    </div>
                </div>

                <button type="submit" name="submit" class="login-btn">Login</button>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Request access</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add animation to input fields on focus
        document.querySelectorAll('.input-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentNode.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>