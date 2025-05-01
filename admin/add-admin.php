<?php 
include './menu.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $avatar = trim($_POST['avatar']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // If no errors, proceed with database operation
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn = new mysqli('localhost', 'root', '', 'mock-food');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if username already exists
        $check_sql = "SELECT id FROM tbl_admin WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "Username already exists";
        } else {
            // Insert new admin
            $insert_sql = "INSERT INTO tbl_admin (username, full_name, password, avatar) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $username, $full_name, $hashed_password, $avatar);

            if ($insert_stmt->execute()) {
                $_SESSION['success'] = "Admin created successfully!";
                header("Location: admin.php");
                exit();
            } else {
                $errors[] = "Error creating admin: " . $insert_stmt->error;
            }
            
            $insert_stmt->close();
        }
        
        $check_stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin | Restaurant Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="form-header">
            <h2><i class="fas fa-user-plus"></i> Add New Admin</h2>
            <p>Fill in the details below to add a new admin to the system.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" class="admin-form">
                <!-- Avatar Upload -->
                <div class="avatar-upload">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4361ee&color=fff"
                        alt="Admin Avatar"
                        class="avatar-preview"
                        id="avatar-preview">
                    <label for="avatar-upload" class="avatar-upload-btn">
                        <i class="fas fa-camera"></i> Choose Avatar
                    </label>
                    <input type="file" id="avatar-upload" accept="image/*">
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <div class="input-icon">
                        <i class="fas fa-at"></i>
                        <input type="text" id="username" name="username" class="form-control" style="padding: .5rem 2.5rem;"
                            placeholder="Enter username" required
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-id-card"></i> Full Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user-tag"></i>
                        <input type="text" id="full_name" name="full_name" class="form-control" style="padding: .5rem 2.5rem;"
                            placeholder="Enter full name" required
                            value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>
                </div>

                <!-- Avatar URL -->
                <div class="form-group">
                    <label for="avatar"><i class="fas fa-link"></i> Avatar URL</label>
                    <div class="input-icon">
                        <i class="fas fa-user-circle"></i>
                        <input type="url" id="avatar" name="avatar" class="form-control" style="padding: .5rem 2.5rem;"
                            placeholder="https://example.com/avatar.jpg"
                            value="<?php echo isset($_POST['avatar']) ? htmlspecialchars($_POST['avatar']) : ''; ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" id="password" name="password" class="form-control" style="padding: 0.5rem 2.5rem;"
                            placeholder="Enter password" required>
                        <i class="fas fa-eye password-toggle" id="toggle-password"></i>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter">
                            <div class="strength-bar" id="strength-bar"></div>
                        </div>
                        <div class="strength-text" id="strength-text">Password strength</div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" style="padding: .5rem 2.5rem;"
                            placeholder="Confirm password" required>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="admin.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script>
        // Avatar Preview
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('avatar-preview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Password Toggle
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Password Strength Meter
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            // Calculate strength
            let score = 0;
            
            // Length
            if (password.length > 0) score += 10;
            if (password.length >= 4) score += 10;
            if (password.length >= 8) score += 20;
            if (password.length >= 12) score += 10;

            // Complexity
            if (/[A-Z]/.test(password)) score += 10;
            if (/[0-9]/.test(password)) score += 10;
            if (/[^A-Za-z0-9]/.test(password)) score += 10;

            // Cap at 100
            score = Math.min(100, score);

            // Update UI
            strengthBar.style.width = score + '%';
            
            // Determine level and colors
            let color, text;
            if (score >= 80) {
                color = '#4CAF50';
                text = 'Very Strong';
            } else if (score >= 60) {
                color = '#8BC34A';
                text = 'Strong';
            } else if (score >= 40) {
                color = '#FFC107';
                text = 'Good';
            } else if (score >= 20) {
                color = '#FF9800';
                text = 'Weak';
            } else {
                color = '#f44336';
                text = 'Very Weak';
            }

            strengthBar.style.backgroundColor = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        });
    </script>
</body>
</html>